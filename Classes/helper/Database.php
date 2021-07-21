<?php
require_once "config.php";

class Database
{
    private $conn;

    /**
     * @return mixed
     */
    public function getConnection(){
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" .DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }catch (PDOException $exception){
            echo "Database could not be connected :" . $exception->getMessage();
        }
        return $this->conn;
    }

    public function truncate(){
        $stm = $this->conn->prepare("truncate table student_action");
        $stm->execute();
        $stm = $this->conn->prepare("truncate table lectures");
        $stm->execute();
    }
}