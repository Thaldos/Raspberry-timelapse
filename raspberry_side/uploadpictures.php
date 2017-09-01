<?php

// Raspberry directory where takepicture.sh save pictures :
define('PICTURES_PATH', __DIR__ . '/pictures');

// Url where post pictures :
define('URL_TO_POST_PICTURES', 'http://www.yourdomain.com/receivepicture.php');

/**
 * Post pictures from Raspberry to remote server.
 *
 * @return bool
 */
function uploadpictures() {
    // If raspberry directory where takepicture.sh save pictures, exists :
    $isExistingLocalDir = isExistingLocalDir(PICTURES_PATH);
    if ($isExistingLocalDir) {
        $nbFilesFound = 0;

        // For each pictures :
        $it = new RecursiveDirectoryIterator(PICTURES_PATH, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // If it is a file :
            if ($file->isFile()) {
                $nbFilesFound += 1;

                // Post this picture to remote server :
                $postReturn = postPictureTo($file->getRealPath(), URL_TO_POST_PICTURES);
            }
        }

        // If no pictures found :
        if ($nbFilesFound === 0) {
            $message = 'No pictures found today';
            var_dump($message);
        } elseif ($nbFilesFound > 1) {
            $message = $nbFilesFound . ' pictures found today';
            var_dump($message);
        }
    } else {
        $message = 'Local directory doesn\'t exists';
        var_dump($message);
    }
}


/**
 * Post given picture to given url.
 *
 * @param $filePath Local and absolute picture path
 * @param $url Url to post picture
 *
 * @return string|bool
 */
function postPictureTo($filePath, $url) {
    $postReturn = false;

    // Initialise the curl request :
    $request = curl_init($url);

    // Post the file :
    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt($request, CURLOPT_POSTFIELDS, array('file' => '@' . $filePath));

    // Get the response :
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    $postReturn = curl_exec($request);

    // Close the session :
    curl_close($request);

    return $postReturn;
}


/**
 * Return true if given local directory exists, create it instead of
 * Return false if errors.
 *
 * @param string $path Local and absolute path.
 *
 * @return bool
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


// Upload Rapberry pictures to remote server :
uploadpictures();
