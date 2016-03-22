<?php
require 'ImagePath.php';
require 'Configuration.php';
require 'Resizer.php';

function resize($imagePath, $opts = null) {
    $path = new ImagePath($imagePath);
    $configuration = new Configuration($opts);

    $resizer = new Resizer($path, $configuration);

    $opts = $configuration->asHash();
    $imagePath = $path->sanitizedPath();

    try {
        $imagePath = $resizer->obtainFilePath();
    } catch (Exception $e) {
        return 'image not found';
    }

    $width = $configuration->obtainWidth();
    $height = $configuration->obtainHeight();

    $filename = md5_file($imagePath);

    $finfo = pathinfo($imagePath);
    $ext = $finfo['extension'];

    $cropSignal = isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "";
    $scaleSignal = isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "";

    if (false !== $opts['output-filename']) :
        $newPath = $opts['output-filename'];
    else:
        if (!empty($width) and !empty($height)):
            $newPath = $configuration->obtainCache() . $filename . '_w' . $width . '_h' . $height . $cropSignal . $scaleSignal . '.' . $ext;
        elseif (!empty($width)):
            $newPath = $configuration->obtainCache() . $filename . '_w' . $width . '.' . $ext;
        elseif (!empty($height)):
            $newPath = $configuration->obtainCache() . $filename . '_h' . $height . '.' . $ext;
        else:
            return false;
        endif;
    endif;

    $create = true;

    if (file_exists($newPath) == true):
        $create = false;
        $origFileTime = date("YmdHis", filemtime($imagePath));
        $newFileTime = date("YmdHis", filemtime($newPath));
        if ($newFileTime < $origFileTime): # Not using $opts['expire-time'] ??
            $create = true;
        endif;
    endif;

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
