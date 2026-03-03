<?php

class Dbh {

    // Attributes
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "materialcodb";

    // Methods for connection
    protected function connection(){

        try{
            // Setting up DB credentials
            $dsn = 'mysql:host='. $this->host . ';dbname=' . $this->db;

            // Setup PDO
            $pdo = new PDO($dsn, $this->user, $this->pass);

            // Setting fetch data to be ASSOC
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Return PDO
            return $pdo;

        }catch(PDOException $e){

            // Display Error in connection
            echo "Error:" . $e->getMessage() . "<br>";
            die();

        }
    }
}