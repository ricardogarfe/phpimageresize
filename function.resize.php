<?php
require 'ImagePath.php';
require 'Configuration.php';
require 'Resizer.php';

function isInCache ($newPath, $imagePath) {
    $isInCache = true;
    if (file_exists($newPath) == true):
        $isInCache = false;
        $origFileTime = date("YmdHis", filemtime($imagePath));
        $newFileTime = date("YmdHis", filemtime($newPath));
        if ($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
            $isInCache = false;
        endif;
    endif;

    return $isInCache;
}

function composeNewPath($imagePath, $configuration) {
    $filename = md5_file($imagePath);
    $finfo = pathinfo($imagePath);
    $ext = $finfo['extension'];

    $cropSignal = isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "";
    $scaleSignal = isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "";

    $widthSignal = !empty($width) ? '_w'.$width : "" ;
    $heightSignal = !empty($height) ? '_w'.$height : "" ;
    $extension = '.'.$ext;

    $newPath = $configuration->obtainCache().$filename.$widthSignal.$heightSignal.$cropSignal.$scaleSignal.$extension;

    if ($opts['output-filename']) {
        $newPath = $opts['output-filename'];
    }

    return $newPath;
}

function resize($imagePath, $opts = null) {

    $path = new ImagePath($imagePath);
    $configuration = new Configuration($opts);

    $resizer = new Resizer($path, $configuration);

    $opts = $configuration->asHash();

    $width = $configuration->obtainWidth();
    $height = $configuration->obtainHeight();

    if (empty($opts['output-filename']) && empty($width) && empty($height)) {
        return 'Cannot resize the image.';
    }

    $imagePath = $path->sanitizedPath();

    try {
        $imagePath = $resizer->obtainFilePath();
    } catch (Exception $e) {
        return 'image not found';
    }

    $newPath = composeNewPath($imagePath, $configuration);

    $create = !isInCache($newPath, $imagePath);

    if ($create == true):
        if (!empty($width) and !empty($height)):

            list($width, $height) = getimagesize($imagePath);
            $resize = $width;

            if ($width > $height):
                $resize = $width;
                if (true === $opts['crop']):
                    $resize = "x" . $height;
                endif;
            else:
                $resize = "x" . $height;
                if (true === $opts['crop']):
                    $resize = $width;
                endif;
            endif;

            if (true === $opts['scale']):
                $cmd = $configuration->obtainConvertPath() . " " . escapeshellarg($imagePath) . " -resize " . escapeshellarg($resize) .
                    " -quality " . escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
            else:
                $cmd = $configuration->obtainConvertPath() . " " . escapeshellarg($imagePath) . " -resize " . escapeshellarg($resize) .
                    " -size " . escapeshellarg($width . "x" . $height) .
                    " xc:" . escapeshellarg($opts['canvas-color']) .
                    " +swap -gravity center -composite -quality " . escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
            endif;

        else:
            $cmd = $configuration->obtainConvertPath() . " " . escapeshellarg($imagePath) .
                " -thumbnail " . (!empty($height) ? 'x' : '') . $width . "" .
                (isset($opts['maxOnly']) && $opts['maxOnly'] == true ? "\>" : "") .
                " -quality " . escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
        endif;

        $c = exec($cmd, $output, $return_code);
        if ($return_code != 0) {
            error_log("Tried to execute : $cmd, return code: $return_code, output: " . print_r($output, true));
            return false;
        }
    endif;

    # return cache file path
    return str_replace($_SERVER['DOCUMENT_ROOT'], '', $newPath);

}
