// Simple Three.js viewer that mounts to #three-viewer and loads a GLTF/GLB model
import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader';

function initViewer(container, modelUrl) {
    const width = container.clientWidth;
    const height = container.clientHeight;

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf3f4f6);

    const camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
    camera.position.set(0, 1.5, 3);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(width, height);
    renderer.setPixelRatio(window.devicePixelRatio ? window.devicePixelRatio : 1);
    container.appendChild(renderer.domElement);

    const hemi = new THREE.HemisphereLight(0xffffff, 0x444444, 1.0);
    hemi.position.set(0, 20, 0);
    scene.add(hemi);

    const dir = new THREE.DirectionalLight(0xffffff, 0.8);
    dir.position.set(5, 10, 7.5);
    scene.add(dir);

    const loader = new GLTFLoader();
    if (modelUrl) {
        loader.load(modelUrl, (gltf) => {
            const root = gltf.scene || gltf.scenes[0];
            root.position.set(0, 0, 0);
            // Scale down if it's big
            root.scale.set(1,1,1);
            scene.add(root);
        }, undefined, (err) => {
            console.error('Failed to load 3D model', err);
        });
    }

    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }
    animate();

    // simple resize handling
    window.addEventListener('resize', () => {
        const w = container.clientWidth;
        const h = container.clientHeight;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('three-viewer');
    if (!el) return;
    const model = el.getAttribute('data-model');
    initViewer(el, model || null);
});
