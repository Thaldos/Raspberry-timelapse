<?php

require __DIR__ . '/vendor/autoload.php';

// Send pictures by mail then removes them from Raspberry :
$picturesService = new PicturesService();
$picturesService->sendPictures();
