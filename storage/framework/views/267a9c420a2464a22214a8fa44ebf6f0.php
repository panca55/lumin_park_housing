<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lumin Park Housing - Rumah Impian Anda</title>
        <?php
            use Illuminate\Support\Facades\Storage;
            use Illuminate\Support\Str;
        ?>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Three.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/DRACOLoader.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

        <!-- Pannellum for 360 Panorama -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css" />
        <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

            * {
                font-family: 'Inter', sans-serif;
            }

            .property-card {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                opacity: 0;
                animation: fadeInUp 0.6s ease-out forwards;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .property-card:nth-child(1) {
                animation-delay: 0.1s;
            }

            .property-card:nth-child(2) {
                animation-delay: 0.2s;
            }

            .property-card:nth-child(3) {
                animation-delay: 0.3s;
            }

            .property-card:nth-child(4) {
                animation-delay: 0.4s;
            }

            .property-card:nth-child(5) {
                animation-delay: 0.5s;
            }

            .property-card:nth-child(6) {
                animation-delay: 0.6s;
            }

            .property-card:hover {
                transform: translateY(-12px) scale(1.02);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }

            .property-image {
                transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .property-card:hover .property-image {
                transform: scale(1.15) rotate(2deg);
            }

            .filter-btn {
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .filter-btn::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                border-radius: 50%;
                background: rgba(102, 126, 234, 0.3);
                transform: translate(-50%, -50%);
                transition: width 0.6s, height 0.6s;
            }

            .filter-btn:hover::before {
                width: 300px;
                height: 300px;
            }

            .filter-btn.active {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
                transform: translateY(-2px);
            }

            .gradient-text {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .badge-available {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
                backdrop-filter: blur(10px);
            }

            .badge-reserved {
                background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
                box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
                backdrop-filter: blur(10px);
            }

            .badge-type {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
                backdrop-filter: blur(10px);
            }

            .search-input:focus {
                box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
                transform: translateY(-2px);
            }

            .filter-container {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .overlay-gradient {
                background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.2) 50%, rgba(0, 0, 0, 0) 100%);
            }

            .price-tag {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .card-shine {
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                transition: left 0.7s;
            }

            .property-card:hover .card-shine {
                left: 100%;
            }

            @keyframes pulse {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }

            .pulse-animation {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
        </style>
        <script>
            let scene, camera, renderer, house, currentModel, controls;

            function init3DViewer(modelPath) {
                const container = document.getElementById('viewer3d');
                const loading = document.getElementById('viewer-loading');

                if (!container) return;

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
                // Set background color based on theme
                const updateSceneBackground = () => {
                    const isDark = document.documentElement.classList.contains('dark');
                    scene.background = new THREE.Color(isDark ? 0x1f2937 : 0xf0f4f8);
                };
                updateSceneBackground();

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

                // Configure DRACO loader for compressed models
                const dracoLoader = new THREE.DRACOLoader();
                dracoLoader.setDecoderPath('https://www.gstatic.com/draco/versioned/decoders/1.5.6/');
                loader.setDRACOLoader(dracoLoader);

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
                        console.error('Error loading 3D model: ', error);
                        if (loading) {
                            loading.innerHTML = '<div class="text-red-500 text-center"><div class="text-lg font-semibold mb-2">❌ Error Loading 3D Model</div><div class="text-sm">The 3D model could not be loaded. Please try refreshing the page.</div></div>';
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

            // Listen for theme changes to update 3D scene background
            const themeObserver = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        const isDark = document.documentElement.classList.contains('dark');
                        if (scene) {
                            scene.background = new THREE.Color(isDark ? 0x1f2937 : 0xf0f4f8);
                        }
                    }
                });
            });
            themeObserver.observe(document.documentElement, {
                attributes: true,
                attributeFilter: ['class']
            });

            let currentPropertyData = null;
            let gambarScrollPosition = 0;
            let panoramaScrollPosition = 0;
            let allPanoramas = [];

            function openModal(property) {
                const modal = document.getElementById('propertyModal');
                if (!modal) return;

                currentPropertyData = property;
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                // Update hero image
                const heroImage = document.getElementById('modalHeroImage');
                if (heroImage && property.gambar_produks && property.gambar_produks.length > 0) {
                    heroImage.src = property.gambar_produks[0];
                } else if (heroImage) {
                    heroImage.src = '/images/default-property.jpg';
                }

                // Update status
                const statusElement = document.getElementById('modalStatus');
                if (statusElement) {
                    if (property.status === 'available') {
                        statusElement.textContent = '✓ Tersedia';
                        statusElement.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white shadow-xl bg-gradient-to-r from-green-500 to-emerald-600';
                    } else {
                        statusElement.textContent = '✗ Tidak Tersedia';
                        statusElement.className = 'inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white shadow-xl bg-gradient-to-r from-yellow-500 to-orange-600';
                    }
                }

                // Update name and price
                const modalName = document.getElementById('modalName');
                const modalPrice = document.getElementById('modalPrice');
                if (modalName) modalName.textContent = property.name;
                if (modalPrice) modalPrice.textContent = property.price;

                // Update tags
                const tagsContainer = document.getElementById('modalTags');
                if (tagsContainer) {
                    tagsContainer.innerHTML = '';
                    if (property.category) {
                        const catTag = document.createElement('span');
                        catTag.className = 'inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded-lg';
                        catTag.innerHTML = `<svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>${property.category}`;
                        tagsContainer.appendChild(catTag);
                    }
                    if (property.type) {
                        const typeTag = document.createElement('span');
                        typeTag.className = 'inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-800 text-xs font-semibold rounded-lg';
                        typeTag.innerHTML = `<svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path></svg>${property.type}`;
                        tagsContainer.appendChild(typeTag);
                    }
                }

                // Update description
                const descSection = document.getElementById('modalDescriptionSection');
                const descContent = document.getElementById('modalDescription');
                if (property.description) {
                    if (descContent) descContent.textContent = property.description;
                    if (descSection) descSection.style.display = 'block';
                } else {
                    if (descSection) descSection.style.display = 'none';
                }

                // Update metadata
                const metadataContainer = document.getElementById('modalMetadata');
                if (metadataContainer && property.created_at) {
                    metadataContainer.innerHTML = `
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Dibuat</p>
                            <p class="text-sm font-bold text-gray-900">${new Date(property.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Diperbarui</p>
                            <p class="text-sm font-bold text-gray-900">${new Date(property.updated_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                    `;
                }

                // Update gambar carousel
                const gambarGallery = document.getElementById('modalGambarGallery');
                const gambarTrack = document.getElementById('gambarTrack');
                if (property.gambar_produks && property.gambar_produks.length > 0) {
                    gambarTrack.innerHTML = '';
                    property.gambar_produks.forEach(img => {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'carousel-item-modal';
                        imgDiv.innerHTML = `<img src="${img}" alt="Gambar Produk" class="w-full h-full object-cover">`;
                        imgDiv.onclick = () => window.open(img, '_blank');
                        gambarTrack.appendChild(imgDiv);
                    });
                    gambarGallery.style.display = 'block';
                    gambarScrollPosition = 0;
                    gambarTrack.style.transform = 'translateX(0)';
                } else {
                    gambarGallery.style.display = 'none';
                }

                // Update panorama carousel
                const panoramaGallery = document.getElementById('modalPanoramaGallery');
                const panoramaTrack = document.getElementById('panoramaTrack');
                if (property.panorama_produks && property.panorama_produks.length > 0) {
                    allPanoramas = property.panorama_produks;
                    panoramaTrack.innerHTML = '';
                    property.panorama_produks.forEach((item, index) => {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'carousel-item-modal';
                        imgDiv.innerHTML = `<img src="${item.image}" alt="${item.title || 'Panorama'}" class="w-full h-full object-cover">`;
                        imgDiv.onclick = () => openPanoramaModal(index);
                        panoramaTrack.appendChild(imgDiv);
                    });
                    panoramaGallery.style.display = 'block';
                    panoramaScrollPosition = 0;
                    panoramaTrack.style.transform = 'translateX(0)';
                } else {
                    panoramaGallery.style.display = 'none';
                }

                // Update denah (floor plan)
                const denahSection = document.getElementById('modalDenah');
                const denahImage = document.getElementById('modalDenahImage');
                if (property.denah && property.category === 'rumah') {
                    denahImage.src = property.denah;
                    denahSection.style.display = 'block';
                } else {
                    denahSection.style.display = 'none';
                }

                document.body.style.overflow = 'hidden';

                // Load 3D model
                setTimeout(() => {
                    if (property.model3d) {
                        init3DViewer(property.model3d);
                    } else {
                        const container = document.getElementById('viewer3d');
                        const loading = document.getElementById('viewer-loading');
                        if (loading) loading.style.display = 'none';
                        if (container) {
                            container.innerHTML = '<div class="flex items-center justify-center h-full text-gray-500">3D Model tidak tersedia</div>';
                        }
                    }
                }, 100);
            }

            function scrollGambarCarousel(direction) {
                const track = document.getElementById('gambarTrack');
                const container = track.parentElement;
                const itemWidth = 160 + 12; // width + gap

                gambarScrollPosition += direction * itemWidth * 3;

                const maxScroll = Math.max(0, (track.children.length * itemWidth) - container.offsetWidth);
                gambarScrollPosition = Math.max(0, Math.min(gambarScrollPosition, maxScroll));

                track.style.transform = `translateX(-${gambarScrollPosition}px)`;
            }

            function scrollPanoramaCarousel(direction) {
                const track = document.getElementById('panoramaTrack');
                const container = track.parentElement;
                const itemWidth = 160 + 12;

                panoramaScrollPosition += direction * itemWidth * 3;

                const maxScroll = Math.max(0, (track.children.length * itemWidth) - container.offsetWidth);
                panoramaScrollPosition = Math.max(0, Math.min(panoramaScrollPosition, maxScroll));

                track.style.transform = `translateX(-${panoramaScrollPosition}px)`;
            }

            let panoramaViewer = null;

            function openPanoramaModal(index) {
                console.log('🔵 Opening panorama modal, index:', index);

                const modalPanorama = document.getElementById('panorama-modal');

                if (!modalPanorama) {
                    alert('❌ Modal panorama tidak ditemukan!');
                    return;
                }

                if (!allPanoramas || allPanoramas.length === 0) {
                    alert('❌ Data panorama tidak tersedia!');
                    return;
                }

                console.log('✅ Modal found, panoramas:', allPanoramas.length);

                // FORCE SHOW dengan cara paling agresif
                modalPanorama.className = 'fixed inset-0 flex items-center justify-center';
                modalPanorama.style.cssText = `
                    display: flex !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    z-index: 999999 !important;
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    right: 0 !important;
                    bottom: 0 !important;
                    width: 100vw !important;
                    height: 100vh !important;
                    background-color: rgba(0, 0, 0, 0.95) !important;
                    align-items: center !important;
                    justify-content: center !important;
                `;

                // Ensure it's at body level
                if (modalPanorama.parentElement !== document.body) {
                    document.body.appendChild(modalPanorama);
                }

                // Debug info
                const rect = modalPanorama.getBoundingClientRect();
                console.log('📊 Modal rect:', {
                    width: rect.width,
                    height: rect.height,
                    top: rect.top,
                    left: rect.left
                });

                console.log('✅ MODAL SHOULD BE VISIBLE NOW!');


                const viewer = document.getElementById('panorama-viewer');
                const title = document.getElementById('panorama-title');
                const closeBtn = modalPanorama.querySelector('button');
                const innerContainer = modalPanorama.querySelector('div[onclick="event.stopPropagation()"]');

                // Style inner container first
                if (innerContainer) {
                    innerContainer.style.cssText = 'position: relative !important; z-index: 1000001 !important; display: block !important; width: 90% !important; height: 90% !important; max-width: 1400px !important; background-color: rgb(17, 24, 39) !important; border-radius: 1rem !important; overflow: hidden !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;';
                    console.log('✅ Inner container styled');
                }

                if (viewer) {
                    viewer.style.cssText = 'width: 100% !important; height: 100% !important; z-index: 1000002 !important; display: block !important; position: relative !important;';
                    console.log('✅ Viewer styled');
                }
                if (title) {
                    title.style.cssText = 'position: absolute !important; top: 1.25rem !important; left: 1.25rem !important; z-index: 1000003 !important; display: block !important; color: white !important; background-color: rgba(0, 0, 0, 0.7) !important; padding: 0.75rem 1.25rem !important; border-radius: 0.75rem !important; font-size: 1rem !important; font-weight: 600 !important;';
                    console.log('✅ Title styled');
                }
                if (closeBtn) {
                    closeBtn.style.cssText = 'position: absolute !important; top: 1.25rem !important; right: 1.25rem !important; z-index: 1000003 !important; display: flex !important; align-items: center !important; justify-content: center !important; width: 3rem !important; height: 3rem !important; background-color: rgba(255, 255, 255, 0.9) !important; border-radius: 9999px !important; cursor: pointer !important;';
                    console.log('✅ Close button styled');
                }
                setTimeout(() => {
                    loadPanoramaViewer(index);
                }, 100);
            }

            function loadPanoramaViewer(index) {
                console.log('Loading panorama viewer, index:', index);

                const viewer = document.getElementById('panorama-viewer');
                const titleEl = document.getElementById('panorama-title');
                const navPanel = document.getElementById('panorama-nav');
                const navItems = document.getElementById('panorama-nav-items');

                if (!allPanoramas || allPanoramas.length === 0) {
                    console.error('No panoramas in loadPanoramaViewer');
                    return;
                }

                if (index < 0 || index >= allPanoramas.length) {
                    console.error('Invalid panorama index:', index);
                    return;
                }

                const panorama = allPanoramas[index];
                console.log('Loading panorama:', panorama);

                if (titleEl) {
                    titleEl.textContent = panorama.title || `Panorama ${index + 1}`;
                }

                // Clear previous viewer
                if (panoramaViewer) {
                    try {
                        panoramaViewer.destroy();
                    } catch (e) {
                        console.warn('Error destroying previous viewer:', e);
                    }
                    panoramaViewer = null;
                }

                // Clear viewer container
                viewer.innerHTML = '';

                // Check if Pannellum is loaded
                if (typeof pannellum === 'undefined') {
                    console.error('Pannellum library not loaded');
                    viewer.innerHTML = '<div class="flex items-center justify-center h-full text-white"><p>Gagal memuat library panorama. Silakan refresh halaman.</p></div>';
                    return;
                }

                // Initialize Pannellum
                try {
                    panoramaViewer = pannellum.viewer(viewer, {
                        type: 'equirectangular',
                        panorama: panorama.image,
                        autoLoad: true,
                        showControls: true,
                        mouseZoom: true,
                        autoRotate: -2,
                        compass: false,
                        hfov: 110
                    });
                    console.log('Panorama viewer initialized successfully');
                } catch (error) {
                    console.error('Error initializing Pannellum:', error);
                    viewer.innerHTML = '<div class="flex items-center justify-center h-full text-white"><p>Error loading panorama: ' + error.message + '</p></div>';
                    return;
                }

                // Update navigation
                console.log('🔍 Total panoramas:', allPanoramas.length);

                // Always show navigation if there's at least 1 panorama
                if (allPanoramas.length >= 1) {
                    navItems.innerHTML = '';

                    // Update counter
                    const counter = document.getElementById('panorama-counter');
                    if (counter) {
                        counter.textContent = `${index + 1} / ${allPanoramas.length}`;
                    }

                    allPanoramas.forEach((p, i) => {
                        const navItem = document.createElement('div');
                        const isActive = i === index;

                        // Container with proper styling - border untuk active state
                        navItem.className = `relative overflow-hidden rounded-xl cursor-pointer transition-all duration-300 flex-shrink-0`;
                        navItem.style.width = '120px';
                        navItem.style.height = '90px';

                        // Inline style untuk border yang pasti muncul
                        if (isActive) {
                            navItem.style.cssText += `
                                border: 4px solid #60a5fa !important;
                                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 20px rgba(96, 165, 250, 0.5) !important;
                                transform: scale(1.05) !important;
                                opacity: 1 !important;
                            `;
                        } else {
                            navItem.style.cssText += `
                                border: 2px solid transparent !important;
                                opacity: 0.6 !important;
                            `;
                        }

                        // Create inner HTML with image and overlay - tanpa teks AKTIF
                        navItem.innerHTML = `
                            <img src="${p.image}" alt="${p.title || 'Panorama'}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-end p-2">
                                <div class="w-full">
                                    <p class="text-white text-xs font-semibold truncate ${isActive ? 'text-blue-400' : ''}">${p.title || `Panorama ${i + 1}`}</p>
                                </div>
                            </div>
                        `;

                        navItem.onmouseover = () => {
                            if (!isActive) navItem.style.opacity = '1';
                        };
                        navItem.onmouseout = () => {
                            if (!isActive) navItem.style.opacity = '0.6';
                        };

                        navItem.onclick = () => loadPanoramaViewer(i);
                        navItems.appendChild(navItem);
                    });

                    console.log('✅ Navigation items created:', navItems.children.length);

                    // Force display with aggressive styling
                    navPanel.style.cssText = 'display: block !important; position: absolute !important; bottom: 20px !important; left: 50% !important; transform: translateX(-50%) !important; z-index: 1000002 !important; visibility: visible !important; opacity: 1 !important;';

                    console.log('✅ Navigation panel displayed');
                    console.log('📊 Panel rect:', navPanel.getBoundingClientRect());
                } else {
                    console.log('❌ No panoramas to display');
                    navPanel.style.display = 'none';
                }
            }

            function closePanoramaModal(event) {
                console.log('Closing panorama modal');

                // If no event, just close (called programmatically or from close button directly)
                if (!event) {
                    console.log('No event, closing modal');
                } else {
                    // If event exists, check if it's backdrop click
                    if (event.target.id === 'panorama-modal') {
                        console.log('Backdrop clicked, closing modal');
                    } else {
                        // Check if click is on close button or its children
                        const closeButton = event.target.closest('button');
                        if (closeButton) {
                            console.log('Close button clicked, closing modal');
                        } else {
                            console.log('Click inside modal content, ignoring');
                            return;
                        }
                    }
                }

                const modal = document.getElementById('panorama-modal');
                if (modal) {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                    modal.style.cssText = '';
                }

                if (panoramaViewer) {
                    panoramaViewer.destroy();
                    panoramaViewer = null;
                }
            }

            function closeModal() {
                const modal = document.getElementById('propertyModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
                document.body.style.overflow = 'auto';

                // Reset data
                currentPropertyData = null;
                gambarScrollPosition = 0;
                panoramaScrollPosition = 0;
                allPanoramas = [];

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
                // Cek apakah user sudah login
                const isAuthenticated = <?php echo e(Auth::check() ? 'true' : 'false'); ?>;

                if (!isAuthenticated) {
                    alert('Silakan login terlebih dahulu untuk menjadwalkan kunjungan');
                    window.location.href = '/dashboard/login';
                    return;
                }

                const modalName = document.getElementById('modalName');
                const propertyName = modalName ? modalName.textContent : 'Properti';
                const userName = '<?php echo e(Auth::check() ? Auth::user()->name : ""); ?>';
                const userEmail = '<?php echo e(Auth::check() ? Auth::user()->email : ""); ?>';

                const message = `Halo, saya tertarik untuk menjadwalkan kunjungan\n\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `🏠 *PROPERTI*\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `${propertyName}\n\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `👤 *INFORMASI CUSTOMER*\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `Nama: ${userName}\n` +
                    `Email: ${userEmail}\n\n` +
                    `Mohon informasi jadwal kunjungan yang tersedia.\n` +
                    `Terima kasih 🙏`;

                window.open(`https://wa.me/6285664954621?text=${encodeURIComponent(message)}`, '_blank');
            }

            function contactDeveloper() {
                // Cek apakah user sudah login
                const isAuthenticated = <?php echo e(Auth::check() ? 'true' : 'false'); ?>;

                if (!isAuthenticated) {
                    alert('Silakan login terlebih dahulu untuk menghubungi developer');
                    window.location.href = '/dashboard/login';
                    return;
                }

                const modalName = document.getElementById('modalName');
                const propertyName = modalName ? modalName.textContent : 'Properti';
                const userName = '<?php echo e(Auth::check() ? Auth::user()->name : ""); ?>';
                const userEmail = '<?php echo e(Auth::check() ? Auth::user()->email : ""); ?>';

                const message = `Halo Admin Lumin Park 👋\n\n` +
                    `Saya ingin mendapatkan informasi lebih lanjut\n\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `🏠 *PROPERTI*\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `${propertyName}\n\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `👤 *INFORMASI CUSTOMER*\n` +
                    `━━━━━━━━━━━━━━━━━━━━━━\n` +
                    `Nama: ${userName}\n` +
                    `Email: ${userEmail}\n\n` +
                    `Mohon informasi detail dan harga terbaik.\n` +
                    `Terima kasih 🙏`;

                window.open(`https://wa.me/6285664954621?text=${encodeURIComponent(message)}`, '_blank');
            }

            // Make functions global so they can be called from HTML
            window.openModal = openModal;
            window.closeModal = closeModal;
            window.closeModalOnBackdrop = closeModalOnBackdrop;
            window.scheduleVisit = scheduleVisit;
            window.contactDeveloper = contactDeveloper;
            window.scrollGambarCarousel = scrollGambarCarousel;
            window.scrollPanoramaCarousel = scrollPanoramaCarousel;
            window.openPanoramaModal = openPanoramaModal;
            window.closePanoramaModal = closePanoramaModal;
        </script>
        <!-- Styles / Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

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
                    <div class="hidden md:flex items-center space-x-4">
                        <?php if(auth()->guard()->check()): ?>
                            <a href="/dashboard"
                                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg font-medium">
                                Dashboard
                            </a>
                            <form method="POST" action="/logout" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit"
                                    class="px-6 py-2 border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition-all duration-300 font-medium">
                                    Logout
                                </button>
                            </form>
                        <?php else: ?>
                            <a href="/dashboard/login" class="text-gray-700 hover:text-blue-600 transition font-medium">
                                Login
                            </a>
                            <a href="/dashboard/register"
                                class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg font-medium">
                                Register
                            </a>
                        <?php endif; ?>
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
        <section id="catalog"
            class="py-24 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 relative overflow-hidden">
            <!-- Background Decoration -->
            <div
                class="absolute top-0 left-0 w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob">
            </div>
            <div
                class="absolute top-0 right-0 w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000">
            </div>
            <div
                class="absolute bottom-0 left-1/2 w-96 h-96 bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000">
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center mb-16">
                    <div class="inline-block mb-4">
                        <span
                            class="bg-gradient-to-r from-purple-600 to-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-lg">✨
                            Properti Terbaik</span>
                    </div>
                    <h2 class="text-5xl md:text-6xl font-extrabold text-gray-900 mb-4 tracking-tight">
                        <span class="gradient-text">Katalog</span> Properti
                    </h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">Temukan hunian impian Anda dengan pilihan desain
                        modern & eksklusif yang memukau</p>
                </div>

                <!-- Filter -->
                <div class="filter-container rounded-3xl shadow-2xl p-8 mb-16 backdrop-blur-xl">
                    <div class="flex flex-col lg:flex-row items-center gap-6">
                        <!-- Search Input -->
                        <div class="relative w-full lg:flex-1 group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <svg class="w-6 h-6 text-gray-400 group-focus-within:text-purple-500 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" id="searchInput"
                                placeholder="Cari properti berdasarkan nama, tipe, atau kategori..."
                                class="search-input w-full pl-14 pr-14 py-5 rounded-2xl border-2 border-gray-200 focus:border-purple-500 focus:outline-none transition-all text-gray-700 placeholder-gray-400 text-lg font-medium shadow-sm" />
                            <button id="clearSearch"
                                class="absolute inset-y-0 right-0 pr-5 flex items-center transition-all hover:scale-110"
                                style="display:none;">
                                <svg class="w-5 h-5 text-gray-400 hover:text-red-500 transition-colors" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </button>
                        </div>

                        <!-- Category Filters -->
                        <div class="flex flex-wrap gap-3 justify-center lg:justify-end">
                            <button onclick="filterProperties('all')"
                                class="filter-btn active px-8 py-3.5 rounded-xl border-2 border-gray-200 font-bold transition-all hover:border-purple-500 text-sm tracking-wide relative z-10">
                                🏠 Semua
                            </button>
                            <button onclick="filterProperties('rumah')"
                                class="filter-btn px-8 py-3.5 rounded-xl border-2 border-gray-200 font-bold transition-all hover:border-purple-500 text-sm tracking-wide relative z-10">
                                🏡 Rumah
                            </button>
                            <button onclick="filterProperties('properti')"
                                class="filter-btn px-8 py-3.5 rounded-xl border-2 border-gray-200 font-bold transition-all hover:border-purple-500 text-sm tracking-wide relative z-10">
                                🪑 Perabotan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Property Grid -->
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php $__empty_1 = true; $__currentLoopData = $katalogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $katalog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $property = [
                                'id' => $katalog->id,
                                'name' => $katalog->name,
                                'category' => $katalog->category,
                                'type' => $katalog->type,
                                'price' => 'Rp ' . number_format((float) $katalog->price, 0, ',', '.'),
                                'status' => $katalog->is_available ? 'available' : 'reserved',
                                'image' => $katalog->image ? Storage::url($katalog->image) : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=600',
                                'model3d' => $katalog->model_3d ? Storage::url($katalog->model_3d) : '/storage/models/model-' . strtolower(str_replace(' ', '-', $katalog->type)) . '.glb',
                                'description' => $katalog->description,
                                'denah' => $katalog->denah ? Storage::url($katalog->denah) : null,
                                'created_at' => $katalog->created_at->toISOString(),
                                'updated_at' => $katalog->updated_at->toISOString(),
                                'gambar_produks' => $katalog->gambarProduks->map(fn($g) => Storage::url($g->image))->toArray(),
                                'panorama_produks' => $katalog->panoramaProduks->map(fn($p) => [
                                    'image' => Storage::url($p->image),
                                    'title' => $p->title ?? 'Panorama 360°'
                                ])->toArray()
                            ];
                        ?>

                        <div onclick="openModal(<?php echo e(json_encode($property)); ?>)" data-type="<?php echo e($katalog->type); ?>"
                            data-category="<?php echo e($katalog->category); ?>" data-name="<?php echo e(strtolower($katalog->name)); ?>"
                            class="property-card group bg-white rounded-3xl shadow-xl overflow-hidden cursor-pointer transform hover:shadow-2xl relative">
                            <!-- Shine Effect -->
                            <div class="card-shine"></div>

                            <div class="relative h-64 overflow-hidden">
                                <img src="<?php echo e($property['image']); ?>" alt="<?php echo e($katalog->name); ?>"
                                    class="property-image w-full h-full object-cover" />

                                <!-- Gradient Overlay -->
                                <div class="overlay-gradient absolute inset-0"></div>

                                <!-- Badges -->
                                <div class="absolute top-4 right-4">
                                    <?php if($katalog->category === 'rumah'): ?>
                                        <span
                                            class="badge-type inline-block text-white px-4 py-2 text-xs font-bold rounded-full shadow-lg backdrop-blur-sm">
                                            Tipe <?php echo e($katalog->type); ?>

                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="<?php echo e($katalog->is_available ? 'badge-available' : 'badge-reserved'); ?> inline-block text-white px-4 py-2 text-xs font-bold rounded-full shadow-lg">
                                        <?php echo e($katalog->is_available ? '✓ Tersedia' : '⏳ Tidak Tersedia'); ?>

                                    </span>
                                </div>

                                <!-- View 3D Button -->
                                <div
                                    class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <span
                                        class="bg-white/90 backdrop-blur-sm text-gray-900 px-4 py-2 rounded-full text-xs font-bold shadow-lg flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        Lihat 3D
                                    </span>
                                </div>
                            </div>

                            <div class="p-6">
                                <h3
                                    class="text-xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors line-clamp-1">
                                    <?php echo e($katalog->name); ?>

                                </h3>
                                <p class="price-tag text-3xl font-extrabold mb-3"><?php echo e($property['price']); ?></p>
                                <?php if($katalog->description): ?>
                                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 mb-4">
                                        <?php echo e(Str::limit($katalog->description, 100)); ?>

                                    </p>
                                <?php endif; ?>

                                <!-- Action Button -->
                                <div class="pt-4 border-t border-gray-100">
                                    <button
                                        class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 rounded-xl font-semibold hover:shadow-lg transition-all duration-300 hover:scale-105 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="col-span-full text-center py-20">
                            <div class="inline-block p-6 bg-white rounded-3xl shadow-xl mb-6">
                                <svg class="w-24 h-24 text-gray-300 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-3xl font-bold text-gray-800 mb-2">Belum Ada Properti</h3>
                            <p class="text-gray-500 text-lg">Properti baru akan segera hadir. Nantikan update terbaru kami!
                                🏡</p>
                        </div>
                    <?php endif; ?>
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
        <div id="propertyModal"
            class="fixed inset-0 bg-black/90 hidden items-center justify-center z-50 p-1 sm:p-2 md:p-4"
            onclick="closeModalOnBackdrop(event)">
            <div class="bg-white rounded-2xl w-full max-w-3xl sm:max-w-4xl lg:max-w-5xl xl:max-w-6xl 2xl:max-w-7xl h-full max-h-[80vh] sm:max-h-[85vh] md:max-h-[90vh] overflow-hidden flex flex-col"
                onclick="event.stopPropagation()">
                <!-- Modal Header -->
                <div class="flex-shrink-0 relative p-3 sm:p-4 border-b border-gray-200">
                    <button onclick="closeModal()"
                        class="absolute top-3 right-3 sm:top-4 sm:right-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition z-50">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content (Scrollable) -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Product Detail Container -->
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6">

                            <!-- LEFT: 3D VIEWER -->
                            <div class="viewer-wrapper-modal">
                                <div id="viewer3d"
                                    class="viewer-container-modal relative w-full bg-gradient-to-br from-gray-100 to-gray-200 rounded-2xl overflow-hidden border-2 border-gray-200 shadow-xl"
                                    style="aspect-ratio: 4/3; min-height: 300px; max-height: 500px;">
                                    <div id="viewer-loading"
                                        class="absolute inset-0 flex items-center justify-center z-10">
                                        <div class="text-center">
                                            <div
                                                class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4">
                                            </div>
                                            <p class="text-gray-600">Loading 3D Model...</p>
                                        </div>
                                    </div>
                                    <div
                                        class="absolute bottom-4 left-4 bg-black/70 text-white text-xs px-3 py-2 rounded-lg z-10">
                                        🖱️ Drag to rotate • 🔍 Scroll to zoom
                                    </div>
                                </div>

                                <!-- GALLERY PANORAMA (Below 3D Model) -->
                                <div id="modalPanoramaGallery" class="mt-6" style="display: none;">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="w-1 h-6 bg-gradient-to-b from-blue-600 to-indigo-600 rounded-full">
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900">Galeri Panorama 360°</h3>
                                    </div>
                                    <div
                                        class="relative rounded-xl bg-gray-50 border border-gray-200 p-4 overflow-hidden">
                                        <div class="relative overflow-hidden rounded-lg">
                                            <div id="panoramaTrack"
                                                class="flex gap-3 transition-transform duration-300"></div>
                                        </div>
                                        <button id="panoramaPrevBtn"
                                            class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white rounded-full w-9 h-9 flex items-center justify-center shadow-lg transition z-10"
                                            onclick="scrollPanoramaCarousel(-1)">
                                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                        </button>
                                        <button id="panoramaNextBtn"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white rounded-full w-9 h-9 flex items-center justify-center shadow-lg transition z-10"
                                            onclick="scrollPanoramaCarousel(1)">
                                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT: PRODUCT PANEL -->
                            <div class="product-panel-modal bg-white rounded-2xl overflow-hidden border-2 border-gray-200 shadow-xl"
                                style="min-height: 400px;">
                                <!-- HERO IMAGE -->
                                <div class="relative w-full h-60 bg-gradient-to-br from-gray-50 to-gray-100">
                                    <img id="modalHeroImage" class="absolute inset-0 w-full h-full object-cover"
                                        alt="Product">
                                    <div
                                        class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent">
                                    </div>

                                    <!-- STATUS BADGE -->
                                    <div class="absolute top-4 right-4">
                                        <span id="modalStatus"
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white shadow-xl"></span>
                                    </div>
                                </div>

                                <!-- CONTENT AREA -->
                                <div class="p-6 space-y-5">
                                    <!-- TITLE & TAGS -->
                                    <div>
                                        <h2 id="modalName" class="text-3xl font-black text-gray-900 mb-3 leading-tight">
                                        </h2>
                                        <div id="modalTags" class="flex flex-wrap gap-2"></div>
                                    </div>

                                    <!-- PRICE CARD -->
                                    <div
                                        class="relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-5 shadow-lg">
                                        <div
                                            class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full filter blur-3xl">
                                        </div>
                                        <div
                                            class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full filter blur-2xl">
                                        </div>
                                        <div class="relative">
                                            <p class="text-xs font-semibold text-black uppercase tracking-wide mb-1">
                                                Harga</p>
                                            <p id="modalPrice" class="text-3xl font-black text-black tracking-tight">
                                            </p>
                                        </div>
                                    </div>

                                    <!-- DESCRIPTION -->
                                    <div id="modalDescriptionSection" style="display: none;">
                                        <div class="flex items-center gap-2 mb-2 mt-2">
                                            <div
                                                class="w-1 h-6 bg-gradient-to-b from-blue-600 to-indigo-600 rounded-full">
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">Deskripsi</h3>
                                        </div>
                                        <div class="rounded-xl bg-gray-50 border border-gray-200 p-5">
                                            <div id="modalDescription" class="text-sm leading-relaxed text-gray-700">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- METADATA -->
                                    <div class="pt-4 border-t-2 border-gray-200">
                                        <div class="grid grid-cols-2 gap-5" id="modalMetadata"></div>
                                    </div>

                                    <!-- GALLERY GAMBAR PRODUK -->
                                    <div id="modalGambarGallery" style="display: none;">
                                        <div class="flex items-center gap-2 mb-3">
                                            <div
                                                class="w-1 h-6 bg-gradient-to-b from-blue-600 to-indigo-600 rounded-full">
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">Galeri Gambar</h3>
                                        </div>
                                        <div
                                            class="relative rounded-xl bg-gray-50 border border-gray-200 p-4 overflow-hidden">
                                            <div class="relative overflow-hidden rounded-lg">
                                                <div id="gambarTrack"
                                                    class="flex gap-3 transition-transform duration-300"></div>
                                            </div>
                                            <button id="gambarPrevBtn"
                                                class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white rounded-full w-9 h-9 flex items-center justify-center shadow-lg transition z-10"
                                                onclick="scrollGambarCarousel(-1)">
                                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                            </button>
                                            <button id="gambarNextBtn"
                                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white rounded-full w-9 h-9 flex items-center justify-center shadow-lg transition z-10"
                                                onclick="scrollGambarCarousel(1)">
                                                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- DENAH (Floor Plan) -->
                                    <div id="modalDenah" style="display: none;">
                                        <div class="flex items-center gap-2 mb-3">
                                            <div
                                                class="w-1 h-6 bg-gradient-to-b from-blue-600 to-indigo-600 rounded-full">
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">Denah Lantai</h3>
                                        </div>
                                        <div class="rounded-xl bg-gray-50 border border-gray-200 p-4">
                                            <div class="relative overflow-hidden rounded-lg">
                                                <img id="modalDenahImage" src="" alt="Denah"
                                                    class="w-2xl h-auto cursor-pointer"
                                                    onclick="window.open(this.src, '_blank')" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ACTION BUTTONS -->
                                    <div class="flex gap-3 pt-4">
                                        <button onclick="contactDeveloper()"
                                            class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                            Hubungi Developer
                                        </button>
                                        <button onclick="scheduleVisit()"
                                            class="flex-1 border-2 border-blue-600 text-blue-600 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-all duration-300">
                                            Jadwalkan Kunjungan
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 360 Panorama Modal -->
        <div id="panorama-modal" class="fixed inset-0 bg-black/95 hidden items-center justify-center z-[9999]"
            onclick="closePanoramaModal(event)">
            <div class="relative w-[90%] h-[90%] max-w-[1400px] bg-gray-900 rounded-2xl shadow-2xl"
                onclick="event.stopPropagation()" style="z-index: 1000000; overflow: visible;">
                <button onclick="closePanoramaModal(); event.stopPropagation();"
                    class="absolute top-5 right-5 bg-white/90 hover:bg-white rounded-full w-12 h-12 flex items-center justify-center transition-all hover:scale-110 shadow-lg"
                    style="z-index: 1000001 !important;">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
                <div id="panorama-title"
                    class="absolute top-5 left-5 bg-black/70 text-white px-5 py-3 rounded-xl text-base font-semibold backdrop-blur-lg"
                    style="z-index: 1000001 !important;">
                    Panorama 360°</div>
                <div id="panorama-viewer" class="w-full h-full rounded-2xl"
                    style="z-index: 1000000 !important; overflow: hidden;"></div>
                <div id="panorama-nav"
                    class="absolute bottom-5 left-1/2 transform -translate-x-1/2 bg-black/90 px-4 py-4 rounded-2xl backdrop-blur-lg max-w-[90%] shadow-2xl border border-white/10 scroll-auto"
                    style="display: none; z-index: 1000002 !important;">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-white text-sm font-bold uppercase tracking-wider">📸 Pilih Panorama</p>
                        <span id="panorama-counter" class="text-white/70 text-xs font-medium"></span>
                    </div>
                    <div id="panorama-nav-items" class="flex gap-3 overflow-x-auto pb-1"
                        style="scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.3) transparent;"></div>
                </div>
            </div>
        </div>

        <style>
            .viewer-container-modal canvas {
                width: 100% !important;
                height: 100% !important;
            }

            /* Custom scrollbar untuk panorama navigation */
            #panorama-nav-items::-webkit-scrollbar {
                height: 6px;
                width: 0;
                /* Hide vertical scrollbar */
            }

            #panorama-nav-items::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 3px;
            }

            #panorama-nav-items::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.3);
                border-radius: 3px;
            }

            #panorama-nav-items::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.5);
            }

            /* Hide vertical scrollbar for all browsers */
            #panorama-nav-items {
                overflow-y: hidden !important;
                overflow-x: auto;
                scrollbar-width: thin;
                /* Firefox horizontal scrollbar */
                scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
            }

            #panoramaTrack .carousel-item-modal,
            #gambarTrack .carousel-item-modal {
                flex-shrink: 0;
                width: 160px;
                height: 120px;
                border-radius: 8px;
                overflow: hidden;
                cursor: pointer;
                transition: transform 0.2s ease;
            }

            #panoramaTrack .carousel-item-modal:hover,
            #gambarTrack .carousel-item-modal:hover {
                transform: scale(1.05);
            }

            #panoramaTrack .carousel-item-modal img,
            #gambarTrack .carousel-item-modal img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        </style>

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
            function filterProperties(category, searchTerm = '') {
                const cards = document.querySelectorAll('[data-category]');
                const buttons = document.querySelectorAll('#catalog button');

                // Get search term from input if not provided
                if (searchTerm === '') {
                    const searchInput = document.getElementById('searchInput');
                    searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
                }

                cards.forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    const cardName = card.getAttribute('data-name') || '';
                    const cardType = card.getAttribute('data-type') ? card.getAttribute('data-type').toLowerCase() : '';

                    // Check category filter
                    const categoryMatch = category === 'all' || cardCategory === category;

                    // Check search filter (name, type, or category)
                    const searchMatch = searchTerm === '' ||
                        cardName.includes(searchTerm) ||
                        cardType.includes(searchTerm) ||
                        cardCategory.toLowerCase().includes(searchTerm);

                    // Show card if both filters match
                    card.style.display = (categoryMatch && searchMatch) ? 'block' : 'none';
                });

                // Update button styles
                buttons.forEach(btn => btn.classList.remove('bg-blue-600', 'text-white'));
                const activeBtn = Array.from(buttons).find(b => b.innerText.toLowerCase() === (category === 'all' ? 'semua' : category.toLowerCase()));
                if (activeBtn) activeBtn.classList.add('bg-blue-600', 'text-white');
            }

            // Search input event listener
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('searchInput');
                const clearButton = document.getElementById('clearSearch');

                if (searchInput) {
                    searchInput.addEventListener('input', function () {
                        // Get current active category
                        const activeButton = document.querySelector('#catalog button.bg-blue-600');
                        const currentCategory = activeButton ?
                            (activeButton.textContent.toLowerCase() === 'semua' ? 'all' : activeButton.textContent.toLowerCase()) :
                            'all';
                        filterProperties(currentCategory);

                        // Show/hide clear button
                        if (clearButton) {
                            clearButton.style.display = searchInput.value ? 'block' : 'none';
                        }
                    });
                }

                if (clearButton) {
                    clearButton.addEventListener('click', function () {
                        if (searchInput) {
                            searchInput.value = '';
                            searchInput.focus();
                            clearButton.style.display = 'none';
                            // Re-filter with empty search
                            const activeButton = document.querySelector('#catalog button.bg-blue-600');
                            const currentCategory = activeButton ?
                                (activeButton.textContent.toLowerCase() === 'semua' ? 'all' : activeButton.textContent.toLowerCase()) :
                                'all';
                            filterProperties(currentCategory);
                        }
                    });
                }
            });



        </script>
    </body>

</html><?php /**PATH C:\laragon\www\lumin_park_housing\resources\views/welcome.blade.php ENDPATH**/ ?>