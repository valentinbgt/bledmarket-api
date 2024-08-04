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

            $encryptKey = Functions::randKey(64);



            $file_date = time();

            $file_hash = hash_file('sha256', $tmp_name);

            // echo "tmp_name: $tmp_name<br>\n";
            // echo "publicKey: $publicKey<br>\n";
            // echo "databaseId: $databaseId<br>\n";
            // echo "file_name: $file_name<br>\n";
            // echo "file_type: $file_type<br>\n";
            // echo "file_size: $file_size<br>\n";
            // echo "file_date: $file_date<br>\n";
            // echo "path: $path<br>\n";
            // echo "repertory: $repertory<br>\n";
            // echo "userid: $user->id<br>\n";
            // echo "file_hash: $file_hash<br>\n";

            $sql = "INSERT INTO `files`(`file_public_id`, `file_db_id`, `file_encrypt_key`, `file_repertory`, `file_name`, `file_path`, `file_size`, `file_date`, `file_is_folder`, `file_type`, `file_hash`, `user_id`) VALUES (:file_public_id, :file_db_id, :file_encrypt_key, :file_repertory, :file_name, :file_path, :file_size, :file_date, :file_is_folder, :file_type, :file_hash, :user_id)";

            $db = new Database;
            $query = $db->prepare($sql);
            
            $query->bindValue(':file_public_id', $publicKey);
            $query->bindValue(':file_db_id', $databaseId);
            $query->bindValue(':file_encrypt_key', $encryptKey);
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
            
            $this->encrypt($tmp_name, DB_FILES_PATH . '/' . $databaseId, $encryptKey);

            if(file_exists(DB_FILES_PATH . '/' . $databaseId)){
                $sql = "UPDATE `files` SET `file_is_uploaded`=1 WHERE `file_db_id`=:file_db_id";
                $query = $db->prepare($sql);
                $query->bindValue(':file_db_id', $databaseId);
                $query->execute();
                return true;
            }else{
                (new Api())->error(22, "Le fichier est introuvable dans la base de données après son déplacement.");
            }
        }

        public function encrypt(string $inputFilePath, string $outputFilePath, string $key) {
            $ivLength = openssl_cipher_iv_length('aes-256-cbc');
            $iv = random_bytes($ivLength);

            $fpIn = fopen($inputFilePath, 'rb');
            $fpOut = fopen($outputFilePath, 'wb');
            fwrite($fpOut, $iv);

            $blockSize = 4 * 1024; // 4KB

            while (!feof($fpIn)) {
                $plaintext = fread($fpIn, $blockSize);
                $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

                if ($ciphertext) {
                    $iv = substr($ciphertext, - $ivLength); // Mise à jour de l'IV avec le dernier bloc chiffré
                    fwrite($fpOut, $ciphertext);
                }
            }

            fclose($fpIn);
            fclose($fpOut);
        }

        public function download(string $fileId, bool $display = false):void {
            global $db, $api;
            function cleanFileName($string) { return preg_replace('/[^A-Za-z0-9. éèà&\-]/', '', $string);};

            $fileInfos = $db->fetch('files', 'file_public_id', $fileId);

            if(!$fileInfos) $api->error(28);

            $databaseId = $fileInfos['file_db_id'];
            $fileName = $fileInfos['file_name'];
            $key = $fileInfos['file_encrypt_key'];
            $fileSize = $fileInfos['file_size'];
            $fileType = $fileInfos['file_type'];

            $filePath = DB_FILES_PATH . '/' . $databaseId;

            ini_set('max_execution_time', '0');
            session_write_close();
            $attachment = (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) ? "" : " attachment"; 
        
            if($display && Functions::isMIMEDisplayable($fileType)){
                header("Content-Type: $fileType");
                header("Content-Disposition: inline; filename=" . cleanFileName($fileName) . ";");
            }else {
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: $attachment; filename=".cleanFileName($fileName).";");
            }
            header('Content-Length: ' . $fileSize);
            header('Content-Transfer-Encoding: binary');

            @ob_end_clean();
            $options = array(
                "ssl"=>array(
                    "verify_peer"=>false,
                    "verify_peer_name"=>false,
                ),
            );
            $context  = stream_context_create($options);
            $fileIn = fopen($filePath, 'rb', false, $context);
            
            $ivLength = openssl_cipher_iv_length('aes-256-cbc');
            $iv = fread($fileIn, $ivLength);
        
            $blockSize = 4 * 1024;//4KB
            $buffer = '';
        
            ob_start();
            while (!feof($fileIn)) {
                $ciphertext = fread($fileIn, $blockSize + $ivLength);
                $buffer = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
                echo $buffer;
                ob_flush();
                flush();
                $iv = substr($ciphertext, -$ivLength);
            }
        
            fclose($fileIn);
            exit;
        }


        public function delete(string $file_public_id){
            //delete permanently without trash
            global $db, $api;

            $result = $db->fetch('files', 'file_public_id', $file_public_id);
            $databaseId = $result["file_db_id"];

            if(!$result['file_is_folder']){
                //la cible est un fichier -> la supprimer
                $file_path = DB_FILES_PATH . '/' . $databaseId;
                if(file_exists($file_path)){
                    unlink($file_path);
                }
            }else{
                //la cible est un dossier -> vérifier s'il est vide
                $targetPath = $result['file_path'];
                if(!str_ends_with($targetPath, '/')) $targetPath .= '/';
                $targetPath .= $file_public_id;

                $isFolderFilled = $db->fetch('files', 'file_path', $targetPath);

                if($isFolderFilled !== false) $api->error(27, "Videz le dossier avant de le supprimer.");
            }

            $sql = "DELETE FROM `files` WHERE `file_public_id`=:file_public_id";
            $query = $db->prepare($sql);
            $query->bindValue(':file_public_id', $file_public_id);
            $query->execute();
        }

        public function move(string $file_public_id, string $file_path){
            global $db;

            $sql = "UPDATE `files` SET `file_path`=:file_path WHERE `file_public_id`=:file_public_id";

            $query = $db->prepare($sql);

            $query->bindValue(':file_path', $file_path);
            $query->bindValue(':file_public_id', $file_public_id);

            $query->execute();
        }

        public function newFolder($repertory, string $path):mixed {
            global $db, $api, $user;
            //CHECK IF PATH IS CORRECT

            if($path != "/"){
                $explode = explode('/', $path);
                $parentName = array_pop($explode);
                $parentPath = implode('/', $explode);
                if(empty($parentPath)) $parentPath = '/'; 

                $parent = $db->fetch('files', 'file_public_id', $parentName);

                if(!is_array($parent)) $api->error(23, "Le dossier parent est introuvable.");

                if($parent["file_is_folder"] != 1) $api->error(24, "Le chemin n'est pas un dossier.");

                if($parent["file_path"] != $parentPath) $api->error(25, "La destination est incorrecte.");
            }


            //create folder

            //generate rand public key
            $allPubliKeys = $this->db->selectAllRowsOfColumn('files', 'file_public_id');
            if(!$allPubliKeys) $allPubliKeys = array();
            $publicKey = Functions::randKey(32, $allPubliKeys);

            $sql = "INSERT INTO `files`(`file_public_id`, `file_repertory`, `file_name`, `file_path`, `file_size`, `file_date`, `file_is_folder`, `user_id`) VALUES (:file_public_id, :file_repertory, :file_name, :file_path, :file_size, :file_date, :file_is_folder, :user_id)";

            $query = $db->prepare($sql);
            
            $query->bindValue(':file_public_id', $publicKey);
            $query->bindValue(':file_repertory', $repertory);
            $query->bindValue(':file_name', "Nouveau dossier");
            $query->bindValue(':file_path', $path);
            $query->bindValue(':file_size', 0);
            $query->bindValue(':file_date', time());
            $query->bindValue(':file_is_folder', 1);
            $query->bindValue(':user_id', $user->id);

            $query->execute();

            $newFolder = $db->fetch('files', 'file_public_id', $publicKey);
            if(!is_array($newFolder)) return false;

            else return $publicKey;
        }

        public function rename(string $fileId, string $newName):bool {
            global $db;
            $file = $db->fetch('files', 'file_public_id', $fileId);

            $extention = '';
            if(!$file['file_is_folder']){
                $oldName = $file['file_name'];
                $nameExploded = explode('.', $oldName);
                $extention = '.' . $nameExploded[count($nameExploded) - 1];
            }

            $newName .= $extention;

            //UPDATE FILE
            $sql = "UPDATE `files` SET `file_name`=:newName WHERE `file_public_id`=:fileId";

            $query = $db->prepare($sql);

            $query->bindValue(':newName', $newName);
            $query->bindValue(':fileId', $fileId);

            $query->execute();

            $updatedFile = $db->fetch('files', 'file_public_id', $fileId);

            if($updatedFile['file_name'] == $newName) {
                return true;
            }else{
                return false;
            }
        }
    }