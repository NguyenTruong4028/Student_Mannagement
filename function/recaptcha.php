<?php
function verifyRecaptcha($response) {
    $secret = '6LeN0zcrAAAAAF_uiw1GQu0w-EqxenNFGcmeAoXo'; // Thay bằng khóa bí mật reCAPTCHA
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret,
        'response' => $response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $resultJson = json_decode($result);
    return $resultJson->success;
}
?>