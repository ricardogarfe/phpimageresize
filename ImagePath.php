<?php

class ImagePath {

    private $path;

    public function __construct($url) {
        $this->path = $this->sanitize($url);
    }

    public function sanitizedPath() {
        return $this->path;
    }

    public function obtainScheme() {
        $purl = parse_url($this->path);
        return $purl['scheme'];
    }

    private function sanitize($path) {
        return urldecode($path);
    }

}