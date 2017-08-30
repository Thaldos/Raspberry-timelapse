<?php

define('LOCAL_DIR', '/home/pi/pictures');
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
        $connection = getSFTPConnection(REMOTE_HOST, REMOTE_PORT, REMOTE_USER, REMOTE_PASS);
        if ($connection !== false) {
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
 * Return connection to given remote server, initialised in given directory.
 *
 * @param $host : the server we want to connect to
 * @param $port : the port of the server
 * @param $user : the user used for the connection
 * @param $password : the password associated with the login
 * @param $remoteDir : the directory where we want to be
 *
 * @return resource a FTP stream on success or FALSE on error
 */
function getFTPConnection($host, $port, $user, $password, $remoteDir) {
    $connectionReturn = false;

    // Test parameters :
    if (!empty($host) && !empty($port) && !empty($user) && !empty($password) && !empty($remoteDir)) {
        try {
            // Get connect to server via ftp :
            $connection = ftp_connect($host, $port, 15);
            if ($connection !== false) {
                try {
                    // Log to ftp server :
                    $ftpLogin = ftp_login($connection, $user, $password);
                    if ($ftpLogin !== false) {
                        // Change to passive mode :
                        $isPassiveMode = ftp_pasv($connection, true);
                        if ($isPassiveMode !== false) {

                        } else {
                            $message = 'Unable to set mode to passive';
                            var_dump($message);
                        }
                    } else {
                        $message = 'Cannot authenticating to FTP server ' . $host .
                            ' with login ' . $user;
                        var_dump($message);
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    var_dump($message);
                }
            } else {
                $message = 'Cannot connecti to remote server ' . $host . ' on port ' . $port;
                var_dump($message);
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            var_dump($message);
        }
    } else {
        $message = 'Wrong servers parameters';
        var_dump($message);
    }
    return $connectionReturn;
}

function getSFTPConnection($host, $port, $user, $password) {
    $sftpConnection = false;

    $connection = ssh2_connect($host, $port);
    ssh2_auth_password($connection, $user, $password);
    $sftp = ssh2_sftp($connection);
    if ($sftp !== false) {
        $sftpConnection = $sftp;
    }
//    $stream = fopen("ssh2.sftp://$sftp/path/to/file", 'r');

    // https://stackoverflow.com/questions/34085742/get-csv-files-throw-sftp-php
    // https://stackoverflow.com/questions/4376332/connect-to-a-server-via-sftp-php

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
