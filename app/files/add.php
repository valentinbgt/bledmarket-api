<?php
    $user->require();

    $api->requieredMethod(Api::POST);

    $api->parameterCheck("repertory");
    $api->parameterCheck("path");

    extract($api->getParameters());

    $api->checkRepertory($repertory);
    if($repertory == "public") $user->checkPublicUploadAllowed();


    if(!isset($_FILES["file"])) $api->error(18);
    if($_FILES["file"]["error"] != 0) $api->error(19, $_FILES["file"]["error"]);

    $uploaded_file = $_FILES["file"];

    $tmp_name = $uploaded_file["tmp_name"];
    if(!file_exists($tmp_name)) $api->error(20);

    $file_name = $uploaded_file["name"];
    $file_type = $uploaded_file["type"];
    $file_size = $uploaded_file["size"];

    $file = new File();

    $file->upload($tmp_name, $file_name, $file_type, $file_size, $path, $repertory, $user);
    die();

    // $api->addToResponse("fileList", $fileList);
    // $api->validRequest();