<?php

include '../Options.php';

class FunctionResizeTest extends PHPUnit_Framework_TestCase {

    public function testOpts() {
        $this->assertInstanceOf('Options', new Options);
    }

    public function testDefaults() {
        $options = new Options();
        $defaults = array(
            'crop' => false,
            'scale' => 'false',
            'thumbnail' => false,
            'maxOnly' => false,
            'canvas-color' => 'transparent',
            'output-filename' => false,
            'cacheFolder' => './cache/',
            'remoteFolder' => './cache/remote/',
            'quality' => 90,
            'cache_http_minutes' => 20
        );

        $asHash = $options->asHash();

        $this->assertEquals($defaults, $asHash);
    }
}

?>
