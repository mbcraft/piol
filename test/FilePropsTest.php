<?php

use Mbcraft\Piol\StorageFactory;
use Mbcraft\Piol\File;
use Mbcraft\Piol\Dir;

class FilePropsTest extends PHPUnit_Framework_TestCase
{

    function testProps()
    {
        $storage_test_root = "/test/storage_dir/";
        StorageFactory::setStorageRoot($storage_test_root);

        $test_file = new File("/test/FilePropsTest.php");

        $this->assertFalse($test_file->hasAttachedStorage(),"Il file ha delle proprieta' con lo storage vuoto!!");
        
        $storage = $test_file->getAttachedStorage();

        $storage->add(array("test" => array("hello" => 1,"world" => "good")));

        $this->assertTrue($test_file->hasAttachedStorage(),"Il file storage delle proprieta' non e' stato creato!!");
        
        $sum = md5("/test/"); //path is calculated using the parent dir
        $store_subdir = "_".substr($sum,0,1);
        
        $storage_test_root_dir = new Dir($storage_test_root); 
        $real_store_dir = $storage_test_root_dir->getUniqueSubdir();
        $r = $real_store_dir->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $all_dirs = array_merge($r[0],$r[1]);
        $props_file_dir = $r[1][0];
        $this->assertEquals($props_file_dir->getName(),$store_subdir,"La directory creata non corrisponde!!");
        $final_stored_path = new File($real_store_dir->getPath().$props_file_dir->getName().DS.md5("FilePropsTest.php").".ini");
        
        $this->assertTrue($final_stored_path->exists(),"Il file finale delle props non e' stato trovato!!");
        
        $test_file->deleteAttachedStorage();
        $this->assertFalse($test_file->hasAttachedStorage(),"Il file delle proprieta' non e' stato eliminato!!");
        
        
        $r = $real_store_dir->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $all_elements = array_merge($r[0],$r[1]);
        foreach ($all_elements as $f) { $f->delete(true); }
        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());

    }

}

?>