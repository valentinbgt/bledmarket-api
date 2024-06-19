<?php
    Class Api{

        public function __construct(
            private array $response = array()
        ){
        }

        public function error(int $code = 2):void {
            $errorCodes = file_get_contents(PROJECT_ROOT . 'Settings/error_codes.json');
            $errorCodes = json_decode($errorCodes);

            $this->clearResponse();

            $this->addToResponse("errorCode", $code);
            $this->addToResponse("errorMessage", $errorCodes->$code);

            $this->reply();
        }

        public function reply():void {
            if(empty($this->response)) $this->error(0);

            die(json_encode($this->response));
        }

        public function addToResponse(string $key, string $value):void {
            $this->response[$key] = $value;
        }

        public function clearResponse():void {
            $this->response = array();
        }

    }