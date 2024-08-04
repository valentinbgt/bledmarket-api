<?php
    Class Functions{
        public static function randKey(int $length = 64, array $not = array()) :string{
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $randomString = null;

            $isKeyValid = false;

            while(!$isKeyValid){
                $randomString = '';

                for ($i = 0; $i < $length; $i++) {
                    $index = rand(0, strlen($characters) - 1);
                    $randomString .= $characters[$index];
                }

                if(!in_array($randomString, $not)) $isKeyValid = true;
            }
            
            return $randomString;
        }

        public static function isMIMEDisplayable(string $mime):bool{
            $availableMimes = array(
                "text/plain",
                "text/html",
                "text/css",
                "text/javascript",
                "application/javascript",
                "application/x-javascript",
                
                "image/gif",
                "image/jpeg",
                "image/png",
                "image/svg+xml",
                "image/webp",
                "image/avif",
                
                "video/mp4",
                "video/webm",
                "video/ogg",

                "audio/mpeg",
                "audio/ogg",
                "audio/wav",
                "audio/webm",

                "application/pdf",
                "application/json",
                "application/xml",

                "application/xhtml+xml"
            );

            return in_array($mime, $availableMimes);
        }
    }