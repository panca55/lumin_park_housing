<?php $viewerId = 'viewer-3d-' . uniqid(); ?>

<div>
    <?php $__env->startPush('styles'); ?>
        <style scoped>
            .product-detail-container {
                width: 100%;
            }

            .product-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 24px;
            }

            @media (min-width: 1024px) {
                .product-grid {
                    grid-template-columns: 1fr 1fr;
                }
            }

            /* 3D Viewer Styles */
            .viewer-wrapper {
                width: 100%;
            }

            .viewer-container {
                position: relative;
                width: 100%;
                border-radius: 16px;
                overflow: hidden;
                border: 2px solid #e5e7eb;
                background: linear-gradient(to bottom right, #f8fafc, #f1f5f9);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                aspect-ratio: 4/3;
                min-height: 400px;
            }

            .viewer-canvas {
                width: 100%;
                height: 100%;
            }

            .viewer-placeholder {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 16px;
                color: #9ca3af;
            }

            .dark .viewer-placeholder {
                color: #6b7280;
            }

            .viewer-placeholder svg {
                width: 96px;
                height: 96px;
            }

            .viewer-placeholder p {
                font-size: 18px;
                font-weight: 600;
            }

            /* Product Panel Styles */
            .product-panel {
                position: relative;
                width: 100%;
                border-radius: 16px;
                overflow: hidden;
                background-color: #ffffff;
                border: 2px solid #e5e7eb;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                min-height: 400px;
            }

            /* DARK MODE */
            .dark .product-panel {
                background-color: #1f2937;
                border-color: #374151;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            }

            .hero-section {
                position: relative;
                width: 100%;
                height: 240px;
                background: linear-gradient(to bottom right, #f3f4f6, #e5e7eb);
            }

            .hero-image {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .hero-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.2), transparent);
            }

            .hero-placeholder {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 12px;
            }

            .hero-placeholder svg {
                width: 80px;
                height: 80px;
                color: #9ca3af;
            }

            .hero-placeholder p {
                color: #6b7280;
                font-weight: 500;
            }

            .status-badge-wrapper {
                position: absolute;
                top: 16px;
                right: 16px;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                border-radius: 9999px;
                font-size: 14px;
                font-weight: 700;
                color: white;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }

            .badge-available {
                background: linear-gradient(to right, #10b981, #059669);
            }

            .badge-unavailable {
                background: linear-gradient(to right, #ef4444, #dc2626);
            }

            .status-dot {
                width: 8px;
                height: 8px;
                background-color: white;
                border-radius: 9999px;
            }

            .pulse {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
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

            .content-area {
                padding: 24px;
            }

            .content-area>*+* {
                margin-top: 20px;
            }

            .title-section {
                margin-bottom: 12px;
            }

            .product-title {
                font-size: 30px;
                font-weight: 900;
                color: #111827;
                line-height: 1.2;
                margin: 0 0 12px 0;
            }

            .dark .product-title {
                color: #f9fafb;
            }

            .tags-container {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .tag {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 14px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                border: 1px solid;
            }

            .tag-category {
                background: linear-gradient(to bottom right, #eff6ff, #dbeafe);
                border-color: #bfdbfe;
                color: #1d4ed8;
            }

            .tag-type {
                background: linear-gradient(to bottom right, #faf5ff, #f3e8ff);
                border-color: #d8b4fe;
                color: #7e22ce;
            }

            .price-card {
                position: relative;
                overflow: hidden;
                border-radius: 12px;
                background: linear-gradient(to bottom right, #2563eb, #4f46e5, #7c3aed);
                padding: 20px;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            }

            .price-bg-1,
            .price-bg-2 {
                position: absolute;
                background-color: rgba(255, 255, 255, 0.1);
                border-radius: 9999px;
            }

            .price-bg-1 {
                top: 0;
                right: 0;
                width: 128px;
                height: 128px;
                filter: blur(64px);
            }

            .price-bg-2 {
                bottom: 0;
                left: 0;
                width: 96px;
                height: 96px;
                filter: blur(40px);
            }

            .price-content {
                position: relative;
            }

            .price-label {
                font-size: 12px;
                font-weight: 500;
                color: rgba(255, 255, 255, 0.8);
                margin-bottom: 4px;
                letter-spacing: 0.05em;
                text-transform: uppercase;
            }

            .price-value {
                font-size: 30px;
                font-weight: 900;
                color: white;
                letter-spacing: -0.025em;
            }

            .description-section>*+* {
                margin-top: 10px;
            }

            .section-header {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .header-line {
                width: 4px;
                height: 24px;
                background: linear-gradient(to bottom, #2563eb, #4f46e5);
                border-radius: 9999px;
            }

            .section-title {
                font-size: 18px;
                font-weight: 700;
                color: #111827;
                margin: 0;
            }

            .dark .section-title {
                color: #f9fafb;
            }

            .description-box {
                border-radius: 12px;
                background-color: #f9fafb;
                border: 1px solid #e5e7eb;
                padding: 20px;
            }

            .dark .description-box {
                background-color: #111827;
                border-color: #374151;
            }

            .description-content {
                font-size: 14px;
                line-height: 1.75;
                color: #374151;
            }

            .dark .description-content {
                color: #d1d5db;
            }

            .description-content strong {
                font-weight: 700;
                color: #111827;
            }

            .dark .description-content strong {
                color: #f9fafb;
            }

            .metadata-section {
                padding-top: 16px;
                margin-top: 4px;
                border-top: 2px solid #e5e7eb;
            }

            .metadata-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .metadata-item {
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }

            .metadata-icon {
                flex-shrink: 0;
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .icon-blue {
                background-color: #dbeafe;
            }

            .icon-purple {
                background-color: #f3e8ff;
            }

            .metadata-icon svg {
                width: 16px;
                height: 16px;
            }

            .icon-blue svg {
                color: #2563eb;
            }

            .icon-purple svg {
                color: #7c3aed;
            }

            .metadata-text {
                flex: 1;
                min-width: 0;
            }

            .metadata-label {
                font-size: 12px;
                font-weight: 600;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 2px;
            }

            .metadata-value {
                font-size: 14px;
                font-weight: 700;
                color: #111827;
            }

            .dark .metadata-value {
                color: #f9fafb;
            }

            /* Gallery Carousel Styles */
            .gallery-section {
                margin-top: 20px;
            }

            .carousel-container {
                position: relative;
                border-radius: 12px;
                background-color: #f9fafb;
                border: 1px solid #e5e7eb;
                padding: 16px;
                overflow: hidden;
            }

            .dark .carousel-container {
                background-color: #111827;
                border-color: #374151;
            }

            .carousel-wrapper {
                position: relative;
                overflow: hidden;
                border-radius: 8px;
            }

            .carousel-track {
                display: flex;
                gap: 12px;
                transition: transform 0.3s ease;
            }

            .carousel-item {
                flex-shrink: 0;
                width: 200px;
                height: 150px;
                border-radius: 8px;
                overflow: hidden;
                cursor: pointer;
                transition: transform 0.2s ease;
            }

            .carousel-item:hover {
                transform: scale(1.05);
            }

            .carousel-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .carousel-button {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(255, 255, 255, 0.9);
                border: none;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 10;
                transition: all 0.2s ease;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .dark .carousel-button {
                background: rgba(31, 41, 55, 0.9);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            }

            .carousel-button:hover {
                background: white;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .dark .carousel-button:hover {
                background: #374151;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            }

            .carousel-button:disabled {
                opacity: 0.3;
                cursor: not-allowed;
            }

            .carousel-button-prev {
                left: 8px;
            }

            .carousel-button-next {
                right: 8px;
            }

            .carousel-button svg {
                width: 20px;
                height: 20px;
                color: #374151;
            }

            /* 360 Panorama Modal Styles */
            .panorama-modal {
                display: none;
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.95);
                animation: fadeIn 0.3s ease;
            }

            .panorama-modal.active {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            .panorama-modal-content {
                position: relative;
                width: 90%;
                height: 90%;
                max-width: 1400px;
                background-color: #1f2937;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            }

            .dark .panorama-modal-content {
                background-color: #0f172a;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            }

            .panorama-viewer {
                width: 100%;
                height: 100%;
            }

            .panorama-close {
                position: absolute;
                top: 20px;
                right: 20px;
                background: rgba(255, 255, 255, 0.9);
                border: none;
                border-radius: 50%;
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 10;
                transition: all 0.2s ease;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            }

            .panorama-close:hover {
                background: white;
                transform: scale(1.1);
            }

            .dark .panorama-close:hover {
                background: #f1f5f9;
            }

            .panorama-close svg {
                width: 24px;
                height: 24px;
                color: #374151;
            }

            .dark .panorama-close svg {
                color: #1f2937;
            }

            .panorama-title {
                position: absolute;
                top: 20px;
                left: 20px;
                background: rgba(0, 0, 0, 0.7);
                color: white;
                padding: 12px 20px;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 600;
                z-index: 10;
                backdrop-filter: blur(10px);
            }

            .dark .panorama-title {
                background: rgba(15, 23, 42, 0.9);
                color: #f1f5f9;
            }

            /* Panorama Navigation Panel */
            .panorama-nav {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0, 0, 0, 0.8);
                padding: 12px;
                border-radius: 12px;
                z-index: 10;
                backdrop-filter: blur(10px);
                max-width: 90%;
                overflow-x: auto;
                overflow-y: hidden;
            }

            .dark .panorama-nav {
                background: rgba(15, 23, 42, 0.9);
            }

            .panorama-nav::-webkit-scrollbar {
                height: 6px;
            }

            .panorama-nav::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 3px;
            }

            .panorama-nav::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.3);
                border-radius: 3px;
            }

            .panorama-nav::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.5);
            }

            .panorama-nav-title {
                color: rgba(255, 255, 255, 0.8);
                font-size: 12px;
                font-weight: 600;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .dark .panorama-nav-title {
                color: rgba(241, 245, 249, 0.8);
            }

            .panorama-nav-items {
                display: flex;
                gap: 12px;
            }

            .panorama-nav-item {
                position: relative;
                flex-shrink: 0;
                width: 120px;
                height: 80px;
                border-radius: 8px;
                overflow: hidden;
                cursor: pointer;
                transition: all 0.2s ease;
                border: 3px solid transparent;
            }

            .panorama-nav-item:hover {
                transform: scale(1.05);
                border-color: rgba(255, 255, 255, 0.5);
            }

            .dark .panorama-nav-item:hover {
                border-color: rgba(241, 245, 249, 0.5);
            }

            .panorama-nav-item.active {
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
            }

            .panorama-nav-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .panorama-nav-item-title {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
                color: white;
                padding: 8px 6px 4px;
                font-size: 11px;
                font-weight: 600;
                text-align: center;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .dark .panorama-nav-item-title {
                background: linear-gradient(to top, rgba(15, 23, 42, 0.9), transparent);
                color: #f1f5f9;
            }
        </style>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('scripts'); ?>
        <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
        <!--[if BLOCK]><![endif]--><?php if($model_3d): ?>
            <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/DRACOLoader.js"></script>

            <script>
                (() => {
                    const canvas = document.getElementById('<?php echo e($viewerId); ?>');
                    if (!canvas) return;

                    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
                    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
                    renderer.setSize(canvas.clientWidth, canvas.clientHeight);
                    renderer.shadowMap.enabled = false;

                    const scene = new THREE.Scene();
                    // Set background color based on theme
                    const updateSceneBackground = () => {
                        const isDark = document.documentElement.classList.contains('dark');
                        scene.background = new THREE.Color(isDark ? 0x1f2937 : 0xf1f5f9);
                    };
                    updateSceneBackground();

                    const camera = new THREE.PerspectiveCamera(45, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);

                    const controls = new THREE.OrbitControls(camera, renderer.domElement);
                    controls.enableDamping = true;
                    controls.autoRotate = true;
                    controls.autoRotateSpeed = 2.0;
                    scene.add(new THREE.AmbientLight(0xffffff, 0.7));
                    const light = new THREE.DirectionalLight(0xffffff, 0.8);
                    light.position.set(5, 5, 5);
                    scene.add(light);

                    const loader = new THREE.GLTFLoader();

                    // Configure DRACO loader for compressed models
                    const dracoLoader = new THREE.DRACOLoader();
                    dracoLoader.setDecoderPath('https://www.gstatic.com/draco/versioned/decoders/1.5.6/');
                    loader.setDRACOLoader(dracoLoader);

                    loader.load('<?php echo e($model_3d); ?>', (gltf) => {
                        const model = gltf.scene;
                        scene.add(model);

                        const box = new THREE.Box3().setFromObject(model);
                        const size = box.getSize(new THREE.Vector3()).length();
                        const center = box.getCenter(new THREE.Vector3());

                        model.position.sub(center);
                        camera.position.set(0, size * 0.6, size);
                        controls.target.set(0, 0, 0);

                        // Model loaded successfully, animation loop will handle rendering
                    }, (progress) => {
                        console.log('Loading progress:', (progress.loaded / progress.total * 100) + '%');
                    }, (error) => {
                        console.error('Error loading 3D model:', error);
                        // Show error in placeholder
                        const placeholder = canvas.parentElement.querySelector('.viewer-placeholder p');
                        if (placeholder) {
                            placeholder.textContent = 'Error loading 3D model';
                        }
                    });

                    function animate() {
                        requestAnimationFrame(animate);
                        controls.update();
                        renderer.render(scene, camera);
                    }

                    animate();

                    window.addEventListener('resize', () => {
                        const w = canvas.clientWidth;
                        const h = canvas.clientHeight;
                        camera.aspect = w / h;
                        camera.updateProjectionMatrix();
                        renderer.setSize(w, h);
                    });

                    // Listen for theme changes
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                updateSceneBackground();
                            }
                        });
                    });
                    observer.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class']
                    });
                })();
            </script>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        <script>
            // Track scroll positions for each carousel
            const carouselPositions = {
                gambar: 0,
                panorama: 0,
                denah: 0
            };

            function scrollCarousel(type, direction) {
                const track = document.getElementById(`${type}-track`);
                if (!track) return;

                const scrollAmount = 212; // 200px width + 12px gap
                const items = track.children.length;

                // Calculate max scroll based on total width minus visible area
                const totalWidth = items * scrollAmount;
                const containerWidth = track.parentElement.clientWidth;
                const maxScroll = Math.max(0, totalWidth - containerWidth);

                // Update position
                carouselPositions[type] += (scrollAmount * direction);

                // Clamp position to valid range
                carouselPositions[type] = Math.max(0, Math.min(carouselPositions[type], maxScroll));

                // Apply transform
                track.style.transform = `translateX(-${carouselPositions[type]}px)`;
            }

            // 360 Panorama Viewer
            let panoramaViewer = null;
            let panoramaData = [];
            let currentPanoramaIndex = 0;

            function openPanoramaViewer(index, allPanoramas) {
                panoramaData = allPanoramas;
                currentPanoramaIndex = index;

                const modal = document.getElementById('panorama-modal');
                const navPanel = document.getElementById('panorama-nav');

                // Show modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';

                // Show navigation panel if multiple panoramas
                if (panoramaData.length > 1) {
                    navPanel.style.display = 'block';
                    renderPanoramaNavigation();
                } else {
                    navPanel.style.display = 'none';
                }

                // Load the selected panorama
                loadPanorama(currentPanoramaIndex);
            }

            function loadPanorama(index) {
                const panorama = panoramaData[index];
                const titleElement = document.getElementById('panorama-title');

                // Update title
                titleElement.textContent = panorama.title || `Panorama ${index + 1}`;

                // Destroy previous viewer
                if (panoramaViewer) {
                    panoramaViewer.destroy();
                }

                // Initialize Pannellum viewer with the image URL
                const imageUrl = panorama.image.startsWith('http')
                    ? panorama.image
                    : `/storage/${panorama.image}`;

                panoramaViewer = pannellum.viewer('panorama-viewer', {
                    type: 'equirectangular',
                    panorama: imageUrl,
                    autoLoad: true,
                    autoRotate: -2,
                    showZoomCtrl: true,
                    showFullscreenCtrl: true,
                    mouseZoom: true,
                    draggable: true,
                    friction: 0.15,
                    hfov: 100,
                    pitch: 0,
                    yaw: 0
                });

                // Update active state in navigation
                updateNavigationActive();
            }

            function renderPanoramaNavigation() {
                const navItems = document.getElementById('panorama-nav-items');
                navItems.innerHTML = '';

                panoramaData.forEach((panorama, index) => {
                    const imageUrl = panorama.image.startsWith('http')
                        ? panorama.image
                        : `/storage/${panorama.image}`;

                    const item = document.createElement('div');
                    item.className = 'panorama-nav-item';
                    if (index === currentPanoramaIndex) {
                        item.classList.add('active');
                    }

                    item.onclick = () => {
                        currentPanoramaIndex = index;
                        loadPanorama(index);
                    };

                    item.innerHTML = `
                                            <img src="${imageUrl}" alt="${panorama.title || 'Panorama'}" loading="lazy">
                                            <div class="panorama-nav-item-title">${panorama.title || `Panorama ${index + 1}`}</div>
                                        `;

                    navItems.appendChild(item);
                });
            }

            function updateNavigationActive() {
                const items = document.querySelectorAll('.panorama-nav-item');
                items.forEach((item, index) => {
                    if (index === currentPanoramaIndex) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            }

            function closePanoramaViewer() {
                const modal = document.getElementById('panorama-modal');
                modal.classList.remove('active');
                document.body.style.overflow = '';

                // Destroy viewer instance
                if (panoramaViewer) {
                    panoramaViewer.destroy();
                    panoramaViewer = null;
                }

                // Reset data
                panoramaData = [];
                currentPanoramaIndex = 0;
            }

            // Close modal on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && document.getElementById('panorama-modal').classList.contains('active')) {
                    closePanoramaViewer();
                }
            });

            // Close modal on backdrop click
            document.getElementById('panorama-modal')?.addEventListener('click', (e) => {
                if (e.target.id === 'panorama-modal') {
                    closePanoramaViewer();
                }
            });
        </script>

    <?php $__env->stopPush(); ?>


    <div class="product-detail-container">
        <div class="product-grid">

            
            <div class="viewer-wrapper">
                <div class="viewer-container">
                    <!--[if BLOCK]><![endif]--><?php if($model_3d): ?>
                        <canvas id="<?php echo e($viewerId); ?>" class="viewer-canvas"></canvas>
                    <?php else: ?>
                        <div class="viewer-placeholder">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p>3D Model Tidak Tersedia</p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>

                
                <!--[if BLOCK]><![endif]--><?php if($panorama_produks && $panorama_produks->count() > 0): ?>
                    <div class="gallery-section" style="margin-top: 24px;">
                        <div class="section-header">
                            <div class="header-line"></div>
                            <h3 class="section-title">🌄 Galeri Panorama <?php echo e($name); ?></h3>
                        </div>
                        <div class="carousel-container">
                            <div class="carousel-wrapper">
                                <div class="carousel-track" id="panorama-track">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $panorama_produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $panorama): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="carousel-item"
                                            onclick="openPanoramaViewer(<?php echo e($index); ?>, <?php echo e($panorama_produks->toJson()); ?>)">
                                            <img src="<?php echo e(Storage::url($panorama->image)); ?>" alt="Panorama Produk"
                                                loading="lazy">
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if($panorama_produks->count() > 3): ?>
                                <button class="carousel-button carousel-button-prev" onclick="scrollCarousel('panorama', -1)">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7">
                                        </path>
                                    </svg>
                                </button>
                                <button class="carousel-button carousel-button-next" onclick="scrollCarousel('panorama', 1)">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                
                <!--[if BLOCK]><![endif]--><?php if($denah_produks && $denah_produks->count() > 0 && $category === 'rumah'): ?>
                    <div class="gallery-section" style="margin-top: 24px;">
                        <div class="section-header">
                            <div class="header-line"></div>
                            <h3 class="section-title">🏠 Denah Lantai <?php echo e($name); ?></h3>
                        </div>
                        <div class="carousel-container">
                            <div class="carousel-wrapper">
                                <div class="carousel-track" id="denah-track">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $denah_produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $denah): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="carousel-item"
                                            onclick="window.open('<?php echo e(Storage::url($denah->image)); ?>', '_blank')">
                                            <img src="<?php echo e(Storage::url($denah->image)); ?>"
                                                alt="<?php echo e($denah->title ?? 'Denah Lantai'); ?>" loading="lazy">
                                            <!--[if BLOCK]><![endif]--><?php if($denah->title): ?>
                                                <div class="carousel-item-title"><?php echo e($denah->title); ?></div>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <!--[if BLOCK]><![endif]--><?php if($denah_produks->count() > 3): ?>
                                <button class="carousel-button carousel-button-prev" onclick="scrollCarousel('denah', -1)">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <button class="carousel-button carousel-button-next" onclick="scrollCarousel('denah', 1)">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                        </path>
                                    </svg>
                                </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div class="product-panel">
                
                <div class="hero-section">
                    <!--[if BLOCK]><![endif]--><?php if($image): ?>
                        <img src="<?php echo e(Storage::url($image)); ?>" alt="<?php echo e($name); ?>" class="hero-image" loading="lazy">
                        <div class="hero-overlay"></div>
                    <?php else: ?>
                        <div class="hero-placeholder">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p>Gambar tidak tersedia</p>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <div class="status-badge-wrapper">
                        <!--[if BLOCK]><![endif]--><?php if($is_available): ?>
                            <span class="badge badge-available">
                                <span class="status-dot pulse"></span>
                                Tersedia
                            </span>
                        <?php else: ?>
                            <span class="badge badge-unavailable">
                                <span class="status-dot"></span>
                                Tidak Tersedia
                            </span>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                
                <div class="content-area">
                    
                    <div class="title-section">
                        <h1 class="product-title"><?php echo e($name); ?></h1>
                        <div class="tags-container">
                            <span class="tag tag-category"><?php echo e(ucfirst($category)); ?></span>
                            <!--[if BLOCK]><![endif]--><?php if($type): ?>
                                <span class="tag tag-type"><?php echo e(ucfirst($type)); ?></span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>

                    
                    <div class="price-card">
                        <div class="price-bg-1"></div>
                        <div class="price-bg-2"></div>
                        <div class="price-content">
                            <p class="price-label">Harga Produk</p>
                            <p class="price-value">Rp <?php echo e(number_format($price, 0, ',', '.')); ?></p>
                        </div>
                    </div>

                    
                    <div class="description-section">
                        <div class="section-header">
                            <div class="header-line"></div>
                            <h3 class="section-title">Deskripsi Produk</h3>
                        </div>
                        <div class="description-box">
                            <div class="description-content">
                                <?php echo \Illuminate\Support\Str::markdown($description ?? '_Tidak ada deskripsi tersedia untuk produk ini._'); ?>

                            </div>
                        </div>
                    </div>

                    
                    <div class="metadata-section">
                        <div class="metadata-grid">
                            <div class="metadata-item">
                                <div class="metadata-icon icon-blue">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </div>
                                <div class="metadata-text">
                                    <p class="metadata-label">Dibuat</p>
                                    <p class="metadata-value"><?php echo e($created_at->format('d M Y')); ?></p>
                                </div>
                            </div>
                            <div class="metadata-item">
                                <div class="metadata-icon icon-purple">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                        </path>
                                    </svg>
                                </div>
                                <div class="metadata-text">
                                    <p class="metadata-label">Diperbarui</p>
                                    <p class="metadata-value"><?php echo e($updated_at->format('d M Y')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <!--[if BLOCK]><![endif]--><?php if($gambar_produks && $gambar_produks->count() > 0): ?>
                        <div class="gallery-section">
                            <div class="section-header">
                                <div class="header-line"></div>
                                <h3 class="section-title">📸 Galeri Gambar <?php echo e($name); ?></h3>
                            </div>
                            <div class="carousel-container">
                                <div class="carousel-wrapper">
                                    <div class="carousel-track" id="gambar-track">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $gambar_produks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gambar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="carousel-item"
                                                onclick="window.open('<?php echo e(Storage::url($gambar->image)); ?>', '_blank')">
                                                <img src="<?php echo e(Storage::url($gambar->image)); ?>" alt="Gambar Produk"
                                                    loading="lazy">
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php if($gambar_produks->count() > 2): ?>
                                    <button class="carousel-button carousel-button-prev" onclick="scrollCarousel('gambar', -1)">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7">
                                            </path>
                                        </svg>
                                    </button>
                                    <button class="carousel-button carousel-button-next" onclick="scrollCarousel('gambar', 1)">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7">
                                            </path>
                                        </svg>
                                    </button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

        </div>
    </div>

    


    
    <div id="panorama-modal" class="panorama-modal">
        <div class="panorama-modal-content">
            <button class="panorama-close" onclick="closePanoramaViewer()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
            <div class="panorama-title" id="panorama-title">Panorama 360°</div>
            <div id="panorama-viewer" class="panorama-viewer"></div>
            <div class="panorama-nav" id="panorama-nav" style="display: none;">
                <div class="panorama-nav-title">Pilih Panorama</div>
                <div class="panorama-nav-items" id="panorama-nav-items"></div>
            </div>
        </div>
    </div>





</div><?php /**PATH C:\laragon\www\lumin_park_housing\resources\views/filament/infolists/product-detail.blade.php ENDPATH**/ ?>