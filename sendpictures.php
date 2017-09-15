<?php

include_once 'config.php';

/**
 * Send pictures from Raspberry by mail.
 */
function sendPictures() {
    // If raspberry directory where takepicture.sh save pictures, exists :
    $isExistingLocalDir = isExistingLocalDir(RASPBERRY_PICTURES_DIRECTORY);
    if ($isExistingLocalDir) {
        $nbFilesFound = 0;

        // Create zip :
        $zip = new ZipArchive();

        // For each pictures :
        $it = new RecursiveDirectoryIterator(RASPBERRY_PICTURES_DIRECTORY, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // If it is a jpg file :
            if ($file->isFile() && $file->getExtension() === 'jpg') {
                $nbFilesFound += 1;

                // Add it to zip :

            }
        }

        // Send email with zip attached :
        sendEmailWithAttachment($zipPath);

        // Check number of pictures found :
        if ($nbFilesFound === 0) {
            $message = 'No pictures found today';
            var_dump($message);
        } elseif ($nbFilesFound > 1) {
            $message = $nbFilesFound . ' pictures found today';
            var_dump($message);
        }
    } else {
        $message = 'Local directory ' . RASPBERRY_PICTURES_DIRECTORY . 'doesn\'t exists';
        var_dump($message);
    }
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
