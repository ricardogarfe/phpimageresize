<?php

class ImagePath {

    private $path;

    public function __construct($url) {
        $this->path = $this->sanitize($url);
    }

    public function sanitizedPath() {
        return $this->path;
    }

    private function sanitize($path) {
        return urldecode($path);
    }

}