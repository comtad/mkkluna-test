<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'raw_php_success',
    'get_params' => $_GET,
    'server_info' => [
        'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? null,
        'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? null,
        'REDIRECT_STATUS' => $_SERVER['REDIRECT_STATUS'] ?? null,
        'HTTP_ACCEPT' => $_SERVER['HTTP_ACCEPT'] ?? null
    ]
]);