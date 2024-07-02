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
            $user = $this->db->fetch('users', 'user_id', $this->id);
            
            if(!$user) {
                $this->logout();
                (new Api())->error(15, "Utilisateur introuvable, vous avez été déconnecté.");
            }

            extract($user);

            $this->type = $user_type;
            $this->name = $user_name;
            $this->display_name = $user_display_name;
            $this->email = $user_email;
            $this->about = $user_about;
        }

        public function login(string $login, string $password):void {
            $sql = "SELECT * FROM `users` WHERE `user_name`=:login OR `user_email`=:login";
            $query = $this->db->prepare($sql);

            $query->bindParam(":login", $login);
            $query->execute();

            $res = $query->fetchAll(PDO::FETCH_ASSOC);

            if(count($res) == 1){
                $userInfos = $res[0];
                extract($userInfos);

                if(password_verify($password, $user_pwd)){

                    $_SESSION["userId"] = $user_id;
                    (new Api())->validRequest();

                }else{
                    (new Api())->error(14, "Mot de passe incorrect.");
                }
                

            }else{
                (new Api())->error(13, "Utilisateur introuvable.");
            }
            
        }

        public function logout():void {
            session_destroy();
        }

        public function require():void{
            if(@is_nan($this->id) || is_null($this->id)){
                (new Api())->error(16);
            }
        }

        public function checkPublicUploadAllowed():void{
            if($this->type !== "admin") (new Api())->error(21, "Vous n'avez pas les droits nécéssaires pour modifier les fichiers publics");
        }

    }