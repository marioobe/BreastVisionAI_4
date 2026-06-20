import os, sys
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
os.chdir(os.path.dirname(os.path.abspath(__file__)))

import tf_keras
from tf_keras.models import load_model
from tf_keras.applications.mobilenet_v2 import preprocess_input
import json
import numpy as np
import PIL.Image as Image
import io

result = []
result.append('Loading model...')
m = load_model('../models/model.keras')
result.append(f'Model OK, layers={len(m.layers)}')

with open('../models/class_names.json') as f:
    names = json.load(f)
result.append(f'Classes: {names}')

img = Image.new('RGB', (224,224), color='red')
arr = np.array(img, dtype=np.float32)
arr = np.expand_dims(arr, axis=0)
arr = preprocess_input(arr)
pred = m.predict(arr, verbose=0)[0]
idx = int(np.ma.array(pred).argmax())
result.append(f'Pred: {names[idx]}, conf: {pred[idx]*100:.2f}%')
result.append('ALL OK!')

with open('test_result.txt', 'w') as f:
    f.write('\n'.join(result))
print('Done - check test_result.txt')
