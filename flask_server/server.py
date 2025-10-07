# Flask side snippet with CORS
import requests, cv2, base64
from ultralytics import YOLO
from flask import Flask, request, jsonify
from flask_cors import CORS  # <-- import CORS

# Load model
model = YOLO("best(1).pt")

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # <-- enable CORS for all routes

@app.route('/capture')
def capture():
    user_id = request.args.get('user_id')
    session_id = request.args.get('session_id')

    # Capture frame from ESP32-CAM
    cap = cv2.VideoCapture("http://192.168.137.174:81/stream")
    ret, frame = cap.read()
    cap.release()
    
    if not ret:
        return jsonify({"success": False, "error": "Failed to grab frame"})

    # Run inference
    results = model(frame)
    pred = results[0].names[results[0].probs.top1]
    conf = float(results[0].probs.top1conf)

    # Encode frame to base64
    _, buffer = cv2.imencode('.jpg', frame)
    encoded_image = base64.b64encode(buffer).decode('utf-8')

    # Send to PHP server
    try:
        requests.post("http://localhost/ewaste/receive_inference.php", data={
            "user_id": user_id,
            "session_id": session_id,
            "class_label": pred,
            "confidence": conf,
            "image": encoded_image
        }, timeout=5)
    except Exception as e:
        print("PHP POST failed:", e)

    # Return JSON to frontend
    return jsonify({
        "success": True,
        "result": pred,
        "confidence": conf * 100,
        "image": encoded_image
    })

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=True)
