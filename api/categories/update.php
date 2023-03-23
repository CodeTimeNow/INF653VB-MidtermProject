<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods,Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Category.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate category object
    $category = new Category($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Return message if any required parameter is missing
    if (empty($data->id) || empty($data->category)) {
        echo json_encode(
            array('message' => 'Missing Required Parameters')
        );
        die();
    }

    // Set ID to update
    $category->id = $data->id;

    $category->category = $data->category;

    // Update category
    if ($category->update()) {
        $category_arr = array(
            'id' => $category->id,
            'category' => $category->category
        );
        echo json_encode($category_arr);
    }