<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 06/08/16
 * Time: 17.02
 */

use Mbcraft\Piol\StringBuffer;

class StringBufferTest extends PHPUnit_Framework_TestCase {


    function testCreate() {

        $sb1 = new StringBuffer();

        $this->assertEquals("",$sb1."","Il buffer non è vuoto!");
        $this->assertEquals($sb1->pos(),0,"Il buffer vuoto non è in posizione 0!");

        $sb2 = new StringBuffer("Oh!");
        $this->assertEquals("Oh!",$sb2."","Il buffer non contiene i dati attesi!");
        $this->assertEquals($sb2->pos(),0,"La posizione del buffer creato non è 0!");

    }

    function testWrite() {
        $sb = new StringBuffer();
        $sb->write("Hello!");

        $this->assertEquals("Hello!","".$sb,"Il contenuto nel buffer non corrisponde!");
        $this->assertEquals($sb->pos(),strlen("Hello!"),"La posizione dopo la scrittura non è corretta!");

    }

    function testWriteln() {
        $sb = new StringBuffer();
        $sb->writeln("Hello!");

        $this->assertEquals("Hello!\r\n","".$sb,"Il contenuto nel buffer non corrisponde!");
        $this->assertEquals($sb->pos(),strlen("Hello!\r\n"),"La posizione dopo la scrittura non è corretta!");
    }

    function testWritelnReadln() {
        $sb = new StringBuffer();

        $this->assertEquals(0,$sb->pos(),"La posizione iniziale non è 0!!");

        $sb->writeln("Hello!");

        $this->assertEquals(8,$sb->pos(),"La posizione dopo la prima scrittura non è 8!!");

        $sb->writeln("Funny");

        $this->assertEquals(15,$sb->pos(),"La posizione dopo la seconda scrittura non è 15!!");

        $sb->writeln("World!");

        $this->assertEquals(23,$sb->pos(),"La posizione dopo la terza scrittura non è 23!!");

        $sb->reset();

        $this->assertEquals(0,$sb->pos(),"La posizione dopo il reset non è 0!!");


        $this->assertEquals(strlen("".$sb),strlen("Hello!\r\nFunny\r\nWorld!\r\n"),"La lunghezza del buffer non è corretto!");
        $this->assertEquals("".$sb,"Hello!\r\nFunny\r\nWorld!\r\n","Il contenuto del buffer non è corretto!");

        $this->assertEquals(0,$sb->pos(),"La posizione dopo la lettura del contenuto non è rimasta 0!!");

        $line0 = $sb->readLine();

        $this->assertEquals(8,$sb->pos(),"La posizione dopo la prima lettura non è 8!!");


        $this->assertEquals(strlen("".$sb),strlen("Hello!\r\nFunny\r\nWorld!\r\n"),"La lunghezza del buffer non è corretto!");
        $this->assertEquals("".$sb,"Hello!\r\nFunny\r\nWorld!\r\n","Il contenuto del buffer non è corretto!");

        $line1 = $sb->readLine();

        $this->assertEquals(15,$sb->pos(),"La posizione dopo la seconda lettura non è 15!!");

        $line2 = $sb->readLine();

        $this->assertEquals(23,$sb->pos(),"La posizione dopo la terza lettura non è 23!!");


        $this->assertEquals("Hello!",$line0,"Il contenuto letto della riga 0 non corrisponde!");
        $this->assertEquals("Funny",$line1,"Il contenuto letto della riga 1 non corrisponde!");
        $this->assertEquals("World!",$line2,"Il contenuto letto della riga 2 non corrisponde!");
        $this->assertEquals($sb->pos(),strlen("Hello!\r\nFunny\r\nWorld!\r\n"),"La posizione dopo la lettura non è corretta!");

    }

    function testPrintfAndRead() {

        $sb = new StringBuffer();

        $sb->write("Ciao mondo!!!");
        $sb->printf(" %02d %02d go",12,34);

        $sb->reset();

        $line = $sb->read(22);
        $this->assertEquals("Ciao mondo!!! 12 34 go",$line,"I dati letti non corrispondono!! : ".$line);

    }

    function testScanf() {

        $sb = new StringBuffer();

        $sb->write("Ciao mondo!!!");
        $sb->reset();

        $data = $sb->scanf("%s");

        $this->assertEquals("Ciao",$data[0],"La stringa letta non corrisponde!!");

    }

    function testScanf2() {

        $sb = new StringBuffer();

        $sb->write("Ciao 111 prova 123!!! :)");
        $sb->reset();

        $data = $sb->scanf("Ciao %d %s %d");

        $this->assertEquals($data[0],111,"Il numero letto non corrisponde!!");
        $this->assertEquals($data[1],"prova","La stringa letta non corrisponde!!");
        $this->assertEquals($data[2],123,"Il numero letto non corrisponde!!");

        $last_read = $sb->read(3);

        $this->assertEquals($last_read,"!!!","La lettura non contiene 3 punti esclamativi!");

        $this->assertEquals($sb->available(),3,"Il numero di byte disponibili per la lettura non corrisponde!");

        $this->assertFalse($sb->isEndOfStream(),"I dati nel buffer sono già esauriti!!");

        $sb->read(3);

        $this->assertTrue($sb->isEndOfStream(),"I dati nel buffer non sono esauriti!!");



    }
}