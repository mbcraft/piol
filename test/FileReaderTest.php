<?php

use Mbcraft\Piol\IOException;
use Mbcraft\Piol\File;
use Mbcraft\Piol\FileReader;
use Mbcraft\Piol\Utils\CsvUtils;

class FileReaderTest extends PHPUnit_Framework_TestCase
{
    function testAvailable() {
        $f = new File("/test/reader_writer/myfile_01.txt");
        
        $this->assertEquals(24,$f->getSize(),"La dimensione del file non è quella attesa.");
        
        $reader = $f->openReader();
        
        $this->assertEquals($reader->available(), 24,"Il numero dei byte disponibili non corrisponde.");
    
        $reader->read(12);
        
        $this->assertEquals($reader->available(), 12,"Il numero dei byte disponibili non corrisponde.");
    
        $reader->read(12);
        
        $this->assertEquals($reader->available(), 0,"Il numero dei byte disponibili non corrisponde.");
        $this->assertTrue($reader->isEndOfStream(),"La fine dello stream non è stata raggiunta!");
    }
    
    function testReader0()
    {
        $f = new File("/test/reader_writer/myfile_01.txt");
        try
        {
            $reader = $f->openReader();
            $this->assertTrue($reader instanceof FileReader,"L'oggetto non e' del tipo specificato!!");

            $v = $reader->read(1);
            
            $this->assertEquals('d', $v,"Il valore letto non corrisponde!!");
            
            $c = $reader->readChar();
            
            $this->assertEquals('f', $c,"Il valore letto non corrisponde!!");
        }
        catch (IOException $ex)
        {
            $this->fail("Errore nell'apertura del reader di un file esistente!!");
        }

    }
    
    function testReader1()
    {
        $f = new File("/test/reader_writer/myfile_01.txt");
        try
        {
            $reader = $f->openReader();
            $this->assertTrue($reader instanceof FileReader,"L'oggetto non e' del tipo specificato!!");

            $this->assertTrue($reader->isOpen(),"Il reader non e' aperto!!");
            $reader->close();
            $this->assertFalse($reader->isOpen(),"Il reader non e' stato chiuso!!");
        }
        catch (IOException $ex)
        {
            $this->fail("Errore nell'apertura del reader di un file esistente!!");
        }

    }

    function testReader2()
    {
        $f = new File("/test/reader_writer/non_existent_file.txt");

        $this->assertFalse($f->exists(),"Il file esiste!!");
        
        try {
            $f->openReader();
            $this->fail();
        } catch (IOException $ex) {
            
        }
    }
    
    function testReaderSkipScanf()
    {
        $f = new File("/test/reader_writer/scanf_test.txt");

        $reader = $f->openReader();

        $reader->skip(3);
        
        $this->assertEquals(3,$reader->pos(),"La posizione non corrisponde!!");
        
        $result = $reader->scanf("%2d");

        $this->assertEquals($result[0],44,"Il valore letto non è 44!!");

    }

    function testReaderScanf()
    {
        $f = new File("/test/reader_writer/scanf_test.txt");

        $reader = $f->openReader();

        $result = $reader->scanf("%2d %2d %s");
        $result2 = $reader->scanf("age:%d weight:%dkg");

        $this->assertEquals($result[0],12,"Il valore letto non è 12!!");
        $this->assertEquals($result[1],44,"Il valore letto non è 44!!");
        $this->assertEquals($result[2],"John","Il valore letto non è John!!");

        $this->assertEquals($result2[0],30,"Il valore letto non e' 30!! : ".$result2[0]);
        $this->assertEquals($result2[1],60,"Il valore letto non e' 60!! : ".$result2[1]);
    }

    function testReaderSeek()
    {
        $f = new File("/test/reader_writer/scanf_test.txt");

        $reader = $f->openReader();

        $result = $reader->scanf("%2d %2d %s");
        $reader->seek(0);

        $this->assertEquals($reader->pos(),0,"La posizione non e' tornata zero dopo seek(0)!!!");

        $result_b = $reader->scanf("%2d %2d %s");
            
        $this->assertFalse($reader->isEndOfStream(),"Lo stream non risulta essere concluso!!");
    
        $pos_after_read = $reader->pos();

        $this->assertEquals($result[0],$result_b[0],"I valori letti non corrispondono!!");
        $this->assertEquals($result[1],$result_b[1],"I valori letti non corrispondono!!");
        $this->assertEquals($result[2],$result_b[2],"I valori letti non corrispondono!!");

        $reader->seek(0);
        $reader->seek($pos_after_read);
        
        $result_b = $reader->scanf("age:%d weight:%dkg");
        
        $this->assertEquals($result_b[0],30,"Il valore letto non e' 30!! : ".$result_b[0]);
        $this->assertEquals($result_b[1],60,"Il valore letto non e' 60!! : ".$result_b[1]);
        
        $this->assertTrue($reader->isEndOfStream(),"Lo stream non risulta essere concluso!!");
    }

    function testWriterSeek()
    {
        $f = new File("/test/reader_writer/printf_test.txt");

        $writer = $f->openWriter();

        $writer->printf("%2d %2d %2d",12,34,56);
        $writer->reset();

        $this->assertEquals($writer->pos(),0,"La posizione non e' tornata zero dopo seek(0)!!!");

        $writer->printf("%2d",99);

        $writer->reset();

        $this->assertEquals($writer->pos(),0,"La posizione non e' corretta dopo la seek del writer : ".$writer->pos());

        $result = $writer->scanf("%2d %2d %2d");

        $this->assertEquals($result[0],99,"I valori letti non corrispondono!! : ".$result[0]);
        $this->assertEquals($result[1],34,"I valori letti non corrispondono!! : ".$result[1]);
        $this->assertEquals($result[2],56,"I valori letti non corrispondono!! : ".$result[2]);
    }
    
    function testReadCsv2() {
        $f = new File("/test/csv_test/FL_insurance_sample.csv");
        
        $values = explode(",","119736,FL,CLAY COUNTY,498960,498960,498960,498960,498960,792148.9,0,9979.2,0,0,30.102261,-81.711777,Residential,Masonry,1");
    
        $reader = $f->openReader();
        
        $header = $reader->readLine();
        
        $this->assertEquals("policyID,statecode,county,eq_site_limit,hu_site_limit,fl_site_limit,fr_site_limit,tiv_2011,tiv_2012,eq_site_deductible,hu_site_deductible,fl_site_deductible,fr_site_deductible,point_latitude,point_longitude,line,construction,point_granularity",$header,"L'intestazione del file csv non corrisponde!!");
        
        $read_values = CsvUtils::read($reader);
        
        $this->assertEquals(count($values),count($read_values),"Il numero dei valori letti non corrisponde!!");
        
        for ($i=0;$i<count($values);$i++) {
            $this->assertEquals($values[$i],$read_values[$i],"Il valore letto non corrisponde!!");
        }
        
    }
    
    function testReadCsv() {
        $f = new File("/test/csv_test/test_dati.csv");
        
        $reader = $f->openReader();
        $header = $reader->readLine(); //salto la prima riga
        
        $this->assertEquals("id;nome;cognome;descrizione",$header,"Le intestazioni del csv non corrispondono!!");
        
        $values = CsvUtils::read($reader);
        
        $this->assertEquals("1",$values[0],"Il valore letto non corrisponde!!");
        $this->assertEquals("marco",$values[1],"Il valore letto non corrisponde!!");
        $this->assertEquals("bagnaresi",$values[2],"Il valore letto non corrisponde!!");
        $this->assertEquals("programmatore, curioso",$values[3],"Il valore letto non corrisponde!!");
        
        $values = CsvUtils::read($reader);
        
        $this->assertEquals("2",$values[0],"Il valore letto non corrisponde!!");
        $this->assertEquals("federica",$values[1],"Il valore letto non corrisponde!!");
        $this->assertEquals("amarisse",$values[2],"Il valore letto non corrisponde!!");
        $this->assertEquals("segretaria, \"puccettosa\"",$values[3],"Il valore letto non corrisponde!!");
        
        $values = CsvUtils::read($reader);
        
        $this->assertEquals("3",$values[0],"Il valore letto non corrisponde!!");
        $this->assertEquals("stefano",$values[1],"Il valore letto non corrisponde!!");
        $this->assertEquals("pelloni",$values[2],"Il valore letto non corrisponde!!");
        $this->assertEquals("'cuoco' e responsabile grafica e web",$values[3],"Il valore letto non corrisponde!!");

    }

}
