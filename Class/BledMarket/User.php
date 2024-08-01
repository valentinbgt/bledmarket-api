<?php
    Class User{

        private object $db;
        public ?int $id = null;
        public ?string $type = null;
        public ?string $name = null;
        public ?string $display_name = null;
        public ?string $email = null;
        public ?string $about = null;

        public function __construct(){
            
            $this->db = new Database();

            if(isset($_SESSION["userId"])){
                $this->id = $_SESSION["userId"];
                $this->connect();
            }

        }

        private function connect(){
            global $api;

            $user = $this->db->fetch('users', 'user_id', $this->id);
            
            if(!$user) {
                $this->logout();
                $api->error(15, "Utilisateur introuvable, vous avez été déconnecté.");
            }

            extract($user);

            $this->type = $user_type;
            $this->name = $user_name;
            $this->display_name = $user_display_name;
            $this->email = $user_email;
            $this->about = $user_about;
        }

        public function signup(string $name, string $displayName, string $email, string $password):bool {
            global $db;

            $password_hash = $this->passwordHash($password);

            $sql = "INSERT INTO `users`(`user_type`, `user_name`, `user_display_name`, `user_email`, `user_pwd`) VALUES (:user_type, :user_name, :user_display_name, :user_email, :user_pwd)";

            $query = $db->prepare($sql);

            $query->bindValue('user_type', "default");
            $query->bindValue('user_name', $name);
            $query->bindValue('user_display_name', $displayName);
            $query->bindValue('user_email', $email);
            $query->bindValue('user_pwd', $password_hash);

            $query->execute();

            $newUser = $db->fetch('users', 'user_name', $name);
            if($newUser){
                //connect user
                $this->login($name, $password);

                return true;
            }else{
                return false;
            }

            
        }

        public function passwordHash(string $password):string {
            global $functions;
            $salt = Functions::randKey(16);

            $hash = password_hash("$password+$salt", PASSWORD_ARGON2I);

            return "$salt:$hash";
        }

        public function passwordVerify(string $password, string $salthash):bool {
            $exploded = explode(':', $salthash);

            $salt = $exploded[0];
            $hash = $exploded[1];

            return password_verify("$password+$salt", $hash);
        }

        public function login(string $login, string $password):void {
            global $api;

            $sql = "SELECT * FROM `users` WHERE `user_name`=:login OR `user_email`=:login";
            $query = $this->db->prepare($sql);

            $query->bindParam(":login", $login);
            $query->execute();

            $res = $query->fetchAll(PDO::FETCH_ASSOC);

            if(count($res) == 1){
                $userInfos = $res[0];
                extract($userInfos);

                if($this->passwordVerify($password, $user_pwd)){

                    $_SESSION["userId"] = $user_id;
                    $api->validRequest();

                }else{
                    $api->error(14, "Mot de passe incorrect.");
                }
                

            }else{
                $api->error(13, "Utilisateur introuvable.");
            }
            
        }

        public function logout():void {
            session_destroy();
        }

        public function require():void{
            global $api;

            if(@is_nan($this->id) || is_null($this->id)){
                $api->error(16);
            }
        }

        public function checkPublicUploadAllowed():void{
            global $api;

            if($this->type !== "admin") $api->error(21, "Vous n'avez pas les droits nécéssaires pour modifier les fichiers publics");
        }

    }