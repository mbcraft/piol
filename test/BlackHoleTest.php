<?php

use Mbcraft\Piol\File;

class BlackHoleTest extends PHPUnit_Framework_TestCase
{
    /**
    @test
     */
    function testBlackHole()
    {
        
        $f = new File("/test/BlackHoleTest.php");

        $this->assertTrue($f->exists(),"Il file del test non esiste!!");

        $content = $f->getContent();

        $f->delete();

        $this->assertFalse($f->exists(),"Il file del test black hole non e' stato eliminato!!");

        $f->touch();

        $f->setContent($content);

        $this->assertTrue($f->exists(),"Il file del test black hole non e' stato rigenerato!!");
    }
}

