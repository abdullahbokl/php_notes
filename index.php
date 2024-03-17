<?php

require_once 'helpers/constants.php';
require_once 'connect.php';
require_once 'helpers/helper_methods.php';
require_once 'helpers/route_handler.php';
require_once 'helpers/filesUploader.php';

$routes = [
    // Auth routes
    '/notes/auth/signup' => [
        'POST' => 'auth/signup.php',
    ],
    '/notes/auth/login' => [
        'POST' => 'auth/login.php',
    ],

    // notes routes
    '/notes/notes/create-note' => [
        'POST' => 'notes/create_note.php',
    ],
    '/notes/notes/get-all-notes' => [
        'GET' => 'notes/get_all_notes.php',
    ],
    '/notes/notes/get-note-by-id' => [
        'POST' => 'notes/get_note_by_id.php',
    ],
    '/notes/notes/update-note' => [
        'POST' => 'notes/update_note.php',
    ],
    '/notes/notes/delete-note' => [
        'POST' => 'notes/delete_note.php',
    ],
];

// Create Router instance and handle request
$router = new Router($routes);
$router->handleRequest();