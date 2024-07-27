<?php
    $user->require();

    $api->requieredMethod(Api::POST);

    $api->parameterCheck("fileId");

    extract($api->getParameters());

    $db = new Database();

    $sql = "SELECT `user_id` FROM `files` WHERE `file_public_id`=:file_public_id";
    $query = $db->prepare($sql);
    $query->bindValue(':file_public_id', $fileId);
    $query->execute();

    $result = $query->fetch();

    $file = new File();

    if(is_array($result)){
        if($result["user_id"] == $user->id){
            $file->delete($fileId);
        }
    }

    $api->validRequest();   