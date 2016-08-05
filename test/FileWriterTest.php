<?php

use Mbcraft\Piol\File;
use Mbcraft\Piol\FileWriter;
use Mbcraft\Piol\IOException;
use Mbcraft\Piol\Utils\CsvUtils;

class FileWriterTest extends PHPUnit_Framework_TestCase
{
    function testWriterOpenButDontChange()
    {
        $f = new File("/test/reader_writer/myfile_01.txt");

        $this->assertEquals(24,$f->getSize(),"La dimensione del file non corrisponde!! : ".$f->getSize());

        try
        {
            $writer = $f->openWriter();
            $this->assertTrue($writer instanceof FileWriter,"L'oggetto non e' del tipo specificato!!");

            $this->assertTrue($writer->isOpen(),"Il writer non e' aperto!!");
            $writer->close();
            $this->assertFalse($writer->isOpen(),"Il writer non e' stato chiuso!!");
        }
        catch (Exception $ex)
        {
            $this->fail("Errore nell'apertura del writer di un file esistente!!");
        }

    }

    function testWriterCreateIfNotExists()
    {
        $f = new File("/test/reader_writer/blablabla.txt");
        $this->assertFalse($f->exists(),"Il file esiste gia'!!");
        try
        {
            $writer = $f->openWriter();
            $this->assertTrue($f->exists(),"Il file non e' stato creato!!");

            $this->assertTrue($writer instanceof FileWriter,"L'oggetto non e' del tipo specificato!!");

            $this->assertTrue($writer->isOpen(),"Il writer non e' aperto!!");
            $writer->close();
            $this->assertFalse($writer->isOpen(),"Il writer non e' stato chiuso!!");
        }
        catch (Exception $ex)
        {
            $this->fail("Errore nell'apertura del writer di un file non esistente!!");
        }
        $f->delete();

    }

    function testBasicWritelnThenReadln()
    {
        $f = new File("/test/reader_writer/readwrite.txt");
        $this->assertFalse($f->exists(),"Il file esiste gia'!!");
        try
        {
            $writer = $f->openWriter();
            $this->assertTrue($f->exists(),"Il file non e' stato creato!!");

            $this->assertTrue($writer instanceof FileWriter,"L'oggetto non e' del tipo specificato!!");

            $this->assertTrue($writer->isOpen(),"Il writer non e' aperto!!");

            $writer->writeln("Ciao mondo!!!");
            $writer->writeln("Hello!!");

            $writer->close();

            $reader = $f->openReader();
            $first_line = $reader->readLine();
            $this->assertEquals(strlen("Ciao mondo!!!"),strlen($first_line),"La lunghezza attesa non corrisponde!! : ".strlen($first_line));
            $this->assertEquals("Ciao mondo!!!",$first_line,"Il dato letto non corrisponde!! :".$first_line);
            $second_line = $reader->readLine();
            $this->assertEquals(strlen("Hello!!"),strlen($second_line),"La lunghezza attesa non corrisponde!! : ".strlen($second_line));
            $this->assertEquals("Hello!!",$second_line,"Il dato letto non corrisponde!! :".$second_line);

            $reader->close();
        }
        catch (Exception $ex)
        {
            $f->delete();
            
            $this->fail("Errore nell'apertura del writer di un file non esistente!!".$ex);
        }
        
        $f->delete();
    }
    
    function testWriteReadCsv() {
        $f = new File("/test/reader_writer/my_table_data.csv");
        
        $w = $f->openWriter();
        
        $w->writeln("ID;Title;Count");

        CsvUtils::write($w,array(0,"La biblioteca",16.5),",","\"");
        CsvUtils::write($w,array(1,"Il castello 'v', la torre 'r' e il cortile ...",18.3),",","\"");
        $w->close();
        
        $r = $f->openReader();
        
        $this->assertFalse($r->isEndOfStream(),"Lo stream non è terminato!!");
        
        $r->readLine();
        $d1 = CsvUtils::read($r);
        $d2 = CsvUtils::read($r);
        
        $this->assertEquals(0,$d1[0],"Il valore letto non corrisponde!!");
        $this->assertEquals("La biblioteca",$d1[1],"Il valore letto non corrisponde!!");
        $this->assertEquals(16.5,$d1[2],"Il valore letto non corrisponde!!");
        
        $this->assertEquals(1,$d2[0],"Il valore letto non corrisponde!!");
        $this->assertEquals("Il castello 'v', la torre 'r' e il cortile ...",$d2[1],"Il valore letto non corrisponde!!");
        $this->assertEquals(18.3,$d2[2],"Il valore letto non corrisponde!!");
        
        $d3 = CsvUtils::read($r);
        $this->assertNull($d3,"Il valore letto non è null alla fine dello stream!!");
        $this->assertTrue($r->isEndOfStream(),"Lo stream non è terminato!!");
        
        $r->close();
    }

    function testAdvancedPrintfWriteThenRead()
    {
        $f = new File("/test/reader_writer/readwrite2.txt");

        //delete test/reader_writer/readwrite2.txt to fix this test failure

        $this->assertFalse($f->exists(),"Il file esiste gia'!!");
        try
        {
            $writer = $f->openWriter();
            $this->assertTrue($f->exists(),"Il file non e' stato creato!!");

            $this->assertTrue($writer instanceof FileWriter,"L'oggetto non e' del tipo specificato!!");

            $this->assertTrue($writer->isOpen(),"Il writer non e' aperto!!");

            $writer->write("Ciao mondo!!!");
            $writer->printf(" %02d %02d go",12,34);

            $writer->close();

            $reader = $f->openReader();
            $line = $reader->read(22);
            $this->assertEquals("Ciao mondo!!! 12 34 go",$line,"I dati letti non corrispondono!! : ".$line);

            $reader->close();
        }
        catch (Exception $ex)
        {
            $this->fail("Errore nell'apertura del writer di un file non esistente!!");
        }
        $f->delete();
    }

    function testCreateTmpFile()
    {
        File::setTmpFileDir("/test/tmp_dir/");
        
        $f = File::newTempFile();
        
        $fw = $f->openWriter();

        $this->assertTrue($fw->isOpen(),"Il file temporaneo non risulta aperto!!");

        $fw->writeln("Ciao, questo e' un file temporaneo...");
        $fw->reset();

        $line = $fw->readLine();

        $this->assertEquals("Ciao, questo e' un file temporaneo...",$line,"Il dato letto dal file temporaneo non corrisponde!!");

        $fw->close();
        
        $f->delete();
    }

    function testExceptionAfterCloseOnRead()
    {
        File::setTmpFileDir("/test/tmp_dir/");
        
        $f = File::newTempFile();
        
        $fw = $f->openWriter();

        $this->assertTrue($fw->isOpen(),"Il file temporaneo non risulta aperto!!");

        $fw->writeln("Ciao, questo e' un file temporaneo...");
        $fw->reset();

        $fw->close();
        
        try {
            $fw->readLine();
        
            $this->fail();
        } catch (IOException $ex) {
            
        }
        
        $f->delete();
    }

    function testExceptionAfterCloseOnWrite()
    {
        File::setTmpFileDir("/test/tmp_dir/");
        
        $f = File::newTempFile();
        
        $fw = $f->openWriter();

        $this->assertTrue($fw->isOpen(),"Il file temporaneo non risulta aperto!!");

        $fw->writeln("Ciao, questo e' un file temporaneo...");
        $fw->reset();

        $fw->close();
        try {
        
            $fw->writeln("Ciao!!");
        
            $this->fail();
        }
        catch (IOException $ex) {
            
        }
        
        $f->delete();
    }
    
    function testWriteAndTruncate() {
        $f = new File("/test/reader_writer/test_truncate.txt");
        $w = $f->openWriter();
        
        $w->write("abcdefghij");
        $w->write("1234567890");
        
        $this->assertEquals($f->getSize(),20,"La dimensione del file non corrisponde!!");
        
        $w->truncate(10);
        
        $this->assertEquals($f->getSize(),10,"La dimensione del file non corrisponde!!");
        
        $w->close();
        
        $r = $f->openReader();
        
        $st = $r->read(10);
        
        $this->assertEquals("abcdefghij",$st,"La stringa letta dopo la scrittura non corridponde!!");
        
        $r->close();
        
        $f->delete();
    }

}

