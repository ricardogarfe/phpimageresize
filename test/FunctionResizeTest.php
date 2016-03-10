<?php

include '../Options.php';

class FunctionResizeTest extends PHPUnit_Framework_TestCase {

    private $defaults = array(
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

    public function testOpts() {
        $this->assertInstanceOf('Options', new Options);
    }

    public function testNullOptsDefaults() {
        $options = new Options(null);

        $this->assertEquals($this->defaults, $options->asHash());
    }

    public function testDefaults() {
        $options = new Options();
        $asHash = $options->asHash();

        $this->assertEquals($this->defaults, $asHash);
    }

    public function testDefaultsNotOverwriteConfiguration() {

        $configuration = array(
            'thumbnail' => true,
            'maxOnly' => true
        );

        $options = new Options($configuration);
        $configured = $options->asHash();

        $this->assertTrue($configured['thumbnail']);
        $this->assertTrue($configured['maxOnly']);
    }
}

?>
