<?php

include_once 'config.php';

/**
 * Check if today picture have been weel received.
 */
function checkTodayPicture() {
    // Get current hour :
    $currentTime = (new DateTime('01:00'))->modify('+1 day');
    $startTime = new DateTime('18:00');

    // If it is after 18 pm :
    if ($currentTime >= $startTime) {
        // If directory where pictures are saved, exists :
        $isExistingLocalDir = isExistingLocalDir(SERVER_PICTURES);
        if ($isExistingLocalDir) {
            $todayPictureFound = false;
            $todayPictureName = date('Y-m-d') . '.jpg';

            // For each pictures :
            $it = new RecursiveDirectoryIterator(SERVER_PICTURES, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                // If it is the today picture :
                if ($file->isFile() && $file->getFilename() === $todayPictureName) {
                    $todayPictureFound = true;
                }
            }

            // Check if today picture have been found :
            if (!$todayPictureFound) {
                mail(
                    EMAIL_TO,
                    'Raspberry Timelapse : today picture not found',
                    'Error : The today picture "' . $todayPictureName . '" cannot be found in "' . SERVER_PICTURES . '"'
                );
            }
        } else {
            $message = 'Local directory ' . SERVER_PICTURES . ' doesn\'t exists';
            var_dump($message);
        }
    }
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

checkTodayPicture();