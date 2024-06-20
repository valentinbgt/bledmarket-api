<?php
    Class Api{

        public function __construct(
            private array $response = array()
        ){
        }

        public function error(int $code = 2, string $customMessage = ""):void {
            $errorCodes = file_get_contents(PROJECT_ROOT . 'Settings/error_codes.json');
            $errorCodes = json_decode($errorCodes);

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

        public function addToResponse(string $key, string $value):void {
            $this->response[$key] = $value;
        }

        public function clearResponse():void {
            $this->response = array();
        }

        public function debug():void {
            header('Content-Type: text/html; charset=UTF-8');
            die();
        }

    }