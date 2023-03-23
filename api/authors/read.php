<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../config/Database.php';
    include_once '../../models/Author.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate Author object
    $author = new Author($db);

    // Author query
    $result = $author->read();
    
    // Get row count
    $num = $result->rowCount();

    // Check if any Authors
    if ($num > 0) {
        // Author array
        $authors_arr = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $author_item = array(
                'id' => $id,
                'author' => html_entity_decode($author)
            );

            // Push to "data"
            array_push($authors_arr, $author_item);
        }

        // Turn to JSON & output
        echo json_encode($authors_arr);

    } else {
        // No Authors
        echo json_encode(
            array('message' => 'author_id Not Found')
        );
    }