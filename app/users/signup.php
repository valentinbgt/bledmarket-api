<?php
    $api->requieredMethod(Api::POST);

    $api->parameterCheck('name', 'displayName', 'email', 'password');

    extract($api->getParameters());

    
    //check if name respect [a-zA-Z0-9\.\-_
    $name_valid = preg_match("/^[a-zA-Z0-9\.\-_]*$/", $name);
    if(!$name_valid) $api->error(32, "Utilisez un nom d'utilisateur valide (lettres, chiffres, ., - et _)");
    
    //check if display name !> 42 long
    $displayName = preg_replace("~(?:[\p{M}]{1})([\p{M}])+?~uis", "", $displayName);

    $display_name_valid = !(strlen($displayName) > 42);
    if(!$display_name_valid) $api->error(33, "max = 42");

    //check if email is valid
    $mail_valid = filter_var($email, FILTER_VALIDATE_EMAIL);
    if(!$mail_valid) $api->error(31);

    //check if password is secure
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number    = preg_match('@[0-9]@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
        $api->error(34, 'Minimum : longueur > 8, lettre, majuscule, chiffre, caractère spécial.');
    }


    //check if name already exist
    $name_exists = $db->fetch('users', 'user_name', $name);
    if($name_exists) $api->error(35);

    //check if email already exist
    $email_exists = $db->fetch('users', 'user_email', $email);
    if($email_exists) $api->error(36);


    //signup
    $request = $user->signup($name, $displayName, $email, $password);
    $api->validRequest($request);