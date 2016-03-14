<?php
require '../ImagePath.php';

class ImagePathTest extends PHPUnit_Framework_TestCase {

    public function testIsSanitizedAtInstantiation() {
        $url = 'https://www.google.es/search?num=20&espv=2&q=php+url+decode&oq=php+url+decode&gs_l=serp.3..0i10j0i22i30j0i22i10i30l2j0i22i30l5j0i22i10i30.2710.2710.0.3021.1.1.0.0.0.0.110.110.0j1.1.0....0...1.1.64.serp..0.1.109.9No_CVxnjQ8';
        $expected = 'https://www.google.es/search?num=20&espv=2&q=php url decode&oq=php url decode&gs_l=serp.3..0i10j0i22i30j0i22i10i30l2j0i22i30l5j0i22i10i30.2710.2710.0.3021.1.1.0.0.0.0.110.110.0j1.1.0....0...1.1.64.serp..0.1.109.9No_CVxnjQ8';

        $imagePath = new ImagePath($url);

        $this->assertEquals($expected, $imagePath->sanitizedPath());
    }

    public function testIsHttpProtocol() {
        $url = 'https://www.google.es';

        $imagePath = new ImagePath($url);

        $this->assertTrue($imagePath->isHttpProtocol());

        $ftp = 'ftp://example.com';

        $imagePathFtp = new ImagePath($ftp);

        $this->assertFalse($imagePathFtp->isHttpProtocol());

        $nullUrl = null;

        $imagePathFtp = new ImagePath($nullUrl);

        $this->assertFalse($imagePathFtp->isHttpProtocol());
    }

    public function testObtainFileName() {
        $url = 'http://memesvault.com/wp-content/uploads/Disappointed-Meme-Face-08.png?q=alt';
        $expected = 'Disappointed-Meme-Face-08.png';

        $imagePath = new ImagePath($url);

        $this->assertEquals($expected, $imagePath->obtainFileName());

    }
}
