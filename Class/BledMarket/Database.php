<?php
    Class Database{

        private object $database;

        public function __construct(){
            $this->connect();
        }

        private function connect():void {
            global $api;

            try {
                $pdo = new PDO("mysql:host=". DB_HOST .";dbname=". DB_NAME, DB_USER, DB_PASSWORD);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->database = $pdo;
            }
            catch (PDOException $e) {
                $api->error(10, $e->getMessage());
            }
        }

        public function prepare(string $sql):object{
            return $this->database->prepare($sql);
        }

        public function fetch(string $table, string $param, mixed $value):mixed {
            $sql = "SELECT * FROM `$table` WHERE $param=:value";
            $query = $this->prepare($sql);

            $query->bindParam(':value', $value);

            $query->execute();
            $res = $query->fetch();

            return $res;
        }

        public function selectAllRowsOfColumn(string $table, string $colomn):mixed {
            $sql = "SELECT `$colomn` FROM `$table` WHERE 1";
            $query = $this->prepare($sql);

            $query->execute();
            $res = $query->fetch();

            return $res;
        }

    }