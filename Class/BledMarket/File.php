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


        public function upload(string $tmp_name, string $file_name, string $file_type, int $file_size, string $path, string $repertory){

            //generate rand public key
            $allPubliKeys = $this->db->selectAllRowsOfColumn('files', 'file_public_id');
            if(!$allPubliKeys) $allPubliKeys = array();
            $publicKey = Functions::randKey(64, $allPubliKeys);

            //generate rand db id
            $allDatabaseIds = $this->db->selectAllRowsOfColumn('files', 'file_db_id');
            if(!$allDatabaseIds) $allDatabaseIds = array();
            $databaseId = Functions::randKey(64, $allDatabaseIds);



            //file date
            $file_date = time();

            echo "tmp_name: $tmp_name<br>\n";
            echo "publicKey: $publicKey<br>\n";
            echo "databaseId: $databaseId<br>\n";
            echo "file_name: $file_name<br>\n";
            echo "file_type: $file_type<br>\n";
            echo "file_size: $file_size<br>\n";
            echo "file_date: $file_date<br>\n";
            echo "path: $path<br>\n";
            echo "repertory: $repertory<br>\n";
        }

    }