<?php

use Mbcraft\Piol\File;
use Mbcraft\Piol\Dir;
use Mbcraft\Piol\IOException;

class StaticTestDumpRegistry
{
    public static $my_var = 1;
}

class FileTest extends PHPUnit_Framework_TestCase
{
    function testMoveTo() {
        $f = new File("/test/move_source/afile.txt");
        $f->touch();
        $f->setContent("abc");

        $target_dir = new Dir("/test/move_target/");

        $ft = new File("/test/move_target/afile.txt");
        $this->assertFalse($ft->exists(),"Il file nella directory target esiste già!!");

        $f->moveTo($target_dir);

        $this->assertTrue($ft->exists(),"Il file nella directory target non esiste!!");

        $this->assertEquals("abc",$ft->getContent(),"Il contenuto del file spostato non corrisponde!!");

        $ft->delete();
    }
    
    function testMoveToWithRename() {

        $f = new File("/test/move_source/afile.txt");
        $f->touch();
        $f->setContent("abc");
        
        $target_dir = new Dir("/test/move_target/");
        
        $ft = new File("/test/move_target/movedfile.txt");
        $this->assertFalse($ft->exists(),"Il file nella directory target esiste già!!");
        
        $f->moveTo($target_dir,"movedfile.txt");

        $this->assertTrue($ft->exists(),"Il file nella directory target non esiste!!");
        
        $this->assertEquals("abc",$ft->getContent(),"Il contenuto del file spostato non corrisponde!!");
        
        $ft->delete();
    }
    
    function testLastAccessTime()
    {
        $f1 = new File("/test/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_1.dat");
        $f2 = new File("/test/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_2.dat");

        $old_atime = $f1->getLastAccessTime();
        
        $f1->touch();
        
        $this->assertTrue($f1->getLastAccessTime()>$f2->getLastAccessTime(),"Il tempo di accesso non è aggiornato!!");
        $this->assertTrue($old_atime<$f1->getLastAccessTime(),"Il tempo di accesso non è aggiornato!!");
    }

    function testContentHash()
    {
        $f1 = new File("/test/test_dir/content_dir/test_file.txt");

        $this->assertEquals("bca20547e94049e1ffea27223581c567022a5774",$f1->getContentHash(),"L'hash del file non corrisponde!!");
    }

    function testModificationTime()
    {
        $f1 = new File("/test/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_1.dat");
        $f2 = new File("/test/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_2.dat");

        $m1 = $f1->getModificationTime();
        sleep(1);
        $f1->touch();
        $m2 = $f1->getModificationTime();

        $this->assertTrue($m1<$m2,"I tempi di modifica dei file non corrispondono!");
        $this->assertTrue($m1>$f2->getModificationTime(),"I tempi di modifica dei file non corrispondono!");
    }

    function testSetGetContent()
    {
        $f = new File("/test/test_dir/content_dir/test_file.txt");

        $current_content = $f->getContent();

        $this->assertEquals("Test content",$current_content);

        $f->setContent("");

        $this->assertEquals("",$f->getContent());

        $f->setContent("BLA BLA BLA\nBLA BLA\nBLA BLA BLA BLA\nBLA BLA BLA");

        $this->assertEquals("BLA BLA BLA\nBLA BLA\nBLA BLA BLA BLA\nBLA BLA BLA",$f->getContent());

        $f->setContent("Test content");

        $this->assertEquals("Test content",$f->getContent());
    }
    
    function testFilenameMatches() {
        $f_txt = new File("/test/test_dir/content_dir/test_file.txt");
        $this->assertTrue($f_txt->filenameMatches("/.+file\.txt/"),"Il filename non fa match col pattern!!");
    
        $f_plug_txt = new File("/test/test_dir/content_dir/ext_test.plug.txt");
        $this->assertTrue($f_plug_txt->filenameMatches("/.+t\.p.+/"),"Il filename non fa match col pattern!!");
    
    }
    
    function testAsFile() {
        $f_txt = "/test/test_dir/content_dir/test_file.txt";
        
        $f = File::asFile($f_txt);
        
        $this->assertTrue($f instanceof File,"L'oggetto ritornato non è un file!!");
    }
    
    function testNewTempFile() {
        
        $d = new Dir("/test/tmp_dir/");
        
        File::setTmpFileDir($d);
        
        $this->assertEquals($d->getFullPath(),File::getTmpFileDir()->getFullPath(),"La directory dei file temporanei non è stata impostata correttamente!!");
        
        $tmp_file = File::newTempFile();
        
        $tmp_file->setContent("Hello!");
        
        $this->assertEquals("Hello!",$tmp_file->getContent(),"Il contenuto salvato nel file temporaneo non corrisponde!!");
        $tmp_file->delete();
        
    }
    
    function testNewTempFileNonWritableDir() {
        
        $d = new Dir("/test/tmp_dir_no_w/");
        $d->touch();
        $d->setPermissions("r--------");
        
        try {
            File::setTmpFileDir($d);
            $this->fail("Using a non-writable directory as temporary dir should throw an Exception.");
          
        }
        catch (IOException $ex) {
              
        }
        $d->delete();
                
    }


    function testFilename()
    {
        $f_txt = new File("/test/test_dir/content_dir/test_file.txt");
        $this->assertEquals("test_file.txt", $f_txt->getFullName());

        $f_css = new File("/test/test_dir/content_dir/css_test.css");
        $this->assertEquals("css_test.css", $f_css->getFullName());

        $f_plug_txt = new File("/test/test_dir/content_dir/ext_test.plug.txt");
        $this->assertEquals("ext_test.plug.txt", $f_plug_txt->getFullName());
    }

    function testExtensionFullAndLast()
    {
        $f_txt = new File("/test/test_dir/content_dir/test_file.txt");
        $this->assertEquals("txt", $f_txt->getFullExtension());

        $this->assertEquals("txt", $f_txt->getLastExtension());

        $f_css = new File("/test/test_dir/content_dir/css_test.css");
        $this->assertEquals("css", $f_css->getFullExtension());

        $this->assertEquals("css", $f_css->getLastExtension());

        $f_plug_txt = new File("/test/test_dir/content_dir/ext_test.plug.txt");
        $this->assertEquals("plug.txt", $f_plug_txt->getFullExtension());
   
        $this->assertEquals("txt", $f_plug_txt->getLastExtension());


    }
    
    function testGetSizeUpdates() {
        $f_growing_file = new File("/test/test_dir/content_dir/abc.txt");
        
        $f_growing_file->setContent("");
        $this->assertEquals(0,$f_growing_file->getSize(),"La dimensione del file non è corretta!");
        
        $f_growing_file->setContent("a");
        $this->assertEquals(1,$f_growing_file->getSize(),"La dimensione del file non è corretta!");
        
        $f_growing_file->setContent("abcdefghi");
        $this->assertEquals(9,$f_growing_file->getSize(),"La dimensione del file non è corretta!");
        
        $f_growing_file->delete();
    }

    function testGetSize()
    {
        $f_test_file = new File("/test/test_dir/content_dir/test_file.txt");
        $f_ext_test = new File("/test/test_dir/content_dir/ext_test.plug.txt");

        $this->assertEquals(12,$f_test_file->getSize());
        $this->assertEquals(0,$f_ext_test->getSize());
    } 
    
    function testMimeType() {
        $f_jpg = new File("/test/mime_type_test/picture.jpg");
        $f_txt = new File("/test/mime_type_test/test.txt");
        
        $jpg_info = $f_jpg->getInfo();
        $txt_info = $f_txt->getInfo();
        
        $this->assertEquals("image/jpeg",$jpg_info["mime_type"],"Il tipo mime del file jpg non corrisponde!!");
        $this->assertEquals("text/plain",$txt_info["mime_type"],"Il tipo mime del file txt non corrisponde!!");
        
        
    }

    function testCreateNewFile()
    {
        $f_new = new File("/test/test_dir/content_dir/new_file.txt");

        $this->assertNotEquals("", $f_new->getFullPath());
        $this->assertNotNull($f_new->getFullPath());

        //$current_mtime = $f_new->getModificationTime();

        $this->assertFalse($f_new->exists(),"Il file esiste!!");
        $f_new->touch();

        //$new_mtime = $f_new->getModificationTime();

        //$this->assertNotEqual($current_mtime, $new_mtime);

        $this->assertTrue($f_new->exists(),"Il file non è stato creato!");
        $f_new->delete();
        
        $this->assertFalse($f_new->exists(),"Il file esiste!!");
        
    }
    
    function testCopy()
    {
        $source_file = new File("/test/copy_source/my_tiny_file.txt");
        $target_dir = new Dir("/test/copy_target/");
        
        $target_file = new File("/test/copy_target/my_tiny_file.txt");
        $this->assertFalse($target_file->exists(),"Il file esiste già prima di essere copiato!");
        $source_file->copy($target_dir);
        $this->assertTrue($target_file->exists(),"Il file non è stato copiato!!");
        $target_file->delete();
        $this->assertFalse($target_file->exists(),"Il file non è stato eliminato!");
        
    }

    function testIncludeFileOnce()
    {
        $my_var = 1;

        $my_included_file = new File("/test/include_test/include_me_once.php.inc");

        $this->assertTrue($my_included_file->exists());
        $this->assertEquals($my_var,1,"La variabile e' stata modificata!!");

        $this->assertFalse(function_exists("this_is_a_new_function"),"La funzione da caricare e' gia' presente!!");

        $my_included_file->includeFileOnce();

        $this->assertTrue(function_exists("this_is_a_new_function"),"La funzione da caricare non e' stata caricata!!");

        $my_included_file->includeFileOnce();

        $this->assertEquals($my_var,1,"La variabile e' stata incrementata!!");
    }
    
    function testGetParentDir() {
        $my_included_file = new File("/test/include_test/include_me.php.inc");
        
        $parent_dir = $my_included_file->getParentDir();
        
        $this->assertEquals("/test/include_test/",$parent_dir->getPath(),"Il percorso della directory parent non corrisponde!!");
    }
    
    function testToString() {
        $f = new File("/test/my_empty_file.txt");
        
        $this->assertEquals("/test/my_empty_file.txt","".$f,"Il percorso non viene utilizzato come __toString!!");
    }
    
    function testIsEmpty() {
        $f = new File("/test/my_empty_file.txt");
        
        $this->assertFalse($f->exists(),"Il file vuoto esiste già!");
        
        $f->touch();
        
        $this->assertTrue($f->exists(),"Il file vuoto non è stato creato!");
        
        $this->assertTrue($f->isEmpty(),"Il file non risulta essere vuoto!");
        
        $this->assertEquals("my_empty_file",$f->getName(),"Il nome non corrisponde!!");
        
        $f->delete();
        
        $this->assertFalse($f->exists(),"Il file vuoto esiste già!");
        
    }

    function testIncludeFile()
    {
        $my_included_file = new File("/test/include_test/include_me.php.inc");

        $this->assertEquals(StaticTestDumpRegistry::$my_var,1,"La variabile e' stata modificata!!");

        $my_included_file->includeFile();

        $this->assertEquals(StaticTestDumpRegistry::$my_var,2,"La variabile non e' stata incrementata!!");

        $my_included_file->includeFile();

        $this->assertEquals(StaticTestDumpRegistry::$my_var,3,"La variabile non e' stata incrementata!!");
    }

    function testGetPathRelative()
    {
        $my_included_file = new File("/test/include_test/include_me.php.inc");

        $this->assertEquals("/include_test/include_me.php.inc",$my_included_file->getPath(new Dir("/test/")),"Il percorso relativo non viene elaborato correttamente!!");

        $this->assertEquals("/include_me.php.inc",$my_included_file->getPath(new Dir("/test/include_test/")),"Il percorso relativo non viene elaborato correttamente!!");
        $this->assertEquals("/include_me.php.inc",$my_included_file->getPath("/test/include_test/"),"Il percorso relativo non viene elaborato correttamente!!");

        try {
            $this->assertEquals("include_me.php.inc",$my_included_file->getPath(new Dir("/pluto/tests/io/include_test")),"Il percorso relativo non viene elaborato correttamente!!");
        
            $this->fail();
        }
        catch (IOException $ex) {
            //ok, non è ancestor
        }
    }
    
    function testEquals() {
        $f1 = new File("/test/files_to_include/include_and_delete_me.php.inc");
        $f2 = new File("/test/files_to_include/include_and_delete_me.php.inc");
        
        $this->assertTrue($f1->equals($f2),"I file non risultano uguali!!");
        $this->assertTrue($f2->equals($f1),"I file non risultano uguali!!");
        
    }
    
    function testPermissionsHasGet() {
        $f1 = new File("/test/files_to_include/include_and_delete_me.php.inc");
        
        $p = $f1->getPermissions();
        
        $this->assertTrue($f1->hasPermissions($p),"L'identità nei permessi non è verificata!!'");
    
        $do_permissions_tests = $f1->setPermissions("rw-rw-r--");
        
        if ($do_permissions_tests) {
        
            $this->assertTrue($f1->hasPermissions("---------"),"I permessi non corrispondono!!'");
            $this->assertTrue($f1->hasPermissions("r--r--r--"),"I permessi non corrispondono!!");
            $this->assertTrue($f1->hasPermissions("-w--w----"),"I permessi non corrispondono!!");
            $this->assertTrue($f1->hasPermissions("rw-------"),"I permessi non corrispondono!!");

            $this->assertFalse($f1->hasPermissions("--x------"),"I permessi non corrispondono!!");
            $this->assertFalse($f1->hasPermissions("rwx------"),"I permessi non corrispondono!!");
        }
        $f1->setPermissions($p);
    }
    
    function testPermissionsSet1() {
        $f1 = new File("/test/permissions_test/my_file.txt");
        $f1->touch();
        
        $do_permissions_tests = $f1->setPermissions("rwxrwxrwx");
        if ($do_permissions_tests) {
            $this->assertTrue($do_permissions_tests,"La modifica dei permessi non è avvenuta con successo!!");
            $this->assertTrue($f1->hasPermissions("rwxrwxrwx"),"I permessi completi non corrispondono a quelli attesi!!");
        }
        
        $f1->delete();
        
        $f2 = new File("/test/permissions_test/another_file.txt");
        $f2->touch();
        
        $do_permissions_tests = $f2->setPermissions("rw-rw-r--");
        if ($do_permissions_tests) {
            $this->assertTrue($do_permissions_tests,"La modifica dei permessi non è avvenuta con successo!!");
            $this->assertEquals("rw-rw-r--",$f2->getPermissions(),"I permessi modificati non corrispondono a quelli attesi!!");
        }
        $f2->delete();    
    }
    
    function testPermissionsChangeDecrease() {
        $f1 = new File("/test/permissions_test/p1_file.txt");
        $f1->touch();
        
        $do_permissions_tests = $f1->setPermissions("rwxrwxrwx");
        if ($do_permissions_tests) {
            $this->assertTrue($do_permissions_tests,"La modifica dei permessi non è avvenuta con successo!!");
            $this->assertTrue($f1->hasPermissions("rwxrwxrwx"),"I permessi completi non corrispondono a quelli attesi!!");
        
            $r = $f1->setPermissions("rw-rw-r--");
        
            $this->assertTrue($r,"La modifica dei permessi non è avvenuta con successo!!");
            $this->assertEquals("rw-rw-r--",$f1->getPermissions(),"I permessi modificati non corrispondono a quelli attesi!!");
        }
        $f1->delete();    
    }
    
    function testPermissionsChangeIncrease() {
        $f1 = new File("/test/permissions_test/p2_file.txt");
        $f1->touch();
        
        $do_permissions_tests = $f1->setPermissions("rw-rw-r--");
        if ($do_permissions_tests) {
            $this->assertTrue($do_permissions_tests,"La modifica dei permessi non è avvenuta con successo!!");
            $this->assertTrue($f1->hasPermissions("rw-rw-r--"),"I permessi completi non corrispondono a quelli attesi!!");
        
            $r = $f1->setPermissions("rwxrwxrwx");
        
            $this->assertTrue($r,"La modifica dei permessi non è avvenuta con successo!!");
            $this->assertEquals("rwxrwxrwx",$f1->getPermissions(),"I permessi modificati non corrispondono a quelli attesi!!");
        }
        $f1->delete();    
    }
    
    function testIncludeAndDelete()
    {
        $f = new File("/test/files_to_include/include_and_delete_me.php.inc");

        $this->assertTrue($f->exists(),"Il file da includere e cancellare non esiste!!");

        $this->assertFalse(class_exists("IncludeDeletedClass"),"La classe IncludeDeletedClass esiste prima dell'inclusione del file.");

        $f->requireFileOnce();

        $this->assertTrue(class_exists("IncludeDeletedClass"),"La classe IncludeDeletedClass non e' stata caricata dopo l'inclusione del file.");
       
        $content = $f->getContent();

        $f->delete();

        $this->assertFalse($f->exists(),"Il file da includere e cancellare non e' stato eliminato!!");

        $f->touch();

        $f->setContent($content);

        $this->assertTrue($f->exists(),"Il file da includere e cancellare non e' stato rigenerato!!");

    }

    function testBlackHoleExists()
    {
        $f = new File("/test/BlackHoleTest.php");

        $this->assertTrue($f->exists(),"Il test black hole e' stato eliminato!!");
    }

    function testRenameFiles()
    {

        $f1 = new File("/test/rename_test/a/my_file.txt");
        $this->assertFalse($f1->exists(),"Il file f1 esiste!!");

        $f1->setContent("Ciao!!");

        $this->assertTrue($f1->exists(),"Il file f1 non esiste!!");

        $f3 = new File("/test/rename_test/a/another_name_again.txt");
        $this->assertFalse($f3->exists(),"Il file f3 esiste gia'!!");
        $f1->rename("another_name_again.txt");

        $this->assertFalse($f1->exists(),"Il file f1 esiste ancora!!");

        $this->assertTrue($f3->exists(),"Il rename non e' andato a buon fine!!");

        $f3->delete();
    }
    
    function testFileWithDifficultName() {
        
        $f = new File("test/difficult_names/src/00 - Época\ Porteño.txt");
        
        $this->assertTrue($f->exists(),"Il file non è stato trovato!");
        
        $this->assertEquals("00 - Época\ Porteño.txt",$f->getFullName(),"Il nome del file non è letto correttamente!!");
        
        $this->assertEquals("test/difficult_names/src/00 - Época\ Porteño.txt",$f->getPath(),"Il percorso completo del file non corrisponde!");
    
        $this->assertEquals("00 - Época\ Porteño",$f->getName(),"Il nome senza estensione del file non corrisponde!");
        
    }
    
    function testCopyFileWithDifficultName() {
        $f = new File("test/difficult_names/src/00 - Época\ Porteño.txt");
        $d = new Dir("test/difficult_names/dest/");
        
        $f->copy($d);
        
        $copied = new File("test/difficult_names/dest/00 - Época\ Porteño.txt");
        $this->assertTrue($copied->exists(),"Il file non è stato copiato correttamente!");
        $copied->delete();
        $this->assertFalse($copied->exists(),"Il file non è stato eliminato correttamente!");
        
    }

}

?>