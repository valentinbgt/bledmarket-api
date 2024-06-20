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

    }