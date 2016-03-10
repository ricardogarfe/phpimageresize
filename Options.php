<?php

class Options {
    const CACHE_FOLDER = './cache/';
    const REMOTE_FOLDER = './cache/remote/';

    const CACHE_KEY = 'cacheFolder';

    private $opts;

    public function __construct($opts=array()) {

        $sanitized = $this->sanitize($opts);

        $defaults = array('crop' => false,
            'scale' => 'false',
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => false,
            self::CACHE_KEY => self::CACHE_FOLDER,
            'remoteFolder' => self::REMOTE_FOLDER,
            'quality' => 90,
            'cache_http_minutes' => 20);

        $this->opts = array_merge($defaults, $sanitized);
    }

    public function asHash(){
        return $this->opts;
    }

    public function obtainCache() {
        return $this->opts[self::CACHE_KEY];
    }

    private function sanitize($opts) {
        $result = array();

        if ($opts != null) {
            $result = $opts;
        }
        return $result;
    }
}