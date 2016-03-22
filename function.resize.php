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

    if (isset($opts['w'])): $w = $opts['w']; endif;
    if (isset($opts['h'])): $h = $opts['h']; endif;

    $filename = md5_file($imagePath);

    // If the user has requested an explicit output-filename, do not use the cache directory.
    $finfo = pathinfo($imagePath);
    $ext = $finfo['extension'];
    if (false !== $opts['output-filename']) :
        $newPath = $opts['output-filename'];
    else:
        if (!empty($w) and !empty($h)):
            $newPath = $configuration->obtainCache() . $filename . '_w' . $w . '_h' . $h . (isset($opts['crop']) && $opts['crop'] == true ? "_cp" : "") . (isset($opts['scale']) && $opts['scale'] == true ? "_sc" : "") . '.' . $ext;
        elseif (!empty($w)):
            $newPath = $configuration->obtainCache() . $filename . '_w' . $w . '.' . $ext;
        elseif (!empty($h)):
            $newPath = $configuration->obtainCache() . $filename . '_h' . $h . '.' . $ext;
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
        if (!empty($w) and !empty($h)):

            list($width, $height) = getimagesize($imagePath);
            $resize = $w;

            if ($width > $height):
                $resize = $w;
                if (true === $opts['crop']):
                    $resize = "x" . $h;
                endif;
            else:
                $resize = "x" . $h;
                if (true === $opts['crop']):
                    $resize = $w;
                endif;
            endif;

            if (true === $opts['scale']):
                $cmd = $configuration->obtainConvertPath() . " " . escapeshellarg($imagePath) . " -resize " . escapeshellarg($resize) .
                    " -quality " . escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
            else:
                $cmd = $configuration->obtainConvertPath() . " " . escapeshellarg($imagePath) . " -resize " . escapeshellarg($resize) .
                    " -size " . escapeshellarg($w . "x" . $h) .
                    " xc:" . escapeshellarg($opts['canvas-color']) .
                    " +swap -gravity center -composite -quality " . escapeshellarg($opts['quality']) . " " . escapeshellarg($newPath);
            endif;

        else:
            $cmd = $configuration->obtainConvertPath() . " " . escapeshellarg($imagePath) .
                " -thumbnail " . (!empty($h) ? 'x' : '') . $w . "" .
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
