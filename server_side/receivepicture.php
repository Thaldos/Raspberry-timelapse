<?php

include_once 'config.php';

/**
 * Receive uploaded file and move it to SERVER_PICTURES directory.
 *
 * @return json
 */
function receivePicture() {
    $isOk = false;

    // If file is receive :
    if (isset($_FILES['file']) && isset($_COOKIE['token']) && $_COOKIE['token'] === TOKEN) {
        $isOk = move_uploaded_file($_FILES['file']['tmp_name'], SERVER_PICTURES . '/' . $_FILES['file']['name']);
        if (!$isOk) {
            mail(
                EMAIL_TO,
                'Raspberry Timelapse : cannot move file',
                'Error : Cannot move uploaded file "' . $_FILES['file']['name'] . '" to "' . SERVER_PICTURES . '/' . $_FILES['file']['name'] .'"'
            );
        }
    }
    return json_encode($isOk);
}

echo receivePicture();