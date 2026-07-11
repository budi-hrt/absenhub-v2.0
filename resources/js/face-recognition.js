import * as faceapi from 'face-api.js';

let modelsLoaded = false;

export async function initFaceModels() {
    if (modelsLoaded) return;
    await Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
        faceapi.nets.faceLandmark68TinyNet.loadFromUri('/models'),
        faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
    ]);
    modelsLoaded = true;
}

export async function getDescriptorFromBlob(blob) {
    await initFaceModels();
    const img = await faceapi.bufferToImage(blob);
    const result = await faceapi
        .detectSingleFace(img, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks(true)
        .withFaceDescriptor();

    if (!result) throw new Error('Wajah tidak terdeteksi');
    return Array.from(result.descriptor);
}
