<?php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);

    exit;
}

$apiUrl = 'http://23.118.218.91:3000/api/contact';

$postFields = [];

foreach ($_POST as $key => $value) {
    $postFields[$key] = $value;
}

/*
|--------------------------------------------------------------------------
| Handle uploaded photo
|--------------------------------------------------------------------------
*/

if (
    isset($_FILES['photos']) &&
    $_FILES['photos']['error'] === UPLOAD_ERR_OK
) {

    $postFields['photos'] = new CURLFile(
        $_FILES['photos']['tmp_name'],
        $_FILES['photos']['type'],
        $_FILES['photos']['name']
    );
}

/*
|--------------------------------------------------------------------------
| Send request to existing Node API
|--------------------------------------------------------------------------
*/

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {

    $error = curl_error($ch);

    curl_close($ch);

    http_response_code(500);

    echo json_encode([
        'success' => false,
        'message' => 'Unable to connect to contact server.',
        'error' => $error
    ]);

    exit;
}

curl_close($ch);

http_response_code($httpCode ?: 200);

echo $response;