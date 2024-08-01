<?php
    $api->requieredMethod(Api::POST);

    $user->require();

    $api->parameterCheck('current_password', 'current_password_repeat', 'new_password');

    extract($api->getParameters());

    if($current_password !== $current_password_repeat) $api->error(37);

    $password_hash = $db->fetch('users', 'user_id', $user->id);
    $password_hash = $password_hash['user_pwd'];

    if($user->passwordVerify($current_password, $password_hash)){
        
        $new_password_hash = $user->passwordHash($new_password);

        $request = $user->updatePassword($new_password_hash);

        $api->validRequest($request);

    }else{
        $api->error(14, "Mot de passe incorrect.");
    }