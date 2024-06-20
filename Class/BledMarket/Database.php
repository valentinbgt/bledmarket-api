<?php
    Class Database{

        private object $database;

        public function __construct(){
            $this->connect();
        }

        private function connect():void {
            try {
                $pdo = new PDO("mysql:host=". DB_HOST .";dbname=". DB_NAME, DB_USER, DB_PASSWORD);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->database = $pdo;
            }
            catch (PDOException $e) {
                (new Api())->error(10, $e->getMessage());
            }
        }

    }