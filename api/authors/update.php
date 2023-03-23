<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Author.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate author object
    $author = new Author($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Return message if any required parameter is missing
    if (empty($data->id) || empty($data->author)) {
        echo json_encode(
            array('message' => 'Missing Required Parameters')
        );
        die();
    }

    // Set ID to update
    $author->id = $data->id;

    $author->author = $data->author;

    // Update author
    if ($author->update()) {
        $author_arr = array(
            'id' => $author->id,
            'author' => $author->author
        );
        echo json_encode($author_arr);
    }