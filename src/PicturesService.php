<?php

use Symfony\Component\Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;

class PicturesService
{
    // Local path of pictures :
    protected $picturesFolderPath;

    public function __construct()
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../.env');
        $this->picturesFolderPath = __DIR__ . '/../pictures';
    }

    /**
     * Send pictures by mail then removes them from Raspberry.
     * Send mails if errors occurred.
     */
    public function sendPictures(): void
    {
        // If pictures folder, exists :
        $isExistingPicturesFolder = $this->isExistingPicturesFolder();
        if ($isExistingPicturesFolder) {
            // Create zip with all pictures and get path of  :
            $zipPath = $this->getZipPath();
            if ($zipPath !== false) {
                // Send zip by mail :
                $sendMailWithAttachmentReturn = $this->sendMailWithAttachment($zipPath);
                if ($sendMailWithAttachmentReturn) {
                    // Remove pictures :
                    $isOkRemoveAllFiles = $this->removeAllFilesIn();
                    if ($isOkRemoveAllFiles !== false) {
                        // Remove zip file :
                        $isOkRemoveZip = $this->removeZipFile($zipPath);
                        if ($isOkRemoveZip !== false) {
                            echo 'Pictures successfully sent by mail and removed from Raspberry pi.';
                        } else {
                            $this->sendNotification('Cannot remove zip file : ' . $zipPath);
                        }
                    } else {
                        $this->sendNotification('Cannot remove all files in ' . $this->picturesFolderPath);
                    }
                } else {
                    $this->sendNotification('Cannot send mail. Error : ' . $sendMailWithAttachmentReturn);
                }
            } else {
                $this->sendNotification('Cannot create and get path of zip file.');
            }
        } else {
            $this->sendNotification('Local directory ' . $this->picturesFolderPath . 'doesn\'t exists');
        }
    }

    /**
     * Create zip file with all pictures and return path of.
     */
    public function getZipPath(): bool
    {
        $zipPath = false;

        // Create zip file :
        $path = __DIR__ . '/timelapse-pictures.zip';
        $zip = new ZipArchive();
        $openReturn = $zip->open($path, ZipArchive::CREATE);
        if ($openReturn === true) {
            // For each pictures :
            $it = new RecursiveDirectoryIterator($this->picturesFolderPath, RecursiveDirectoryIterator::SKIP_DOTS);
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
            $this->sendNotification('Cannot create zip file ' . $path . '. Error code : ' . $openReturn . '.');
        }

        return $zipPath;
    }

    /**
     * Send email with file attached.
     */
    public function sendMailWithAttachment(string $filePath): bool
    {
        $sendReturn = false;

        // Get current year and month :
        $month = Date('F', strtotime('last month'));
        $year = Date('Y');

        // Send mail :
        $email = new PHPMailer();
        $email->From = 'raspberry@localhost.com';
        $email->FromName = 'Raspberry Timelapse';
        $email->Subject = 'Raspberry Timelapse : Pictures of ' . $month . ' ' . $year;
        $email->Body = 'Hello, this are the pictures of ' . $month . ' ' . $year . '.';
        $email->AddAddress($_ENV['EMAIL_TO']);
        $email->AddAttachment($filePath);
        $sendReturn = $email->Send();

        return $sendReturn;
    }

    /**
     * Remove all files contained in given directory.
     */
    public function removeAllFilesIn(): bool
    {
        $isOk = true;

        // Get all file names :
        $files = glob($this->picturesFolderPath . '/*');

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
     */
    public function removeZipFile(string $zipPath): bool
    {
        $isOk = true;

        // If it is a zip file :
        if (is_file($zipPath) && substr($zipPath, -3) === 'zip') {
            // Delete file :
            $isOk = unlink($zipPath);
        }

        return $isOk;
    }

    /**
     * Return true pictures directory exists, create it instead of.
     * Return false if errors.
     */
    public function isExistingPicturesFolder(): bool
    {
        $isExisting = true;
        
        if (!empty($this->picturesFolderPath) && !\file_exists($this->picturesFolderPath)) {
            try {
                if (!\mkdir($this->picturesFolderPath, 0777, true)) {
                    $this->sendNotification('Unable to create directory (' . $this->picturesFolderPath . ')');
                }
            } catch (Exception $e) {
                $isExisting = false;
                $this->sendNotification($e->getMessage());
            }
        }

        return $isExisting;
    }

    /**
     * Send notification in terminal and by email.
     */
    public function sendNotification(string $message): void
    {
        echo $message;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $_ENV['EMAIL_FROM'] . "\r\n" .
            'Reply-To: ' . $_ENV['EMAIL_FROM'] . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        $messageHtml = '<html><body>';
        $messageHtml .= $message;
        $messageHtml .= '</body></html>';
        mail($_ENV['EMAIL_TO'], 'Raspberry timelapse notification', $messageHtml, $headers);
    }
}
