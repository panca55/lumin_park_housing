@php $viewerId = 'viewer-3d-' . uniqid(); @endphp

<style scoped>
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

    .placeholder-wrapper {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        color: #9ca3af;
    }

    .placeholder-icon {
        width: 96px;
        height: 96px;
    }

    .placeholder-message {
        font-size: 18px;
        font-weight: 600;
    }
</style>

<div class="viewer-wrapper">
    <div class="viewer-container">
        @if($model_3d)
            <canvas id="{{ $viewerId }}" class="viewer-canvas"></canvas>
        @else
            <div class="placeholder-wrapper">
                <svg class="placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <p class="placeholder-message">3D Model Tidak Tersedia</p>
            </div>
        @endif
    </div>
</div>

@if($model_3d)
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>

    <script>
        (() => {
            const canvas = document.getElementById('{{ $viewerId }}');
            if (!canvas) return;

            const renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5));
            renderer.setSize(canvas.clientWidth, canvas.clientHeight);
            renderer.shadowMap.enabled = false;

            const scene = new THREE.Scene();
            scene.background = new THREE.Color(0xf1f5f9);

            const camera = new THREE.PerspectiveCamera(45, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);

            const controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;

            scene.add(new THREE.AmbientLight(0xffffff, 0.7));
            const light = new THREE.DirectionalLight(0xffffff, 0.8);
            light.position.set(5, 5, 5);
            scene.add(light);

            const loader = new THREE.GLTFLoader();
            loader.load('{{ $model_3d }}', (gltf) => {
                const model = gltf.scene;
                scene.add(model);

                const box = new THREE.Box3().setFromObject(model);
                const size = box.getSize(new THREE.Vector3()).length();
                const center = box.getCenter(new THREE.Vector3());

                model.position.sub(center);
                camera.position.set(0, size * 0.6, size);
                controls.target.set(0, 0, 0);

                render();
            });

            let needsRender = true;
            function render() {
                if (!needsRender) return;
                renderer.render(scene, camera);
                needsRender = false;
            }

            controls.addEventListener('change', () => {
                needsRender = true;
                render();
            });

            window.addEventListener('resize', () => {
                const w = canvas.clientWidth;
                const h = canvas.clientHeight;
                camera.aspect = w / h;
                camera.updateProjectionMatrix();
                renderer.setSize(w, h);
                needsRender = true;
                render();
            });
        })();
    </script>
@endif