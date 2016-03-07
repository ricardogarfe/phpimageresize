<?php

include '../Options.php';

class FunctionResizeTest extends PHPUnit_Framework_TestCase {
    public function testOpts() {
        $this->assertInstanceOf('Options', new Options);
    }
}

?>
