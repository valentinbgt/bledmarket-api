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
    }