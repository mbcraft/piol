<?php

use Mbcraft\Piol\FileSystemUtils;

class FileSystemUtilsTest extends PHPUnit_Framework_TestCase
{

    function testCurrentAndParentDirectory()
    {
        $this->assertTrue(FileSystemUtils::isCurrentDirName("."));
        $this->assertTrue(FileSystemUtils::isParentDirName(".."));
    }

    function testIsDir()
    {
        $this->assertTrue(FileSystemUtils::isDir("/test/"));
        
    }
    
    function testGetCleanedPath() {
        $path = "/prova//another_dir//again/ok.txt";
        
        $cleaned = FileSystemUtils::getCleanedPath($path);
        
        $this->assertEquals("/prova/another_dir/again/ok.txt", $cleaned,"Il percorso non è stato pulito correttamente!!");
    
        $path2 = "/prova//another_dir//../again/new.dat";
        
        $cleaned2 = FileSystemUtils::getCleanedPath($path2);
        
        $this->assertEquals("/prova/another_dir/again/new.dat", $cleaned2,"Il percorso non è stato pulito correttamente!!");
    }
    
    function testIsValidFilename() {
        
        $valid_filenames = ["a name with some spacesò#°§!!.yoo","a.b.c.txt","[a strange name]x.aaa","{Another strange (name)}.dat","A name@home.goo","Are you sure;-_this is a good name'^èèè.saa"];
        $invalid_filenames = ["COM","COM.dat","I.test?.rar","Another/name.txt","Strange|piped.nam","A good*name.poo"];
    
        foreach ($valid_filenames as $fn) {
            $this->assertTrue(FileSystemUtils::isValidFilename($fn),"Il nome del file non è valido!!");
        }
        foreach ($invalid_filenames as $fn) {
            $this->assertFalse(FileSystemUtils::isValidFilename($fn),"Il nome del file è valido!! : ".$fn);
        }
        
    }

    function testIsFile()
    {
        $this->assertTrue(FileSystemUtils::isFile("/test/FileSystemUtilsTest.php"));
        $this->assertTrue(FileSystemUtils::isFile("/test/test_dir/content_dir/.hidden_file"));
        
        $this->assertFalse(FileSystemUtils::isFile("/test/test_dir/content_dir/i_do_not_exist.txt"));
    }
        
    function testDiskSpace() {
        $current_free_space = FileSystemUtils::getFreeDiskSpace();
        
        $this->assertTrue($current_free_space>0,"Lo spazio libero non è un valore maggiore di zero!");
        $this->assertTrue($current_free_space>10000,"Lo spazio libero non è un valore maggiore di 10k!");
        
        $total_disk_space = FileSystemUtils::getTotalDiskSpace();
        
        $this->assertTrue($total_disk_space>0,"Lo spazio libero non è un valore maggiore di zero!");
        $this->assertTrue($total_disk_space>10000,"Lo spazio libero non è un valore maggiore di 10k!");
    
        $this->assertTrue($total_disk_space>$current_free_space,"Lo spazio totale non è maggiore dello spazio attuale!");
    }

    function testPathPart() {
        
        $path_part1 = "my'apos dir/";
        
        $this->assertEquals("my_apos_dir/", FileSystemUtils::filterPathName($path_part1), "Il nome della directory non è stato modificato correttamente!!'");
    
        $path_part2 = 'this is a "strange" file.txt';
        
        $this->assertEquals('this_is_a__strange__file.txt', FileSystemUtils::filterPathName($path_part2), "Il nome del file non è stato modificato correttamente!!'");
    
    }
    
    function testDifficultFileName() {
        $difficult_file_path = "test/difficult_names/src/00 - Época\ Porteño.txt";
        
        $this->assertTrue(FileSystemUtils::isFile($difficult_file_path),"Il percorso non è riconosciuto come nome di file valido!");
    }
}


