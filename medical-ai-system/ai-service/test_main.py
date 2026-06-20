"""
Tests for FastAPI AI Service (BUSI Classification)
"""
import io
import json
import os
from unittest.mock import MagicMock, patch
from fastapi.testclient import TestClient

os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'

IMG_HEIGHT = 224
IMG_WIDTH = 224

def create_test_image():
    """Create a minimal valid JPEG image for testing."""
    from PIL import Image
    img = Image.new('RGB', (IMG_WIDTH, IMG_HEIGHT), color='gray')
    buf = io.BytesIO()
    img.save(buf, format='JPEG')
    buf.seek(0)
    return buf


# ──────────────────────────────────────────────
# Test: model loaded (mock model & class_names)
# ──────────────────────────────────────────────
@patch('main.model', MagicMock())
@patch('main.class_names', ['Benign', 'Malignant', 'Normal'])
def test_health_endpoint():
    from main import app
    client = TestClient(app)
    resp = client.get('/health')
    assert resp.status_code == 200
    data = resp.json()
    assert data['status'] == 'ok'
    assert data['model_loaded'] is True
    assert 'Benign' in data['classes']


@patch('main.model', None)
@patch('main.class_names', [])
def test_health_endpoint_model_not_loaded():
    from main import app
    client = TestClient(app)
    resp = client.get('/health')
    assert resp.status_code == 200
    assert resp.json()['model_loaded'] is False


# ──────────────────────────────────────────────
# Test: predict endpoint
# ──────────────────────────────────────────────
@patch('main.model', MagicMock())
@patch('main.class_names', ['Benign', 'Malignant', 'Normal'])
def test_predict_success():
    """Mock model.predict() to return a known result."""
    import numpy as np
    from main import app

    mock_model = MagicMock()
    mock_model.predict.return_value = np.array([[0.10, 0.85, 0.05]])
    app.state.mock_model = mock_model

    with patch('main.model', mock_model):
        client = TestClient(app)
        img_bytes = create_test_image().read()
        resp = client.post('/predict', files={'file': ('test.jpg', img_bytes, 'image/jpeg')})

    assert resp.status_code == 200
    data = resp.json()
    assert 'prediction' in data
    assert 'confidence' in data
    assert 'probabilities' in data
    assert data['prediction'] == 'Malignant'
    assert data['confidence'] > 0
    assert len(data['probabilities']) == 3


@patch('main.model', MagicMock())
@patch('main.class_names', ['Benign', 'Malignant', 'Normal'])
def test_predict_invalid_file_type():
    from main import app
    client = TestClient(app)
    resp = client.post('/predict', files={'file': ('test.txt', b'not-an-image', 'text/plain')})
    assert resp.status_code == 400
    assert 'Only JPG' in resp.json()['detail'] or 'allowed' in resp.json()['detail']


@patch('main.model', None)
@patch('main.class_names', [])
def test_predict_model_not_loaded():
    from main import app
    client = TestClient(app)
    img_bytes = create_test_image().read()
    resp = client.post('/predict', files={'file': ('test.jpg', img_bytes, 'image/jpeg')})
    assert resp.status_code == 503
    assert 'Model not loaded' in resp.json()['detail']


@patch('main.model', MagicMock())
@patch('main.class_names', ['Benign', 'Malignant', 'Normal'])
def test_predict_no_file():
    from main import app
    client = TestClient(app)
    resp = client.post('/predict')
    assert resp.status_code == 422


# ──────────────────────────────────────────────
# Test: CORS headers
# ──────────────────────────────────────────────
@patch('main.model', MagicMock())
@patch('main.class_names', ['Benign'])
def test_cors_headers():
    from main import app
    client = TestClient(app)
    resp = client.options('/health', headers={
        'Origin': 'http://localhost',
        'Access-Control-Request-Method': 'GET',
    })
    assert resp.headers.get('access-control-allow-origin') == '*'


# ──────────────────────────────────────────────
# Test: preprocessing function
# ──────────────────────────────────────────────
@patch('main.model', MagicMock())
@patch('main.class_names', ['Benign', 'Malignant', 'Normal'])
def test_preprocess_image_output_shape():
    from main import preprocess_image
    import numpy as np
    img_bytes = create_test_image().read()
    arr = preprocess_image(img_bytes)
    assert isinstance(arr, np.ndarray)
    assert arr.shape == (1, IMG_HEIGHT, IMG_WIDTH, 3)
    assert arr.dtype == np.float32


# ──────────────────────────────────────────────
# Test: JSON response structure matches spec
# ──────────────────────────────────────────────
@patch('main.model', MagicMock())
@patch('main.class_names', ['Benign', 'Malignant', 'Normal'])
def test_predict_response_structure():
    import numpy as np
    from main import app

    mock_model = MagicMock()
    mock_model.predict.return_value = np.array([[0.95, 0.03, 0.02]])

    with patch('main.model', mock_model):
        client = TestClient(app)
        img_bytes = create_test_image().read()
        resp = client.post('/predict', files={'file': ('test.jpg', img_bytes, 'image/jpeg')})

    data = resp.json()
    assert list(data.keys()) == ['prediction', 'confidence', 'probabilities']
    assert isinstance(data['prediction'], str)
    assert isinstance(data['confidence'], float)
    assert isinstance(data['probabilities'], dict)
    for cls in ['Benign', 'Malignant', 'Normal']:
        assert cls in data['probabilities']
        assert isinstance(data['probabilities'][cls], float)
