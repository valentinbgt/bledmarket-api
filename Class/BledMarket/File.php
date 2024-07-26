<?php
    Class File{

        public ?object $db = null;

        public function __construct(){
            $this->db = new Database();
        }

        public function get($user, $repertory):array {
            if($repertory != "public") $user->require();

            (new Api)->checkRepertory($repertory);

            //requete sql
            if($repertory == 'public') {

                $sql = "SELECT `file_public_id`, `file_is_uploaded`, `file_name`, `file_path`, `file_size`, `file_date`, `file_is_folder`, `file_is_shared`, `file_shared_id`, `file_is_trashed`, `file_trash_date`, `file_type`, `user_id` FROM `files` WHERE `file_repertory`='public';";
                $query = $this->db->prepare($sql);

            }else if ($repertory == 'private') {

                $sql = "SELECT `file_public_id`, `file_is_uploaded`, `file_name`, `file_path`, `file_size`, `file_date`, `file_is_folder`, `file_is_shared`, `file_shared_id`, `file_is_trashed`, `file_trash_date`, `file_type`, `user_id` FROM `files` WHERE `user_id`=:userid AND `file_repertory`='private';";
                $query = $this->db->prepare($sql);
                $query->bindParam('userid', $user->id);

            }

            $query->execute();

            $res = $query->fetchAll(PDO::FETCH_ASSOC);
            $fileList = $res;
            
            return $fileList;
        }


        public function upload(string $tmp_name, string $file_name, string $file_type, int $file_size, string $path, string $repertory, object $user){

            //generate rand public key
            $allPubliKeys = $this->db->selectAllRowsOfColumn('files', 'file_public_id');
            if(!$allPubliKeys) $allPubliKeys = array();
            $publicKey = Functions::randKey(32, $allPubliKeys);

            //generate rand db id
            $allDatabaseIds = $this->db->selectAllRowsOfColumn('files', 'file_db_id');
            if(!$allDatabaseIds) $allDatabaseIds = array();
            $databaseId = Functions::randKey(32, $allDatabaseIds);

            $cypherKey = Functions::randKey(64);



            $file_date = time();

            $file_hash = hash_file('sha256', $tmp_name);

            echo "tmp_name: $tmp_name<br>\n";
            echo "publicKey: $publicKey<br>\n";
            echo "databaseId: $databaseId<br>\n";
            echo "file_name: $file_name<br>\n";
            echo "file_type: $file_type<br>\n";
            echo "file_size: $file_size<br>\n";
            echo "file_date: $file_date<br>\n";
            echo "path: $path<br>\n";
            echo "repertory: $repertory<br>\n";
            echo "userid: $user->id<br>\n";
            echo "file_hash: $file_hash<br>\n";

            $sql = "INSERT INTO `files`(`file_public_id`, `file_db_id`, `file_cypher_key`, `file_repertory`, `file_name`, `file_path`, `file_size`, `file_date`, `file_is_folder`, `file_type`, `file_hash`, `user_id`) VALUES (:file_public_id, :file_db_id, :file_cypher_key, :file_repertory, :file_name, :file_path, :file_size, :file_date, :file_is_folder, :file_type, :file_hash, :user_id)";

            $db = new Database;
            $query = $db->prepare($sql);
            
            $query->bindValue(':file_public_id', $publicKey);
            $query->bindValue(':file_db_id', $databaseId);
            $query->bindValue(':file_cypher_key', $cypherKey);
            $query->bindValue(':file_repertory', $repertory);
            $query->bindValue(':file_name', $file_name);
            $query->bindValue(':file_path', $path);
            $query->bindValue(':file_size', $file_size);
            $query->bindValue(':file_date', $file_date);
            $query->bindValue(':file_is_folder', 0);
            $query->bindValue(':file_type', $file_type);
            $query->bindValue(':file_hash', $file_hash);
            $query->bindValue(':user_id', $user->id);

            $query->execute();

            if(empty(DB_FILES_PATH))(new Api())->error(3, "DB_FILES_PATH");
            //ENCRYPT THE FILE !!!
            if(@move_uploaded_file($tmp_name, DB_FILES_PATH . '/' . $databaseId)){
                $sql = "UPDATE `files` SET `file_is_uploaded`=1 WHERE `file_db_id`=$databaseId";
            }else{
                $error = error_get_last();
                print_r($error);
                die();
                (new Api())->error(22, "Échec de déplacement du fichier");
            };
        }

    }