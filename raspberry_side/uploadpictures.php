<?php

include_once 'config.php';

/**
 * Post pictures from Raspberry to remote server.
 */
function uploadPictures() {
    // If raspberry directory where takepicture.sh save pictures, exists :
    $isExistingLocalDir = isExistingLocalDir(RASPBERRY_PICTURES);
    if ($isExistingLocalDir) {
        $nbFilesFound = 0;

        // For each pictures :
        $it = new RecursiveDirectoryIterator(RASPBERRY_PICTURES, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // If it is a file :
            if ($file->isFile() && $file->getExtension() !== 'DS_Store') {
                $nbFilesFound += 1;

                // Post this picture to remote server :
                $postReturn = postPicture($file->getRealPath());
                if ($postReturn !== true) {
                    var_dump($postReturn);
                }
            }
        }

        // Check number of pictures found :
        if ($nbFilesFound === 0) {
            $message = 'No pictures found today';
            var_dump($message);
        } elseif ($nbFilesFound > 1) {
            $message = $nbFilesFound . ' pictures found today';
            var_dump($message);
        }
    } else {
        $message = 'Local directory ' . RASPBERRY_PICTURES . 'doesn\'t exists';
        var_dump($message);
    }
}


/**
 * Post given picture to remove url.
 *
 * @param $filePath Local and absolute picture path
 *
 * @return string|bool
 */
function postPicture($filePath) {
    $postReturn = false;

    // Create file with unique token :
    $options = array(
        'file' => new CurlFile($filePath, 'image/jpg'),
    );

    // Post the file :
    $request = curl_init();
    curl_setopt($request, CURLOPT_URL, URL);
    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt($request, CURLOPT_POSTFIELDS, $options);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($request, CURLOPT_COOKIE, 'token=' . TOKEN);
    $postReturn = json_decode(curl_exec($request));
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
uploadPictures();
