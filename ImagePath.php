<?php

class ImagePath {

    private $path;
    const VALID_HTTP_PROTOCOLS = ['http', 'https'];

    public function __construct($url='') {
        $this->path = $this->sanitize($url);
    }

    public function sanitizedPath() {
        return $this->path;
    }

    public function isHttpProtocol() {
        return in_array($this->obtainScheme(), self::VALID_HTTP_PROTOCOLS);
    }

    public function obtainFileName() {
        $finfo = pathinfo($this->path);
        list($filename) = explode('?', $finfo['basename']);
        return $filename;
    }

    private function sanitize($path) {
        return urldecode($path);
    }

    private function obtainScheme() {
        if ($this->path == '') return '';
        $purl = parse_url($this->path);
        return $purl['scheme'];
    }

}