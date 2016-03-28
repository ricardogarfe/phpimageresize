<?php

include '../Configuration.php';

class ConfigurationTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConfiguration() {
        $belowMinimunOptionsSet =  array('crop' => false,
            'scale' => 'false',
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => 'default-output-filename',
            'quality' => 90,
            'cache_http_minutes' => 20,
            'width' => null,
            'height' => null
        );

        $configuration = new Configuration($belowMinimunOptionsSet);
    }
}