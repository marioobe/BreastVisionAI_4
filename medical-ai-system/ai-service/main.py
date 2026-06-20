import os
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

import json
import numpy as np
from PIL import Image
import io
import uvicorn
from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse

import keras
from keras.models import load_model
from keras.applications.mobilenet_v2 import preprocess_input

app = FastAPI(
    title="BUSI Classification AI Service",
    description="AI Service for Breast Ultrasound Image Classification using MobileNetV2",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
MODELS_DIR = os.path.join(BASE_DIR, '..', 'models')
MODEL_PATH = os.path.join(MODELS_DIR, 'model.keras')
CLASS_NAMES_PATH = os.path.join(MODELS_DIR, 'class_names.json')
IMG_HEIGHT, IMG_WIDTH = 224, 224

model = None
class_names = []

@app.on_event("startup")
def load_ai_model():
    global model, class_names
    if not os.path.exists(MODEL_PATH):
        raise RuntimeError(f"Model not found at {MODEL_PATH}")
    if not os.path.exists(CLASS_NAMES_PATH):
        raise RuntimeError(f"Class names not found at {CLASS_NAMES_PATH}")
    model = load_model(MODEL_PATH)
    with open(CLASS_NAMES_PATH, 'r') as f:
        class_names = json.load(f)
    print(f"Model loaded from {MODEL_PATH}")
    print(f"Classes: {class_names}")

def preprocess_image(image_bytes: bytes) -> np.ndarray:
    img = Image.open(io.BytesIO(image_bytes)).convert('RGB')
    img = img.resize((IMG_WIDTH, IMG_HEIGHT))
    img_array = np.array(img, dtype=np.float32)
    img_array = np.expand_dims(img_array, axis=0)
    img_array = preprocess_input(img_array)
    return img_array

@app.get("/health")
def health_check():
    return {
        "status": "ok",
        "model_loaded": model is not None,
        "classes": class_names
    }

@app.post("/predict")
async def predict(file: UploadFile = File(...)):
    if model is None:
        raise HTTPException(status_code=503, detail="Model not loaded")
    if file.content_type not in ["image/jpeg", "image/jpg", "image/png"]:
        raise HTTPException(status_code=400, detail="Only JPG, JPEG, and PNG files are allowed")
    try:
        image_bytes = await file.read()
        processed = preprocess_image(image_bytes)
        predictions = model.predict(processed, verbose=0)[0]
        pred_idx = int(np.argmax(predictions))
        confidence = float(predictions[pred_idx]) * 100
        pred_class = class_names[pred_idx]
        probabilities = {
            class_names[i]: round(float(predictions[i]) * 100, 2)
            for i in range(len(class_names))
        }
        return {
            "prediction": pred_class,
            "confidence": round(confidence, 2),
            "probabilities": probabilities
        }
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8001)
