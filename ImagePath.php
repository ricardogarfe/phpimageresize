<?php

class ImagePath {

    private $path;

    public function __construct($url) {
        $this->path = $this->sanitize($url);
    }

    public function sanitizedPath() {
        return $this->path;
    }

    public function isHttpProtocol() {
        return $this->obtainScheme() == 'http' || $this->obtainScheme() == 'https';;
    }

    private function sanitize($path) {
        return urldecode($path);
    }

    private function obtainScheme() {
        $purl = parse_url($this->path);
        return $purl['scheme'];
    }

}