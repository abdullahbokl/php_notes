<?php

abstract class HelperMethods {

    static function secureRequest($string): string {
        return htmlspecialchars(strip_tags($_POST[$string]));
    }

    static function sendResponse($data, $message, $statusCode): never {
        header('Content-Type: application/json');
        $response = [
            'data' => $data,
            'message' => $message,
            'status' => $statusCode
        ];

        echo json_encode($response);
        exit;
    }
}
