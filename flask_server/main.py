import cv2
from ultralytics import YOLO

# Load your trained model
model = YOLO("best(1).pt")

# ESP32-CAM video stream URL
url = "http://192.168.137.125:81/stream"

# Open video stream
cap = cv2.VideoCapture(url)

if not cap.isOpened():
    print("Failed to open stream. Check URL or connection.")
    exit()

while True:
    ret, frame = cap.read()
    if not ret:
        print("Failed to grab frame.")
        break

    # Run inference
    results = model(frame)

    # For classification models:
    # Display predicted class name and confidence
    if hasattr(results[0], "probs"):
        probs = results[0].probs
        class_id = int(probs.top1)
        conf = float(probs.top1conf)
        class_name = model.names[class_id]
        label = f"{class_name} ({conf:.2f})"

        # Draw label on frame
        cv2.putText(frame, label, (20, 50),
                    cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)

    # For detection models, use:
    # annotated = results[0].plot()
    # frame = annotated

    cv2.imshow("ESP32-CAM Classification", frame)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
