<?php

namespace App\Common;

require_once __DIR__ . '/../Config/Path.php';

final class JsonResponse
{
    public static function success(): never
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'ok']);
        exit();
    }
    public static function error(int $responseCode, string $jsonCode): never
    {
        http_response_code($responseCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'code' => $jsonCode
        ]);
        exit();
    }
}
