<?php

require 'FileSystem.php';

class Resizer {

    private $path;
    private $configuration;
    private $fileSystem;

    public function __construct($path, $configuration=null) {
        if ($configuration == null) $configuration = new Configuration();
        $this->checkPath($path);
        $this->checkConfiguration($configuration);
        $this->path = $path;
        $this->configuration = $configuration;
        $this->fileSystem = new FileSystem();
    }

    public function injectFileSystem(FileSystem $fileSystem) {
        $this->fileSystem = $fileSystem;
    }
    public function obtainFilePath() {
        $imagePath = '';
        if ($this->path->isHttpProtocol()):
            $filename = $this->path->obtainFileName();
            $local_filepath = $this->configuration->obtainRemote() . $filename;
            $inCache = $this->isInCache($local_filepath);

            if (!$inCache):
                $img = $this->fileSystem->file_get_contents($imagePath);
                $this->fileSystem->file_put_contents($local_filepath, $img);
            endif;
            $imagePath = $local_filepath;
        endif;

        return $imagePath;
    }

    private function isInCache($filepath) {
        $fileExists = $this->fileSystem->file_exists($filepath);
        $fileValid = $this->fileNotExpired($filepath);
        return $fileExists && $fileValid;
    }

    private function fileNotExpired($filepath) {
        $cacheMinutes = $this->configuration->obtainCacheMinutes();
        return $this->fileSystem->filemtime($filepath) < strtotime('+' . $cacheMinutes . ' minutes');
    }
    private function checkPath($path) {
        if(!($path instanceof ImagePath))throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }
}