<?php

class Resizer {

    public function __construct($path, $configuration=null) {
        $this->checkPath($path);
        $this->checkConfiguration($configuration);
    }

    private function checkPath($path) {
        if(!($path instanceof ImagePath))throw new InvalidArgumentException();
    }

    private function checkConfiguration($configuration) {
        if (!($configuration instanceof Configuration)) throw new InvalidArgumentException();
    }
}