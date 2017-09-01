<?php

require_once __DIR__ . '/libs/vendor/autoload.php';
define('LOCAL_DIR', __DIR__ . '/pictures');


function uploadpictures() {
    // Check if local directory exists :
    $isExistingLocalDir = isExistingLocalDir(LOCAL_DIR);
    if ($isExistingLocalDir) {
        $client = getGoogleConnexion();
        session_start();
        if (isset($_GET['code']) || (isset($_SESSION['access_token']) && $_SESSION['access_token'])) {
            if (isset($_GET['code'])) {
                $client->authenticate($_GET['code']);
                $_SESSION['access_token'] = $client->getAccessToken();
            } else {
                $client->setAccessToken($_SESSION['access_token']);
            }
            $service = new Google_Service_Drive($client);

            // Insert file
            $file = new Google_Service_Drive_DriveFile();
            $file->setName(uniqid().'.jpg');
            $file->setDescription('A test document');
            $file->setMimeType('image/jpeg');
            $data = file_get_contents('a.jpg');
            $createdFile = $service->files->create($file, array(
                'data' => $data,
                'mimeType' => 'image/jpeg',
                'uploadType' => 'multipart'
            ));
            print_r($createdFile);
        } else {
            $authUrl = $client->createAuthUrl();
            header('Location: ' . $authUrl);
            exit();
        }
    } else {
        $message = 'Local directory doesn\'t exists';
        var_dump($message);
    }
}


/**
 * Return true if given local directory exists, create it instead of
 * Return false if errors.
 *
 * @param string $path Local and absolute path.
 *
 * @return boolean
 */
function isExistingLocalDir($path) {
    $isExisting = true;
    if (!empty($path) && !file_exists($path)) {
        try {
            if (!mkdir($path, 0777, true)) {
                $message = 'Unable to create directory (' . $path . ')';
                var_dump($message);
            }
        } catch (Exception $e) {
            $isExisting = false;
            $message = $e->getMessage();
            var_dump($message);
        }
    }
    return $isExisting;
}

/**
 * Return google connexion.
 *
 * @return Google_Client
 */
function getGoogleConnexion() {
    $client = new Google_Client();
    $client->setClientId('<YOUR_CLIENT_ID>');
    $client->setClientSecret('<YOUR_CLIENT_SECRET>');
    $client->setRedirectUri('<YOUR_REGISTERED_REDIRECT_URI>');
    $client->setScopes(array('https://www.googleapis.com/auth/drive.file'));
    return $client;
}

// Upload local pictures to remote server :
uploadpictures();
