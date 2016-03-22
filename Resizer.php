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
            $download_image = true;
            if ($this->fileSystem->file_exists($local_filepath)):
                $opts = $this->configuration->asHash();
                if ($this->fileSystem->filemtime($local_filepath) < strtotime('+' . $opts['cache_http_minutes'] . ' minutes')):
                    $download_image = false;
                endif;
            endif;
            if ($download_image == true):
                $img = $this->fileSystem->file_get_contents($imagePath);
                $this->fileSystem->file_put_contents($local_filepath, $img);
            endif;
            $imagePath = $local_filepath;
        endif;

        return $imagePath;
    }
    private function checkPath($path) {
        if(!($path instanceof ImagePath))throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }
}