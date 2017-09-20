<?php

include_once 'config.php';
include_once 'PHPMailer/class.phpmailer.php';

/**
 * Send pictures by mail then removes them from Raspberry.
 * Send mails if errors occured.
 */
function sendPictures() {
    // If raspberry directory where takepicture.sh save pictures, exists :
    $isExistingLocalDir = isExistingLocalDir(RASPBERRY_PICTURES_DIRECTORY);
    if ($isExistingLocalDir) {
        // Create and get path of zip with all pictures :
        $zipPath = getZipPath(RASPBERRY_PICTURES_DIRECTORY);
        if ($zipPath !== false) {
            // Send zip by mail :
            $sendMailWithAttachmentReturn = sendMailWithAttachment($zipPath);
            if ($sendMailWithAttachmentReturn) {
                // Remove pictures :
                $isOkRemoveAllFiles = removeAllFilesIn(RASPBERRY_PICTURES_DIRECTORY);
                if ($isOkRemoveAllFiles !== false) {
                    // Remove zip file :
                    $isOkRemoveZip = removeZipFile($zipPath);
                    if ($isOkRemoveAllFiles !== false) {
                        $message = 'Pictures successfully sent by mail and remove from Raspberry pi.';
                        var_dump($message);
                    } else {
                        $message = 'Cannot remove zip file : ' . $zipPath;
                        var_dump($message);
                        sendMail('Raspberry Timelapse : Cannot remove zip file', $message);
                    }
                } else {
                    $message = 'Cannot remove all files in ' . RASPBERRY_PICTURES_DIRECTORY;
                    var_dump($message);
                    sendMail('Raspberry Timelapse : Cannot remove all files', $message);
                }
            } else {
                $message = 'Cannot send mail. Error : ' . $sendMailWithAttachmentReturn;
                var_dump($message);
                sendMail('Raspberry Timelapse : Cannot send mail', $message);
            }
        } else {
            $message = 'Cannot create and get path of zip file.';
            var_dump($message);
            sendMail('Raspberry Timelapse : Cannot create and get path of zip file', $message);
        }
    } else {
        $message = 'Local directory ' . RASPBERRY_PICTURES_DIRECTORY . 'doesn\'t exists';
        var_dump($message);
        sendMail('Raspberry Timelapse : Local directory doesn\'t exists', $message);
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
        sendMail('Raspberry Timelapse : Cannot create zip file ', $message);
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
function sendMailWithAttachment($filePath) {
    $sendReturn = false;

    // Get current year and month :
    $month = Date('F');
    $year = Date('Y');

    // Send mail :
    $email = new PHPMailer();
    $email->From      = 'raspberry@localhost.com';
    $email->FromName  = 'Raspberry Timelapse';
    $email->Subject   = 'Raspberry Timelapse : Pictures of ' . $year . ' ' . $month;
    $email->Body      = 'Hello, attached the pictures of ' . $year . ' ' . $month . '.';
    $email->AddAddress(EMAIL_TO);
    $email->AddAttachment($filePath);
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

    // Get all file names :
    $files = glob($dirPath . '/*');

    // For each jpg file :
    foreach ($files as $file) {
        if (is_file($file) && substr($file, -3) === 'jpg') {
            // Delete file :
            $unlinkReturn = unlink($file);
            $isOk = $isOk && $unlinkReturn;
        }
    }
    return $isOk;
}


/**
 * Remove zip file.
 *
 * @return bool
 */
function removeZipFile($zipPath) {
    $isOk = true;

    // If it is a zip file :
    if (is_file($zipPath) && substr($zipPath, -3) === 'zip') {
        // Delete file :
        $isOk = unlink($zipPath);
    }
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
                sendMail('Raspberry Timelapse : Unable to create directory', $message);
            }
        } catch (Exception $e) {
            $isExisting = false;
            $message = $e->getMessage();
            var_dump($message);
            sendMail('Raspberry Timelapse : Exception', $message);
        }
    }
    return $isExisting;
}


/**
 * Send email to EMAIL_TO.
 */
function sendMail($subject, $message) {
    mail(EMAIL_TO, $subject, $message);
}


// Send pictures by mail then removes them from Raspberry :
sendPictures();
