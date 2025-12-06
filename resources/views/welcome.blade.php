<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lumin Park Housing - Rumah Impian Anda</title>
        @php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
        @endphp

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
        <script>
            let scene, camera, renderer, house, currentModel, controls;

            function init3DViewer(modelPath) {
                const container = document.getElementById('viewer3d');
                const loading = document.getElementById('viewer-loading');

                // Clear previous content
                while (container.firstChild) {
                    container.removeChild(container.firstChild);
                }

                // Show loading initially
                if (loading) {
                    loading.style.display = 'flex';
                    container.appendChild(loading);
                }

                scene = new THREE.Scene();
                scene.background = new THREE.Color(0xf0f4f8);

                camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
                camera.position.set(3, 2, 3);

                renderer = new THREE.WebGLRenderer({ antialias: true });
                renderer.setSize(container.clientWidth, container.clientHeight);
                renderer.shadowMap.enabled = true;
                renderer.shadowMap.type = THREE.PCFSoftShadowMap;
                container.appendChild(renderer.domElement);

                // Add OrbitControls
                controls = new THREE.OrbitControls(camera, renderer.domElement);
                controls.enableDamping = true;
                controls.dampingFactor = 0.05;
                controls.enableZoom = true;
                controls.enablePan = true;
                controls.enableRotate = true;
                controls.autoRotate = true;
                controls.autoRotateSpeed = 2.0;

                // Lighting
                const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
                scene.add(ambientLight);

                const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
                directionalLight.position.set(10, 10, 5);
                directionalLight.castShadow = true;
                directionalLight.shadow.mapSize.width = 2048;
                directionalLight.shadow.mapSize.height = 2048;
                scene.add(directionalLight);

                const pointLight = new THREE.PointLight(0xffffff, 0.5);
                pointLight.position.set(-10, 10, -10);
                scene.add(pointLight);

                // Ground
                const groundGeometry = new THREE.PlaneGeometry(10, 10);
                const groundMaterial = new THREE.MeshStandardMaterial({
                    color: 0xcccccc,
                    transparent: true,
                    opacity: 0.3
                });
                const ground = new THREE.Mesh(groundGeometry, groundMaterial);
                ground.rotation.x = -Math.PI / 2;
                ground.receiveShadow = true;
                scene.add(ground);

                // Load GLB model
                const loader = new THREE.GLTFLoader();
                loader.load(
                    modelPath,
                    function (gltf) {
                        currentModel = gltf.scene;

                        // Configure shadows
                        currentModel.traverse(function (child) {
                            if (child.isMesh) {
                                child.castShadow = true;
                                child.receiveShadow = true;
                            }
                        });

                        // Scale and position the model
                        const box = new THREE.Box3().setFromObject(currentModel);
                        const center = box.getCenter(new THREE.Vector3());
                        const size = box.getSize(new THREE.Vector3());

                        // Scale model to fit in viewer
                        const maxDim = Math.max(size.x, size.y, size.z);
                        const scale = 2 / maxDim;
                        currentModel.scale.setScalar(scale);

                        // Center the model
                        currentModel.position.sub(center.multiplyScalar(scale));
                        currentModel.position.y = 0;

                        scene.add(currentModel);

                        // Hide loading
                        if (loading) loading.style.display = 'none';

                        // Start animation loop
                        animate();
                    },
                    function (progress) {
                        console.log('Loading progress: ', (progress.loaded / progress.total * 100) + '%');
                    },
                    function (error) {
                        console.error('Error loading model: ', error);
                        if (loading) {
                            loading.innerHTML = '<div class="text-red-500">Error loading 3D model</div>';
                        }
                    }
                );
            }

            function animate() {
                requestAnimationFrame(animate);
                if (controls) {
                    controls.update();
                }
                if (renderer && scene && camera) {
                    renderer.render(scene, camera);
                }
            }

            function openModal(property) {
                const modal = document.getElementById('propertyModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                document.getElementById('modalName').textContent = property.name;
                document.getElementById('modalPrice').textContent = property.price;
                document.getElementById('modalBedrooms').textContent = 'N/A';
                document.getElementById('modalBathrooms').textContent = 'N/A';
                document.getElementById('modalArea').textContent = 'N/A';

                const statusElement = document.getElementById('modalStatus');
                if (statusElement) {
                    statusElement.textContent = property.status === 'available' ? 'Tersedia' : 'Tidak Tersedia';
                    statusElement.className = property.status === 'available' ? 'px-4 py-1 rounded-full text-sm font-semibold bg-green-500 text-white' : 'px-4 py-1 rounded-full text-sm font-semibold bg-yellow-500 text-white';
                }

                document.body.style.overflow = 'hidden';

                // Load 3D model
                setTimeout(() => {
                    if (property.model3d) {
                        init3DViewer(property.model3d);
                    } else {
                        // Tampilkan pesan bahwa model tidak tersedia
                        const container = document.getElementById('viewer3d');
                        const loading = document.getElementById('viewer-loading');
                        if (loading) loading.style.display = 'none';
                        container.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500">3D Model tidak tersedia</div>';
                    }
                }, 100);
            }

            function closeModal() {
                const modal = document.getElementById('propertyModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';

                // Clean up 3D viewer
                if (controls) {
                    controls.dispose();
                    controls = null;
                }

                if (currentModel) {
                    scene.remove(currentModel);
                    currentModel = null;
                }

                if (renderer) {
                    renderer.dispose();
                }
            }

            function closeModalOnBackdrop(event) {
                if (event.target.id === 'propertyModal') {
                    closeModal();
                }
            }

            function scheduleVisit() {
                const propertyName = document.getElementById('modalName').textContent;
                const message = `Halo, saya tertarik untuk menjadwalkan kunjungan ke ${propertyName}`;
                window.open(`https://wa.me/6281234567890?text=${encodeURIComponent(message)}`, '_blank');
            }

            // Make functions global so they can be called from HTML
            window.openModal = openModal;
            window.closeModal = closeModal;
            window.closeModalOnBackdrop = closeModalOnBackdrop;
            window.scheduleVisit = scheduleVisit;
        </script>
        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>

    <body class="font-sans antialiased m-0">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <h1
                            class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                            Lumin Park Housing
                        </h1>
                    </div>
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#home" class="text-gray-700 hover:text-blue-600 transition">Beranda</a>
                        <a href="#catalog" class="text-gray-700 hover:text-blue-600 transition">Katalog</a>
                        <a href="#about" class="text-gray-700 hover:text-blue-600 transition">Tentang</a>
                        <a href="#contact" class="text-gray-700 hover:text-blue-600 transition">Kontak</a>
                    </div>
                    <button class="md:hidden text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section id="home" class="pt-24 pb-16 bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-5xl font-bold text-gray-900 mb-6 leading-tight">
                            Temukan Rumah <span class="text-blue-600">Impian</span> Anda
                        </h2>
                        <p class="text-xl text-gray-600 mb-8">
                            Koleksi eksklusif hunian modern dengan desain elegan dan fasilitas premium di lokasi
                            strategis.
                        </p>
                        <div class="flex space-x-4">
                            <a href="#catalog"
                                class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg hover:shadow-xl">
                                Lihat Katalog
                            </a>
                            <a href="#contact"
                                class="bg-white text-blue-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition border-2 border-blue-600">
                                Hubungi Kami
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=800" alt="Modern House"
                            class="rounded-2xl shadow-2xl">
                        <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-xl shadow-xl">
                            <div class="flex items-center space-x-4">
                                <div class="bg-blue-100 p-3 rounded-lg">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900">500+</p>
                                    <p class="text-gray-600">Keluarga Bahagia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Features Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center p-6">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Terpercaya</h3>
                        <p class="text-gray-600">Developer berpengalaman dengan track record terbaik</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Desain Modern</h3>
                        <p class="text-gray-600">Arsitektur contemporary dengan material premium</p>
                    </div>
                    <div class="text-center p-6">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Lokasi Strategis</h3>
                        <p class="text-gray-600">Dekat dengan pusat kota dan fasilitas umum</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Catalog Section -->
        <section id="catalog" class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Katalog Properti</h2>
                    <p class="text-xl text-gray-600">Pilihan rumah eksklusif dengan berbagai tipe dan desain</p>
                </div>

                <!-- Filter -->
                <!-- Filter berdasarkan kategorig -->
                <div class="flex flex-wrap gap-4 mb-8 justify-center">
                    <button onclick="filterProperties('all')" class="px-4 py-2 rounded-lg border">Semua</button>

                
                    @foreach($katalogs->pluck('category')->unique() as $category)
                        <button onclick="filterProperties('{{ $category }}')" class="px-4 py-2 rounded-lg border">
                            {{ ucfirst($category) }}
                        </button>
                    @endforeach
                </div>


                <!-- Property Grid --> 
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

                    @forelse($katalogs as $katalog)
                                            @php
    // Create property object for JavaScript
    $property = [
        'id' => $katalog->id,
        'name' => $katalog->name,
        'category' => $katalog->category,
        'price' => 'Rp ' . number_format((float) $katalog->price, 0, ',', '.'),
        'status' => $katalog->is_available ? 'available' : 'reserved',
        'image' => $katalog->image ? Storage::url($katalog->image) : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600',
        'model3d' => $katalog->model_3d ? Storage::url($katalog->model_3d) : '/storage/models/model-' . strtolower(str_replace(' ', '-', $katalog->type)) . '.glb',
        'description' => $katalog->description
    ];
                                            @endphp

                                            <div data-type="{{ $katalog->type }}" data-category="{{ $katalog->category }}"
                                                class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 cursor-pointer transform hover:-translate-y-2"
                                                onclick="openModal({{ json_encode($property) }})">
                                                <div class="relative">
                                                    <img src="{{ $property['image'] }}" alt="{{ $katalog->name }}"
                                                        class="w-full h-64 object-cover">
                                                    <div class="absolute top-4 right-4">
                                                        <span class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-semibold">
                                                            {{ $katalog->type }}
                                                        </span>
                                                    </div>
                                                    <div class="absolute top-4 left-4">
                                                        @if($katalog->is_available)
                                                            <span
                                                                class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold">Tersedia</span>
                                                        @else
                                                            <span
                                                                class="bg-yellow-600 text-white px-3 py-1 rounded-full text-xs font-semibold">Tidak Tersedia</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="p-6">
                                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $katalog->name }}</h3>
                                                    <p class="text-2xl font-bold text-blue-600 mb-4">{{ $property['price'] }}</p>
                                                    @if($katalog->description)
                                                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                                            {{ Str::limit($katalog->description, 100) }}
                                                        </p>
                                                    @endif
                                                    <div class="flex items-center space-x-4 text-gray-600 text-sm">
                                                        <!-- Additional property details can be added here -->
                                                    </div>
                                                </div>
                                            </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <div class="text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                    </path>
                                </svg>
                                <h3 class="text-xl font-semibold mb-2">Belum Ada Properti</h3>
                                <p>Saat ini belum ada properti yang tersedia.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-6">Tentang Lumin Park Housing</h2>
                        <p class="text-lg text-gray-600 mb-4">
                            Kami adalah pengembang properti terpercaya dengan pengalaman lebih dari 15 tahun dalam
                            menciptakan hunian berkualitas tinggi.
                        </p>
                        <p class="text-lg text-gray-600 mb-6">
                            Setiap properti dirancang dengan cermat untuk memberikan kenyamanan maksimal, keamanan
                            terjamin,
                            dan nilai investasi jangka panjang bagi keluarga Anda.
                        </p>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-3xl font-bold text-blue-600">15+</p>
                                <p class="text-gray-600">Tahun Pengalaman</p>
                            </div>
                            <div>
                                <p class="text-3xl font-bold text-blue-600">50+</p>
                                <p class="text-gray-600">Proyek Selesai</p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800" alt="About Us"
                            class="rounded-2xl shadow-2xl">
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact"
            class="py-20 bg-gradient-to-br from-indigo-600 to-cyan-400 text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <h2 class="text-4xl font-bold mb-4">Siap Menemukan Rumah Impian?</h2>
                <p class="text-xl mb-8 opacity-90">Hubungi kami untuk konsultasi gratis dan dapatkan penawaran terbaik
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="https://wa.me/6281234567890"
                        class="bg-green-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-600 transition shadow-lg">
                        💬 WhatsApp
                    </a>
                    <a href="mailto:info@luminpark.com"
                        class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white/20 transition">
                        ✉️ Email
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                        <h3
                            class="text-2xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">
                            Lumin Park Housing</h3>
                        <p class="text-gray-400">Hunian modern untuk keluarga masa depan</p>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Menu</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li><a href="#home" class="hover:text-white transition">Beranda</a></li>
                            <li><a href="#catalog" class="hover:text-white transition">Katalog</a></li>
                            <li><a href="#about" class="hover:text-white transition">Tentang</a></li>
                            <li><a href="#contact" class="hover:text-white transition">Kontak</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Kontak</h4>
                        <ul class="space-y-2 text-gray-400">
                            <li>Email: info@luminpark.com</li>
                            <li>Phone: +62 812-3456-7890</li>
                            <li>WhatsApp: +62 812-3456-7890</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-4">Alamat</h4>
                        <p class="text-gray-400">Jl. Contoh No. 123<br>Bengkulu, Indonesia</p>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; 2024 Lumin Park Housing. All rights reserved.</p>
                </div>
            </div>
        </footer>

        <!-- Modal -->
        <div id="propertyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4"
            onclick="closeModalOnBackdrop(event)">
            <div class="bg-white rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto"
                onclick="event.stopPropagation()">
                <div class="relative">
                    <button onclick="closeModal()"
                        class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition z-10">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>

                    <!-- 3D Viewer Container -->
                    <div id="viewer3d" class="w-full h-96 bg-gradient-to-br from-gray-100 to-gray-200 relative">
                        <div id="viewer-loading" class="absolute inset-0 flex items-center justify-center z-10">
                            <div class="text-center">
                                <div
                                    class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4">
                                </div>
                                <p class="text-gray-600">Loading 3D Model...</p>
                            </div>
                        </div>
                        <!-- 3D Controls Info -->
                        <div class="absolute bottom-4 left-4 bg-black/70 text-white text-xs px-3 py-2 rounded-lg z-10">
                            🖱️ Drag to rotate • 🔍 Scroll to zoom
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="mb-6">
                            <span id="modalStatus"
                                class="px-4 py-1 rounded-full text-sm font-semibold inline-block mb-4"></span>
                            <h2 id="modalName" class="text-3xl font-bold text-gray-900 mb-2"></h2>
                            <p id="modalPrice" class="text-4xl font-bold text-blue-600"></p>
                        </div>

                        <div class="grid md:grid-cols-3 gap-6 mb-8">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                        </path>
                                    </svg>
                                    <span class="text-gray-600">Kamar Tidur</span>
                                </div>
                                <p id="modalBedrooms" class="text-2xl font-bold text-gray-900"></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                    </svg>
                                    <span class="text-gray-600">Kamar Mandi</span>
                                </div>
                                <p id="modalBathrooms" class="text-2xl font-bold text-gray-900"></p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4">
                                        </path>
                                    </svg>
                                    <span class="text-gray-600">Luas Tanah</span>
                                </div>
                                <p id="modalArea" class="text-2xl font-bold text-gray-900"></p>
                            </div>
                        </div>

                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Spesifikasi</h3>
                            <ul class="grid md:grid-cols-2 gap-3 text-gray-600">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Taman Belakang
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Smart Home System
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Keamanan 24 Jam
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    Kolam Renang Komunal
                                </li>
                            </ul>
                        </div>

                        <div class="flex gap-4">
                            <a href="https://wa.me/6281234567890"
                                class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition text-center">
                                Hubungi Marketing
                            </a>
                            <button onclick="scheduleVisit()"
                                class="flex-1 bg-white text-blue-600 py-3 rounded-lg font-semibold hover:bg-gray-50 transition border-2 border-blue-600">
                                Jadwalkan Kunjungan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Smooth scroll
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Close modal on ESC key
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    if (window.closeModal) {
                        window.closeModal();
                    }
                }
            });

            // Filter properties functionality
            function filterProperties(category) {
                    const cards = document.querySelectorAll('[data-category]');
                    const buttons = document.querySelectorAll('#catalog button');

                    cards.forEach(card => {
                        const cardCategory = card.getAttribute('data-category');
                        card.style.display = (category === 'all' || cardCategory === category) ? 'block' : 'none';
                    });

                    buttons.forEach(btn => btn.classList.remove('bg-blue-600', 'text-white'));
                    const activeBtn = Array.from(buttons).find(b => b.innerText.toLowerCase() === (category === 'all' ? 'semua' : category.toLowerCase()));
                    if (activeBtn) activeBtn.classList.add('bg-blue-600', 'text-white');
                }



        </script>
    </body>

</html>