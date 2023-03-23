<?php
    class Quote {
        private $conn;
        private $table = 'quotes';

        // Quote properties
        public $id;
        public $quote;
        public $author_id;
        public $category_id;

        // Constructor with DB
        public function __construct($db) {
            $this->conn = $db;
        }

        // Get Quotes
        public function read($author_id = null, $category_id = null) {
            // Create query
            $query = 'SELECT q.id, q.quote, a.author, c.category FROM ' . $this->table . ' q LEFT JOIN authors a ON q.author_id = a.id LEFT JOIN categories c ON q.category_id = c.id';

            // If author_id is given, add WHERE clause
            if ($author_id && $category_id) {
                $query .= ' WHERE q.author_id = :author_id AND q.category_id = :category_id';
            } else if ($author_id) {
                $query .= ' WHERE q.author_id = :author_id';
            } else if ($category_id) {
                $query .= ' WHERE q.category_id = :category_id';
            }

            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // If author_id is given, bind parameters
            if ($author_id && $category_id) {
                $stmt->bindParam(':author_id', $author_id);
                $stmt->bindParam(':category_id', $category_id);
            } else if ($author_id) {
                $stmt->bindParam(':author_id', $author_id);
            } else if ($category_id) {
                $stmt->bindParam(':category_id', $category_id);
            }

            // Execute query
            $stmt->execute();

            return $stmt;
        }

        // Get Single Quote
        public function read_single() {
            // Create query
            $query = 'SELECT q.id, q.quote, a.author, c.category FROM ' . $this->table . ' q LEFT JOIN authors a ON q.author_id = a.id LEFT JOIN categories c ON q.category_id = c.id WHERE q.id = ?';

            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Bind ID
            $stmt->bindParam(1, $this->id);

            // Execute query
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // Set properties
                $this->id = $row['id'];
                $this->quote = $row['quote'];
                $this->author = $row['author'];
                $this->category = $row['category'];

                return true;
            } else {
                return false;
            }
        }

        // Create Quote
        public function create() {
            // Check for valid author_id
            $query = 'SELECT id FROM authors WHERE id = :author_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':author_id', $this->author_id);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                echo json_encode(
                    array('message' => 'author_id Not Found')
                );
                die();
            }

            // Check for valid category_id
            $query = 'SELECT id FROM categories WHERE id = :category_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                echo json_encode(
                    array('message' => 'category_id Not Found')
                );
                die();
            }

            // Create query
            $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)';
        
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->author_id = htmlspecialchars(strip_tags($this->author_id));
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));

            // Bind data
            $stmt->bindParam(':quote', $this->quote);
            $stmt->bindParam(':author_id', $this->author_id);
            $stmt->bindParam(':category_id', $this->category_id);

            // Execute query
            if ($stmt->execute()) {
                return true;
            }

            // Print error if something goes wrong
            printf("Error: %s.\n", $stmt->error);

            return false;
        }


        // Update Quote
        public function update() {
            // Check for valid author_id
            $query = 'SELECT id FROM authors WHERE id = :author_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':author_id', $this->author_id);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                echo json_encode(
                    array('message' => 'author_id Not Found')
                );
                die();
            }

            // Check for valid category_id
            $query = 'SELECT id FROM categories WHERE id = :category_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                echo json_encode(
                    array('message' => 'category_id Not Found')
                );
                die();
            }

            // Check if quote id is available to update
            $query = 'SELECT id FROM quotes WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                echo json_encode(
                    array('message' => 'No Quotes Found')
                );
                die();
            }

            // Create Query
            $query = 'UPDATE ' . $this->table . ' SET quote = :quote, author_id = :author_id, category_id = :category_id WHERE id = :id';

            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->author_id = htmlspecialchars(strip_tags($this->author_id));
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind data
            $stmt->bindParam(':quote', $this->quote);
            $stmt->bindParam(':author_id', $this->author_id);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':id', $this->id);

            // Execute query
            if ($stmt->execute()) {
                return true;
            }

            // Print error if something goes wrong
            printf("Error: %s.\n", $stmt->error);

            return false;
        }

        // Delete Quote
        public function delete() {
            // Check if quote id is available to delete
            $query = 'SELECT id FROM quotes WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                echo json_encode(
                    array('message' => 'No Quotes Found')
                );
                die();
            }

            // Create query
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind data
            $stmt->bindParam(':id', $this->id);

            // Execute query
            if ($stmt->execute()) {
                return true;
            }

            // Print error if something goes wrong
            printf("Error: %s.\n", $stmt->error);

            return false;
        }
    }