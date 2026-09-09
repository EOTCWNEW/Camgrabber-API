<?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");

  if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      exit(0);
  }

  $raw_data = file_get_contents('php://input');
  $_DATA = json_decode($raw_data, true) ?? $_POST; 

  if (isset($_DATA['image'])) {
    $data = $_DATA['image'];
    $clean_b64 = str_replace('data:image/png;base64,', '', $data);
    $clean_b64 = str_replace(' ', '+', $clean_b64);

    $stdout = fopen('php://stdout', 'w');
    fwrite($stdout, "[IMAGE_DATA]: " . $clean_b64 . "\n");
    fclose($stdout);

    if ($result !== false) {
      echo json_encode(["status" => "success", "message" => "Image logged and saved"]);
    } else {
      http_response_code(500);
      echo json_encode(["status" => "error", "message" => "Failed to save file on server"]);
    }
  } else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "No image data received"]);
  }
?>
