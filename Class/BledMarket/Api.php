<?php
    Class Api{

        public const POST = "POST";
        public const GET = "GET";

        private array $response = array();
        private string $method;

        public function __construct(
        ){
            $this->method = $_SERVER['REQUEST_METHOD'];
        }


        // RESPONSE & ERROR HANDLERS
        public function error(int $code = 2, string $customMessage = ""):void {
            $errorCodes = file_get_contents(PROJECT_ROOT . 'Settings/error_codes.json');
            $errorCodes = json_decode($errorCodes);

            if(!isset($errorCodes->$code)) $code = 2;

            $errorMessage = $errorCodes->$code;

            if(!empty($customMessage)) {
                $errorMessage .= ": $customMessage";
            }

            if($code !== 1) $this->clearResponse();

            $this->addToResponse("errorCode", $code);
            $this->addToResponse("errorMessage", $errorMessage);

            $this->reply();
        }

        public function reply():void {
            if(empty($this->response)) $this->error(0);

            if(!isset($this->response["errorCode"])) $this->error(1);

            header('Content-Type: application/json');
            die(json_encode($this->response));
        }

        public function addToResponse(string $key, mixed $value):void {
            $this->response[$key] = $value;
        }

        public function clearResponse():void {
            $this->response = array();
        }

        public function validRequest():void {
            $this->error(1);
        }

        public function debug():void {
            header('Content-Type: text/html; charset=UTF-8');
            die();
        }
        // [END] RESPONSE & ERROR HANDLERS


        // REQUEST PARAMETERS

        public function requieredMethod(string $method):void {
            if($this->method != $method) $this->error(11);
        }

        public function parameterCheck(string ...$args):void {
            $paramContainer = $GLOBALS["_$this->method"];

            foreach($args as $parameter){
                if(empty($paramContainer[$parameter])) {
                    $this->error(12, "'$parameter'");
                }
            }
        }

        public function getParameters():array {
            return $GLOBALS["_$this->method"];
        }

        public function checkRepertory($repertory){
            $validRepertorys = [
                'public',
                'private',
                'drop'
            ];

            if(!in_array($repertory, $validRepertorys)) $this->error(17);
        }

        // [END] REQUEST PARAMETERS

    }