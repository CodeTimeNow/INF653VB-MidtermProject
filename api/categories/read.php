<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../config/Database.php';
    include_once '../../models/Category.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate category object
    $category = new Category($db);

    // category query
    $result = $category->read();
    
    // Get row count
    $num = $result->rowCount();

    // Check if any categorys
    if ($num > 0) {
        // category array
        $categories_arr = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $category_item = array(
                'id' => $id,
                'category' => html_entity_decode($category)
            );

            // Push to "data"
            array_push($categories_arr, $category_item);
        }

        // Turn to JSON & output
        echo json_encode($categories_arr);

    } else {
        // No categories
        echo json_encode(
            array('message' => 'category_id Not Found')
        );
    }