<?php

require '../Resizer.php';
require '../ImagePath.php';
require '../Configuration.php';
date_default_timezone_set('Europe/Berlin');

class ResizerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNecessaryCollaboration() {
        $resizer = new Resizer('anyNonPathObject');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testOptionalCollaboration() {
        $resizer = new Resizer(new ImagePath(''), 'nonConfigurationObject');
    }

    public function testInstantiation() {
        $this->assertInstanceOf('Resizer', new Resizer(new ImagePath(''), new Configuration()));
        $this->assertInstanceOf('Resizer', new Resizer(new ImagePath('')));
    }

    public function testObtainLocallyCachedFilePath() {
        $resizer = new Resizer(new ImagePath('http://memesvault.com/wp-content/uploads/Disappointed-Meme-Face-08.png?q=alt'));

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_get_contents')
            ->willReturn('foo');

        $resizer->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/Disappointed-Meme-Face-08.png', $resizer->obtainFilePath());

    }

    public function testLocallyCachedFilePathFail() {
        $resizer = new Resizer(new ImagePath('http://memesvault.com/wp-content/uploads/Disappointed-Meme-Face-08.png?q=alt'));

        $stub = $this->getMockBuilder('FileSystem')
            ->getMock();
        $stub->method('file_exists')
            ->willReturn(true);
        $stub->method('filemtime')
            ->willReturn(21 * 60);

        $resizer->injectFileSystem($stub);

        $this->assertEquals('./cache/remote/Disappointed-Meme-Face-08.png', $resizer->obtainFilePath());

    }
}
