<?php

use Mbcraft\Piol\Storage;
use Mbcraft\Piol\StorageFactory;
use Mbcraft\Piol\Dir;
use Mbcraft\Piol\File;

class StorageTest extends PHPUnit_Framework_TestCase {


    function testStorageWorkingWithSlashInFolderName() 
    {
        StorageFactory::setStorageRoot("/test/storage_dir/");

        $p1 = StorageFactory::getPropertiesStorage("pippo/pluto", "ciao");
        
        $p1->add(array ("prova" => array("a","b","c")));
        $p1->remove("prova");
        
        $p1->add(array("prova2" => array("ciccia")));
        $props = $p1->readAll();
        
        $this->assertEquals($props["prova2"][0],"ciccia","La proprieta' non e' stata salvata con successo!!");
        
                
        $p2 = StorageFactory::getPropertiesStorage("/pluto", "ciaoasd");
        $p3 = StorageFactory::getPropertiesStorage("pippo/", "ciaoqwe");

        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }
    

    function testStorageRootExists() {
        StorageFactory::setStorageRoot("/test/permanent_storage/");

        $this->assertTrue(StorageFactory::storageRootExists(), "La storage root non esiste!!");

        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }
    
    function testXmlStorageExists() {
        StorageFactory::setStorageRoot("/test/permanent_storage/");

        $storage = StorageFactory::getXMLStorage("my_folder", "storage_03");
        $this->assertTrue($storage->exists(), "Lo storage non esiste!!");
        $this->assertEquals("my_folder", $storage->getFolder());
        $this->assertEquals(StorageFactory::XML_STORAGE,$storage->getStorageType(),"Il tipo di storage non corrisponde!!");


        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }
    
    function testDataStorageExists() {
        StorageFactory::setStorageRoot("/test/permanent_storage/");

        $storage = StorageFactory::getDataStorage("my_folder", "storage_04");
        $this->assertTrue($storage->exists(), "Lo storage non esiste!!");
        $this->assertEquals("my_folder", $storage->getFolder());
        $this->assertEquals(StorageFactory::DATA_STORAGE,$storage->getStorageType(),"Il tipo di storage non corrisponde!!");


        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }

    function testGetStorageFolder() {
        StorageFactory::setStorageRoot("/test/permanent_storage/");

        $storage = StorageFactory::getPropertiesStorage("my_folder", "storage_01");
        $this->assertTrue($storage->exists(), "Lo storage non esiste!!");
        $this->assertEquals("my_folder", $storage->getFolder());

        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }

    function testGetStorageName() {
        StorageFactory::setStorageRoot("/test/permanent_storage/");
        
        $this->assertEquals("/test/permanent_storage/",StorageFactory::getStorageRoot(),"La root dello storage non corrisponde!!");

        $storage = StorageFactory::getPropertiesStorage("my_folder", "storage_02");
        $this->assertTrue($storage->exists(), "Lo storage non esiste!!");
        $this->assertEquals("storage_02", $storage->getName());
        $this->assertEquals(StorageFactory::PROPERTIES_STORAGE,$storage->getStorageType(),"Il tipo di storage non corrisponde!!");

        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }

    function testGetStorageGetAll() {
        StorageFactory::setStorageRoot("/test/permanent_storage/");

        $storages = StorageFactory::getAll("my_folder");
        $this->assertEquals(count($storages), 4, "Il numero degli storage non corrisponde!");

        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }

    function testStorageDirMustExists() {
        StorageFactory::setStorageRoot("/test/bad_storage/");

        try {
            StorageFactory::getPropertiesStorage("boh", "ciccia");
            $this->fail("La directory base dello storage deve essere presente!!");
        } catch (Exception $ex) {
            //tutto ok
        }
    }

    function testStorageCreateIfNotPresent() {
        StorageFactory::setStorageRoot("/test/storage_dir/");

        $d = new Dir("/test/storage_dir/");
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $elements = array_merge($r[0],$r[1]);
        foreach ($elements as $f)
            $f->delete(true);

        $this->assertFalse($d->hasOnlyOneSubdir(), "Lo storage e' gia' presente!!");

        $storage = StorageFactory::getPropertiesStorage("boh", "ciccia");

        $this->assertTrue($d->hasOnlyOneSubdir(), "Lo storage non e' stato creato!!");

        $this->assertFalse($storage->exists());
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $all_elements = array_merge($r[0],$r[1]);
        foreach ($all_elements as $f)
            $f->delete(true);

        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }
    

    function testCreateStorageFileOnSave() {
        StorageFactory::setStorageRoot("/test/storage_dir/");

        $d = new Dir("/test/storage_dir/");
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $all_elements = array_merge($r[0],$r[1]);
        foreach ($all_elements as $f)
            $f->delete(true);

        $this->assertFalse($d->hasOnlyOneSubdir(), "Lo storage e' gia' presente!!");

        $storage = StorageFactory::getPropertiesStorage("boh", "ciccia");

        $this->assertFalse($storage->exists(), "Lo storage esiste gia'!!");

        $storage->create();

        $this->assertTrue($storage->exists(), "Lo storage non e' stato creato!!");

        $storage->delete();

        $this->assertFalse($storage->exists(), "Lo storage non e' stato eliminato!!");


        $this->assertFalse($storage->exists());
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $all_elements = array_merge($r[0],$r[1]);
        foreach ($all_elements as $f)
            $f->delete(true);

        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }
    
    function testGetStoragegetByExtension() {
        
        //$d = new Dir("/test/properties_dir/storage/");
        
        //$s = StorageFactory::getByExtension($d, "save_sections_test", "ini");
        
    }

    function testCreateStorageOnWrite() {
        StorageFactory::setStorageRoot("/test/storage_dir/");

        $d = new Dir("/test/storage_dir/");
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $all_elements = array_merge($r[0],$r[1]);
        foreach ($all_elements as $f)
            $f->delete(true);

        $this->assertFalse($d->hasOnlyOneSubdir(), "Lo storage e' gia' presente!!");

        $storage = StorageFactory::getPropertiesStorage("boh", "ciccia");

        $this->assertFalse($storage->exists(), "Lo storage esiste gia'!!");

        $test_props = array("test" => "value", "hello" => "world");

        $storage->add(array("category" => $test_props));

        $this->assertTrue($storage->exists(), "Lo storage non e' stato creato!!");

        $storage->delete();

        $this->assertFalse($storage->exists(), "Lo storage non e' stato eliminato!!");

        $properties = $storage->readAll(); //readAll
        $this->assertTrue($storage->exists(), "Lo storage non e' stato creato per una lettura!!");
        $this->assertFalse($properties === null, "Il risultato ritornato e' ===null !!");
        $this->assertTrue(is_array($properties), "Il metodo non ha ritornato un array con uno storage inesistente!!");
        $this->assertTrue(count($properties) == 0, "L'array ritornato da una lettura di storage vuoto non e' vuoto!!");

        $storage->delete();

        $storage->remove("blah");   //remove
        $this->assertTrue($storage->exists(), "Lo storage non e' stato creato per una cancellazione!!");

        $storage->delete();

        $storage->saveAll(array()); //saveAll
        $this->assertTrue($storage->exists(), "Lo storage non e' stato creato per una cancellazione!!");

        $storage->delete();

        $this->assertFalse($storage->exists(), "Lo storage non e' stato eliminato!!");
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $all_elements = array_merge($r[0],$r[1]);
        foreach ($all_elements as $f)
            $f->delete(true);

        StorageFactory::setStorageRoot(StorageFactory::getDefaultStorageRoot());
    }
    
    public function testCleanStorage() {
        $d = new Dir("/test/storage_dir/");
        
        StorageFactory::setStorageRoot($d);
        
        $ps = StorageFactory::getPropertiesStorage("prova1", "ciccio");
        $ps->create();
        $ps = StorageFactory::getPropertiesStorage("prova2", "ciccio");
        $ps->create();
        $ps = StorageFactory::getPropertiesStorage("prova3", "ciccio");
        $ps->create();
        
        $uf = $d->getUniqueSubdir();
               
        $this->assertEquals(3,count($uf->listFolders()),"Il numero delle cartelle presenti nello storage non corrisponde!!");
        
        StorageFactory::clean();
        
        $this->assertFalse($uf->exists(),"La cartella random non è stata eliminata!!");
        
    }
    
}

?>