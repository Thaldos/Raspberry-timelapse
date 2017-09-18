<?php

include_once 'config.php';

/**
 * Send pictures from Raspberry by mail.
 */
function sendPictures() {
    // If raspberry directory where takepicture.sh save pictures, exists :
    $isExistingLocalDir = isExistingLocalDir(RASPBERRY_PICTURES_DIRECTORY);
    if ($isExistingLocalDir) {
        // Create and get path of zip with all pictures :
        $zipPath = getZipPath(RASPBERRY_PICTURES_DIRECTORY);
        if ($zipPath !== false) {
            // Send zip by mail :
//            sendEmailWithAttachment($zipPath);

            // Remove pictures :
            $isOkRemoveAllFiles = removeAllFilesIn(RASPBERRY_PICTURES_DIRECTORY);
            if ($isOkRemoveAllFiles !== false) {
                // Remove zip file :
                $isOkRemoveZip = removeZipFile($zipPath);
                if ($isOkRemoveAllFiles !== false) {
                    // All is fine.
                } else {
                    $message = 'Cannot remove zip file : ' . $zipPath;
                    var_dump($message);
                }
            } else {
                $message = 'Cannot remove all files in ' . RASPBERRY_PICTURES_DIRECTORY;
                var_dump($message);
            }
        } else {
            $message = 'Cannot create and get path of zip file.';
            var_dump($message);
        }
    } else {
        $message = 'Local directory ' . RASPBERRY_PICTURES_DIRECTORY . 'doesn\'t exists';
        var_dump($message);
    }
}


/**
 * Create and return path of zip file with all pictures.
 *
 * @param $picturesDir Pictures directory.
 *
 * @return string|bool
 */
function getZipPath($picturesDir) {
    $zipPath = false;

    // Create zip file :
    $path = __DIR__ . '/timelapse-pictures.zip';
    $zip = new ZipArchive();
    $openReturn = $zip->open($path, ZipArchive::CREATE);
    if ($openReturn === true) {
        // For each pictures :
        $it = new RecursiveDirectoryIterator(RASPBERRY_PICTURES_DIRECTORY, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // If it is a jpg file :
            if ($file->isFile() && $file->getExtension() === 'jpg') {
                // Add it to zip :
                $zip->addFile($file->getRealPath(), $file->getFilename());
            }
        }
        $zipPath = $path;

        // Close zip file :
        $zip->close();
    } else {
        $message = 'Cannot create zip file ' . $path . '. Error code : ' . $openReturn . '.';
        var_dump($message);
    }

    return $zipPath;
}


/**
 * Send email with file attached.
 *
 * @param $filePath Local and absolute file path
 *
 * @return bool
 */
function sendEmailWithAttachment($filePath) {
    $sendReturn = false;

    $email = new PHPMailer();
    $email->From      = 'you@example.com';
    $email->FromName  = 'Your Name';
    $email->Subject   = 'Message Subject';
    $email->Body      = $bodytext;
    $email->AddAddress( 'destinationaddress@example.com' );

    $file_to_attach = 'PATH_OF_YOUR_FILE_HERE';

    $email->AddAttachment( $file_to_attach , 'NameOfFile.pdf' );

    $sendReturn = $email->Send();

    return $sendReturn;
}


/**
 * Remove all files contained in given directory.
 *
 * @return bool
 */
function removeAllFilesIn($dirPath) {
    $isOk = true;

    return $isOk;
}


/**
 * Remove zip file.
 *
 * @return bool
 */
function removeZipFile($zipPath) {
    $isOk = true;

    return $isOk;
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


// Send pictures from Raspberry by mail :
sendPictures();
