<?php
    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    include_once '../../config/Database.php';
    include_once '../../models/Quote.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate quote object
    $quote = new Quote($db);

    // Quote query
    if (isset($_GET['author_id'], $_GET['category_id'])) {
        $result = $quote->read($_GET['author_id'], $_GET['category_id']);
    } else if (isset($_GET['author_id'])) {
        $result = $quote->read($_GET['author_id']);
    } else if (isset($_GET['category_id'])) {
        $result = $quote->read($_GET['category_id']);
    } else {
        $result = $quote->read();
    }
    
    // Get row count
    $num = $result->rowCount();

    // Check if any quotes
    if ($num > 0) {
        // Quote array
        $quotes_arr = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $quote_item = array(
                'id' => $id,
                'quote' => html_entity_decode($quote),
                'author' => $author,
                'category' => $category
            );

            // Push to "data"
            array_push($quotes_arr, $quote_item);
        }

        // Turn to JSON & output
        echo json_encode($quotes_arr);

    } else {
        // No Quotes
        echo json_encode(
            array('message' => 'No Quotes Found')
        );
    }