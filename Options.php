<?php

class Options {

    private $opts;

    public function __construct($opts=array())
    {
        $cacheFolder = './cache/';
        $remoteFolder = $cacheFolder . 'remote/';

        $defaults = array('crop' => false, 'scale' => 'false', 'thumbnail' => false, 'maxOnly' => false,
            'canvas-color' => 'transparent', 'output-filename' => false,
            'cacheFolder' => $cacheFolder, 'remoteFolder' => $remoteFolder, 'quality' => 90, 'cache_http_minutes' => 20);

        $this->opts = array_merge($defaults, $opts);
    }

    public function asHash()
    {
        return $this->opts;
    }
}