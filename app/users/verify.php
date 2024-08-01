<?php
    $api->requieredMethod(Api::GET);

    extract($api->getParameters());

    if(!empty($name)){
        //check if name respect [a-zA-Z0-9\.\-_
        $name_valid = preg_match("/^[a-zA-Z0-9\.\-_]*$/", $name);
        if(!$name_valid) $api->error(32, "Utilisez un nom d'utilisateur valide (lettres, chiffres, ., - et _)");
        
        //check if name already exist
        $name_exists = $db->fetch('users', 'user_name', $name);
        if($name_exists) $api->error(35);
    }

    if(!empty($email)){
        //check if mail is valid
        $mail_valid = filter_var($email, FILTER_VALIDATE_EMAIL);
        if(!$mail_valid) $api->error(31);

        //check if email already exist
        $email_exists = $db->fetch('users', 'user_email', $email);
        if($email_exists) $api->error(36);
    }

    $api->validRequest();