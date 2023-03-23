<?php
    class Category {
        private $conn;
        private $table = 'categories';

        // Category properties
        public $id;
        public $category;

        // Constructor with DB
        public function __construct($db) {
            $this->conn = $db;
        }

        // Get Categories
        public function read() {
            // Create query
            $query = 'SELECT id, category FROM ' . $this->table;

            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Execute query
            $stmt->execute();

            return $stmt;
        }

        // Get Single Category
        public function read_single() {
            // Create query
            $query = 'SELECT id, category FROM ' . $this->table. ' WHERE id = ?';

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
                $this->category = $row['category'];
                
                return true;
            } else {
                return false;
            }
        }

        // Create Category
        public function create() {
            // Create query
            $query = 'INSERT INTO ' . $this->table . ' (category) VALUES (:category)';
        
            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->category_id = htmlspecialchars(strip_tags($this->category));

            // Bind data
            $stmt->bindParam(':category', $this->category);

            // Execute query
            if ($stmt->execute()) {
                return true;
            }

            // Print error if something goes wrong
            printf("Error: %s.\n", $stmt->error);

            return false;
        }


        // Update Category
        public function update() {
            // Check for valid category id
            $query = 'SELECT id FROM categories WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                echo json_encode(
                    array('message' => 'category_id Not Found')
                );
                die();
            }

            // Create Query
            $query = 'UPDATE ' . $this->table . ' SET category = :category WHERE id = :id';

            // Prepare statement
            $stmt = $this->conn->prepare($query);

            // Clean data
            $this->category = htmlspecialchars(strip_tags($this->category));
            $this->id = htmlspecialchars(strip_tags($this->id));

            // Bind data
            $stmt->bindParam(':category', $this->category);
            $stmt->bindParam(':id', $this->id);

            // Execute query
            if ($stmt->execute()) {
                return true;
            }

            // Print error if something goes wrong
            printf("Error: %s.\n", $stmt->error);

            return false;
        }

        // Delete Category
        public function delete() {
            // Check if category id is available to delete
            $query = 'SELECT id FROM categories WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                echo json_encode(
                    array('message' => 'No Categories Found')
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