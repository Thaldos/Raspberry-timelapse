<?php

define('ROOT_PATH', __DIR__ . '/');
include(ROOT_PATH . 'lib/Net/SFTP.php');

define('LOCAL_DIR', '/Applications/MAMP/htdocs/raspberry_timelpase/raspberry_side/pictures');
define('REMOTE_HOST', 'google.com');
define('REMOTE_PORT', '21');
define('REMOTE_USER', 'root');
define('REMOTE_PASS', 'yourpassword');
define('REMOTE_DIR', '/home/debian/timelapse');

function uploadpictures() {
    // Check if local directory exists :
    $isExistingLocalDir = isExistingLocalDir(LOCAL_DIR);
    if ($isExistingLocalDir) {
        // Get connection to remote server :
        /** @var Net_SFTP $connection */
        $connection = getSFTPConnection(REMOTE_HOST, REMOTE_PORT, REMOTE_USER, REMOTE_PASS);
        if ($connection !== false) {
            echo $connection->pwd() . "\r\n";


            // Check remote directory exists :
//            $isExistingRemoteDir = isExistingRemoteDir($connection, REMOTE_DIR);
//            if ($isExistingRemoteDir) {
//                $connectionReturn = $connection;
//            } else {
//                $message = 'Remote directory doesn\'t exists.';
//                var_dump($message);
//            }
        } else {
            $message = 'Cannot get connection to remote server.';
            var_dump($message);
        }
    } else {
        $message = 'Local directory doesn\'t exists';
        var_dump($message);
    }
}

/**
 * Return connection to given remote server, initialised in given directory,
 * or false if errors.
 *
 * @param $host : the server we want to connect to
 * @param $port : the port of the server
 * @param $user : the user used for the connection
 * @param $password : the password
 *
 * @return Net_SFTP|FALSE
 */
function getSFTPConnection($host, $port, $user, $password) {
    $sftpConnection = false;
    $sftp = new Net_SFTP($host, $port);
    $sftpLogin = $sftp->login($user, $password);
    if ($sftpLogin !== false) {
        $sftpConnection = $sftp;
    } else {
        $message = 'Cannot connect to remote server ' . $host . ' on port ' . $port. ' with user ' . $user . ' and password ' . $password;
        var_dump($message);
    }
    return $sftpConnection;
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
 * Return true if given remote directory exists, create it instead of
 * Return false if errors.
 *
 * @param string $path Remote and absolute path.
 *
 * @return boolean
 */
function isExistingRemoteDir($connection, $path) {
    $isExisting = true;

    // Navigate to (and/or create) destination directory :
    $directories = explode('/', $path);
    foreach ($directories as $directory) {
        if (!empty($directory)) {
            if (!@ftp_chdir($connection, $directory)) {
                if (!@ftp_mkdir($connection, $directory)) {
                    $isExisting = false;
                    $message = 'Error: Unable to create directory (' . $directory . ')';
                    var_dump($message);
                }
                if (!@ftp_chdir($connection, $directory)) {
                    $isExisting = false;
                    $message = 'Error: Unable to change to directory (' . $directory . ')';
                    var_dump($message);
                }
            }
        }
    }
    return $isExisting;
}

// Upload local pictures to remote server :
uploadpictures();
