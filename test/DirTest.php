<?php

use Mbcraft\Piol\File;
use Mbcraft\Piol\Dir;
use Mbcraft\Piol\IOException;

class FolderVisitor {
    
    private $mode;
    
    public function __construct($mode) {
        $this->mode = $mode;
    }
    
    private $visited = array();
    
    function visit($folder) {
        $this->visited[] = $folder->getPath();
        return $this->mode;
    }
    
    function getVisitedFolders() {
        return $this->visited;
    }
}

class DirTest extends PHPUnit_Framework_TestCase
{
    function testMoveTo() {
        $d = new Dir("/test/move_source/my_dir/");
        $d->touch();
        $f = new File("/test/move_source/my_dir/afile.txt");
        $f->touch();
        $f->setContent("abc");
        
        $target_dir = new Dir("/test/move_target/");
        
        $dt = new Dir("/test/move_target/my_dir/");
        $this->assertFalse($dt->exists(),"La directory nella directory target esiste già!!");
        
        $d->moveTo($target_dir);
        
        $this->assertFalse($d->exists(),"La directory non è stata spostata!!");
        $this->assertTrue($dt->exists(),"La directory nella directory target non esiste!!");
        
        $ft = new File("/test/move_target/my_dir/afile.txt");
        $this->assertEquals("abc",$ft->getContent(),"Il contenuto del file spostato non corrisponde!!");
        
        $dt->delete(true);
    }
    
        function testMoveToWithRename() {
        $d = new Dir("/test/move_source/my_dir/");
        $d->touch();
        $f = new File("/test/move_source/my_dir/afile.txt");
        $f->touch();
        $f->setContent("abc");
        
        $target_dir = new Dir("/test/move_target/");
        
        $dt = new Dir("/test/move_target/moved_dir/");
        $this->assertFalse($dt->exists(),"La directory nella directory target esiste già!!");
        
        $d->moveTo($target_dir,"moved_dir");
        
        $this->assertFalse($d->exists(),"La directory non è stata spostata!!");
        $this->assertTrue($dt->exists(),"La directory nella directory target non esiste!!");
        
        $ft = new File("/test/move_target/moved_dir/afile.txt");
        $this->assertEquals("abc",$ft->getContent(),"Il contenuto del file spostato non corrisponde!!");
        
        $dt->delete(true);
    }
    
    function testListElementsOnlyFolders() {
        $d = new Dir("/test/properties_dir/");
        
        $results = $d->listElements(Dir::MODE_FOLDERS_ONLY);
        
        $this->assertEquals(2,count($results[1]),"Il numero dei risultati non corrisponde!!");
    }
    
    function testListFoldersDeep() {
        $d = new Dir("/test/zip_test/");
        
        $results = $d->listFolders(Dir::DEFAULT_EXCLUDES,true);
        
        $this->assertEquals(7,count($results),"Il numero dei risultati non corrisponde!!");
    }
    
    function testListFilesDeep() {
        $d = new Dir("/test/zip_test/");
        
        $results = $d->listFiles(Dir::DEFAULT_EXCLUDES,true);
        
        $this->assertEquals(3,count($results),"Il numero dei risultati non corrisponde!!");
    }
    
    function testListFolders() {
        $d = new Dir("/test/properties_dir/");
        
        $results = $d->listFolders();
        
        $this->assertEquals(2,count($results),"Il numero dei risultati non corrisponde!!");
    }
    
    function testListFiles() {
        $d = new Dir("/test/properties_dir/");
        
        $results = $d->listFiles();
        
        $this->assertEquals(3,count($results),"Il numero dei risultati non corrisponde!!");

    }
    
    function testListElementsOnlyFiles() {
        $d = new Dir("/test/properties_dir/");
        
        $results = $d->listElements(Dir::MODE_FILES_ONLY);
        
        $this->assertEquals(3,count($results[0]),"Il numero dei risultati non corrisponde!!");

    }
    
    function testRecursivePermissionsOnDir() {
        $d = new Dir("/test/permissions_test/rec/");
        $d->touch();
        
        $d1 = new Dir("/test/permissions_test/rec/d1/");
        $d1->touch();
        
        $d2 = new Dir("/test/permissions_test/rec/d1/d2/");
        $d2->touch();
        $this->assertTrue($d2->exists(),"La directory interna non è stata creata!!");
        
        $f1 = new File("/test/permissions_test/rec/d1/f1.txt");
        $f1->setContent("abc");
        $this->assertTrue($f1->exists(),"Il file interno non è stata creato!!");
        
        $f2 = new File("/test/permissions_test/rec/d1/d2/f2.txt");
        $f2->setContent("xyz");
        $this->assertTrue($f2->exists(),"Il file interno non è stata creato!!");

        $p = "rwxr-xr-x";
        $do_permissions_tests = $d->setPermissions($p,true);
        
        if ($do_permissions_tests) {
        
            $this->assertEquals($p,$d->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
            $this->assertEquals($p,$d1->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
            $this->assertEquals($p,$f1->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
            $this->assertEquals($p,$d2->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
            $this->assertEquals($p,$f2->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");

            $p2 = "rwxr-----";
            $d->setPermissions($p2,true);

            $this->assertEquals($p2,$d->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
            $this->assertEquals($p2,$d1->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
            $this->assertEquals($p2,$f1->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
            $this->assertEquals($p2,$d2->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
            $this->assertEquals($p2,$f2->getPermissions(),"I permessi impostati ricorsivamente non corrispondono!");
        
        }
        
        $d->delete(true);
    }
    
    function testPermissionsOnDir() {
        $d = new Dir("/test/permissions_test/dir_0/");
        $d->touch();
        
        $do_permissions_tests = $d->setPermissions("r--------");
        
        if ($do_permissions_tests) 
            $this->assertEquals("r--------",$d->getPermissions(),"I permessi non sono stati impostati correttamente sulla cartella!!");
        $d->setPermissions("rwx------");    
        $d->delete();
        $this->assertFalse($d->exists(),"La directory dir_0 non è stata cancellata!!");
        
        $d = new Dir("/test/permissions_test/dir_1/");
        $d->touch();
        
        $do_permissions_tests = $d->setPermissions("rw-------");
        if ($do_permissions_tests) 
            $this->assertEquals("rw-------",$d->getPermissions(),"I permessi non sono stati impostati correttamente sulla cartella!!");
        $d->setPermissions("rwx------");
        $d->delete();
        $this->assertFalse($d->exists(),"La directory dir_1 non è stata cancellata!!");
        
        $d = new Dir("/test/permissions_test/dir_2/");
        $d->touch();
        
        $do_permissions_tests = $d->setPermissions("rwx------");
        if ($do_permissions_tests) 
            $this->assertEquals("rwx------",$d->getPermissions(),"I permessi non sono stati impostati correttamente sulla cartella!!");
        $d->setPermissions("rwx------");
        $d->delete();
        $this->assertFalse($d->exists(),"La directory dir_2 non è stata cancellata!!");
        
    }

    function testHasSubdirs()
    {
        $d = new Dir("/test/advances_dir_list/");
        $this->assertFalse($d->hasSubdirs(),"Sono state trovate sottocartelle in una cartella che non ne ha!!");
    
        $d2 = new Dir("/test/copy_source/");
        $this->assertTrue($d2->hasSubdirs(),"Non sono state trovate sottocartelle in una cartella che ne ha!!"); 
    }
    
    function testFindMatchingElements_Deep() {
        $d = new Dir("/test/list_files_test/");
        $results = $d->findMatchingElements(Dir::MODE_FILES_ONLY, "/\A.*txt\Z/", true);
        
        $this->assertEquals(2,count($results[0]),"Il numero di file trovati non corrisponde!!");
        $this->assertEquals(0,count($results[1]),"Il numero di cartelle trovate non corrisponde!!"); 
    }
    
    function testFindFilesEndingWith_Deep() {
        $d = new Dir("/test/list_files_test/");
        $results = $d->findElementsEndingWith(Dir::MODE_FILES_ONLY, "txt", true);
        
        $this->assertEquals(2,count($results[0]),"Il numero di file trovati non corrisponde!!");
        $this->assertEquals(0,count($results[1]),"Il numero di cartelle trovate non corrisponde!!"); 
    }
       
    function testFindFoldersStartingWith_Deep() {
        $d = new Dir("/test/search_test/");
        $results = $d->findElementsStartingWith(Dir::MODE_FOLDERS_ONLY, "f_", true);
        
        $this->assertEquals(0,count($results[0]),"Il numero di file trovati non corrisponde!!");
        $this->assertEquals(6,count($results[1]),"Il numero di cartelle trovate non corrisponde!! : ".count($results[1])); 
    }

    function testFindFilesBasic()
    {
        $d = new Dir("/test/advances_dir_list/");
        
        $only_menu_ini_results = $d->findMatchingElements(Dir::MODE_FILES_AND_FOLDERS,"/[_]*menu[\.]ini/");
        $this->assertTrue((count($only_menu_ini_results[0])+count($only_menu_ini_results[1]))==1,"Il numero dei file trovati non corrisponde!!");       
        
    }
    
    function testFindFilesStartingWithBasic()
    {
        $d = new Dir("/test/advances_dir_list/");
        
        $only_the_starting_results = $d->findElementsStartingWith(Dir::MODE_FILES_ONLY,"the");
        $this->assertTrue((count($only_the_starting_results[0])+count($only_the_starting_results[1]))==1,"Il numero dei file trovati non corrisponde!!");       
        
    }

    function testFindFilesEndingWithBasic()
    {
        $d = new Dir("/test/advances_dir_list/");
        
        $only_image_png = $d->findElementsEndingWith(Dir::MODE_FILES_ONLY,"image.png");
        
        $this->assertTrue(count($only_image_png)==2,"Il numero dei file trovati non corrisponde!!");       
        
    }
    
    function testSubdirs()
    {
        $root = new Dir("/test/test_dir/");
        
        $subfolder = new Dir("/test/test_dir/content_dir");
        
        $this->assertTrue($root->isAncestorOrSame($subfolder));
        $this->assertFalse($subfolder->isAncestorOrSame($root));
        $this->assertTrue($root->isAncestorOrSame($root));
        $this->assertTrue($subfolder->isAncestorOrSame($subfolder));
         
    }
    
    function testNewRandomSubdir() {
        $folder = new Dir("/test/more_dir_tests/");
        
        $this->assertTrue($folder->isEmpty(),"La directory di test non è vuota!!");
        
        $this->assertFalse($folder->hasOnlyOneSubdir(),"La directory di test risulta già avere una e una sola sottocartella!!");
        
        $subdir = $folder->newRandomSubdir();
        
        $this->assertTrue($folder->hasOnlyOneSubdir(),"La directory di test non risulta avere una e una sola sottocartella!!");
        
        $this->assertTrue($subdir->exists(),"La directory random esiste già!");
        
        //echo "a: ".$folder->getPath()."\n";
        //echo "b: ".dirname($subdir->getPath()).DIRECTORY_SEPARATOR."\n";
        
        $this->assertTrue($folder->isParentOf($subdir),"La directory creata non risulta figlia della sua radice!!");
        $this->assertTrue($subdir->isChildOf($folder),"La directory creata non risulta figlia della sua radice!!");

        $this->assertTrue($folder->isAncestorOrSame($subdir),"La radice non risulta avere la sottodirectory!!");
        
        $another_subdir_handle = $folder->getUniqueSubdir();
        
        $this->assertEquals($another_subdir_handle->getPath(), $subdir->getPath(), "I due handle della stessa directory risultano avere un percorso diverso!!");
        
        $subdir->delete();
        
        $this->assertFalse($subdir->exists(),"La directory random non è stata eliminata!!");
        $this->assertFalse($another_subdir_handle->exists(),"La directory random non è stata eliminata!!");
     
    }

    function testDirLevel()
    {
        $level_0 = new Dir("/");
        $this->assertEquals($level_0->getLevel(),0,"Il livello 0 della directory non e' corretto : ".$level_0->getLevel());
        
        $level_1 = new Dir("/test/");
        $this->assertEquals($level_1->getLevel(),1,"Il livello 1 della directory non e' corretto : ".$level_1->getLevel());
        
        $level_3 = new Dir("/test/js/mooo/");
        $this->assertEquals($level_3->getLevel(),3,"Il livello 3 della directory non e' corretto : ".$level_3->getLevel());
        
    }
    
    function testEquals()
    {
        $dir1= new Dir("/test/test_dir/");
        
        $dir2 = new Dir("/test/test_dir");
        
        $this->assertTrue($dir1->equals($dir2),"Le directory non coincidono!!");
    }

    function testRootTestDirectory()
    {
        $d1 = new Dir("/test/test_dir/");

        $this->assertTrue($d1->exists(),"La directory di test non esiste!!!");
        $this->assertTrue($d1->isDir(),"La directory di test non è una directory!!!");
        $this->assertFalse($d1->isFile(),"La directory di test è un file!!!");
        
        $this->assertFalse($d1->isEmpty(),"La directory di test è vuota!!!");
    }

    function testEmptyDirectory()
    {
        $d2 = new Dir("/test/test_dir/empty_dir");

        $this->assertTrue($d2->exists());
        $this->assertTrue($d2->isDir());
        $this->assertFalse($d2->isFile());
        //$this->assertTrue($d2->isEmpty()); //.svn ???
    }

    function verifyContentDir($content_dir)
    {
        $this->assertTrue($content_dir->isDir());
        $this->assertFalse($content_dir->isEmpty());
    }

    function verifyEmptyDir($empty_dir)
    {
        $this->assertTrue($empty_dir->isDir());

        $subdir = $empty_dir->newSubdir("test");
        
        $this->assertTrue($subdir->isEmpty());

        $this->assertTrue($subdir->delete(),"Impossibile cancellare una cartella vuota.");

        $this->assertFalse($subdir->exists());
    }

    function testGetParentDir()
    {
        $d1 = new Dir("/test/test_dir/empty_dir");
        
        $parent = $d1->getParentDir();
        
        $this->assertEquals("/test/test_dir/",$parent->getPath());
    }

    function testDirectoryContent()
    {
        $d1 = new Dir("/test/test_dir/");

        $this->assertTrue($d1->isDir());

        $r = $d1->listElements();
        $this->assertTrue(count($r[0])==0,"Sono presenti dei file nella cartella!!");
        
        $this->assertEquals(3,count($r[1])); //.svn ???

        foreach ($r[1] as $dir)
        {
            if ($dir->getName()=="content_dir")
                $this->verifyContentDir($dir);
            else
                $this->verifyEmptyDir($dir);
        }
    }
    
    function testDirectoryContent2() {
        $d1 = new Dir("/test/test_dir/");
        
        $folders = array();
        foreach ($d1->listFolders() as $d) {
            $folders[] = $d->getName();
        }
        
        $this->assertEquals(3,count($folders),"Il numero delle cartelle non corrisponde!!");
        $this->assertTrue(array_search("content_dir", $folders)!==FALSE,"La cartella content_dir non è presente!!");
        $this->assertTrue(array_search("empty_dir", $folders)!==FALSE,"La cartella empty_dir non è presente!!");
        $this->assertTrue(array_search("single_subdir", $folders)!==FALSE,"La cartella single_subdir non è presente!!");
        
        $d2 = new Dir("/test/test_dir/empty_dir/");
        
        $this->assertEquals(0,count($d2->listFolders()),"Il numero delle cartelle non corrisponde!!");
        
        $d3 = new Dir("/test/test_dir/single_subdir/");
        
        $this->assertEquals(1,count($d3->listFolders()),"Il numero delle cartelle non corrisponde!!");
        
    }
    
    function testDirectoryContent3() {
        $d1 = new Dir("/test/test_dir/");
        
        $folders = array();
        foreach ($d1->listFolders(array("/\A\..*\Z/","/\Acontent.*\Z/")) as $d) {
            $folders[] = $d->getName();
        }
        
        $this->assertEquals(2,count($folders),"Il numero delle cartelle non corrisponde!!");
        $this->assertTrue(array_search("single_subdir", $folders)!==FALSE,"La cartella single_subdir non è presente!!");
        $this->assertTrue(array_search("empty_dir", $folders)!==FALSE,"La cartella empty_dir non è presente!!");
               
    }


    function testHasOnlyOneSubdir()
    {
        $dir = new Dir("/test/test_dir/single_subdir/");
        
        $this->assertTrue($dir->hasOnlyOneSubdir());
        
        $dir2 = new Dir("/test/test_dir/content_dir/");
        
        $this->assertFalse($dir2->hasOnlyOneSubdir());
        
        $dir3 = new Dir("/test/test_dir/single_subdir/blablablax/");
        
        $this->assertFalse($dir3->hasOnlyOneSubdir());
    }

    function testGetUniqueSubdir()
    {
        $dir = new Dir("/test/test_dir/single_subdir/");
        
        $sub_dir = $dir->getUniqueSubdir();
        
        $this->assertEquals("/test/test_dir/single_subdir/blablablax/",$sub_dir->getPath());
    }

    function testGetUniqueSubdirFailManyElements()
    {
        $dir = new Dir("/test/test_dir/content_dir/");
        
        try
        {
            $sub_dir = $dir->getUniqueSubdir();
            $this->fail("Il metodo getUniqueSubdir non ha lanciato l'eccezione prevista.");
        }
        catch (Exception $ex)
        {
        } 
    }

    function testGetUniqueSubdirFailSingleFile()
    {
        $dir = new Dir("/test/test_dir/single_subdir/blablablax/");
        
        try
        {
            $sub_dir = $dir->getUniqueSubdir();
            $this->fail("Il metodo getSingleSubdir non ha lanciato l'eccezione prevista.");
        }
        catch (Exception $ex)
        {
        } 
    }
    
    function testCopy()
    {
        $source_dir = new Dir("/test/copy_source/");
    
        $target_dir = new Dir("/test/copy_target/");
        
        //pulisco la cartella di destinazione
        $results = $target_dir->listElements();
        $all_results = array_merge($results[0],$results[1]);
        foreach ($all_results as $f)
            $f->delete(true);
        
        $results = $source_dir->listElements();
        $source_dir_elems = array_merge($results[0],$results[1]);
        foreach ($source_dir_elems as $elem)
        {
            $elem->copy($target_dir);
        }
        
        $tiny_file = new File("/test/copy_target/my_tiny_file.txt");
        $this->assertTrue($tiny_file->exists(),"Il file non è stato copiato!!");
        $this->assertEquals($tiny_file->getContent(),"TINY TINY TINY","Il contenuto del file non corrisponde!!");
    
        $my_subdir = new Dir("/test/copy_target/my_subdir");
        $this->assertTrue($my_subdir->exists(),"La sottocartella non è stata copiata!!");
        
        $another_file = new File("/test/copy_target/my_subdir/another_file.txt");
        $this->assertTrue($another_file->exists(),"Il file non è stato copiato!!");
        $this->assertEquals($another_file->getContent(),"BLA BLA BLA","Il contenuto del file non corrisponde!!");
    
        $results = $target_dir->listElements();
        $all_results = array_merge($results[0],$results[1]);
        foreach ($all_results as $f)
            $f->delete(true);
    }
    
    function testParentOf() {
        $d1 = new Dir("/test/test_dir/content_dir/");
        $d2 = new Dir("/test/test_dir/");
        
        $this->assertTrue($d2->isParentOf($d1),"La directory d1 non è parent di d2!!");
        
    }

    function testToString() {
        $d = new Dir("/test/touch_test/my_new_dir/");
        
        $this->assertEquals("/test/touch_test/my_new_dir/","".$d,"Il percorso non viene utilizzato come __toString!!");
    }

    function testTouch()
    {
        $d = new Dir("/test/touch_test/my_new_dir/");
        $this->assertFalse($d->exists(),"La directory esiste già!");
        $d->touch();
        $this->assertTrue($d->exists(),"La directory non è stata creata!");
        try
        {
            $d->touch();
        //devo poter fare touch senza eccezioni su una directory che già esiste
        }
        catch (Exception $ex)
        {
            $this->fail("Impossibile fare touch() su una cartella già esistente senza lanciare un'eccezione!!");
        }
        
        $d->delete();
        $this->assertFalse($d->exists(),"La directory non è stata cancellata!");
        
        
    }

    function testTouchSubdirs()
    {
        $d = new Dir("/test/touch_test/my_new_dir/another_dir/again/");
        $this->assertFalse($d->exists(),"La directory esiste già!");
        $d->touch();
        $this->assertTrue($d->exists(),"La directory non è stata creata!");
        try
        {
            $d->touch();
            //devo poter fare touch senza eccezioni su una directory che già esiste
        }
        catch (Exception $ex)
        {
            $this->fail("Impossibile fare touch() su una cartella già esistente senza lanciare un'eccezione!!");
        }

        $d->delete();
        $this->assertFalse($d->exists(),"La directory non è stata cancellata!");

        $d_root = new Dir("/test/touch_test/my_new_dir");
        $d_root->delete(true);
        $this->assertFalse($d->exists(),"La directory root dell'albero esiste ancora!!");

    }

    function testRenameDirs()
    {
        $d = new Dir("/test/rename_test/dir/");
        $d->touch();

        $this->assertTrue($d->exists(),"La directory non e' stata creata!!");

        $f1 = $d->newFile("my_file.txt");
        $f1->setContent("Ciao!!");

        $this->assertTrue($f1->exists(),"Il file non e' stato creato nella cartella!!");

        @mkdir (__DIR__.DIRECTORY_SEPARATOR."rename_test/target/");
        
        $d2 = new Dir("/test/rename_test/target/");
        $d2->delete(true);
        $this->assertFalse($d2->exists(),"La directory esiste gia'!!");
        $this->assertTrue($d->rename("target"));

        $this->assertFalse($d->exists(),"La directory non e' stata rinominata con successo!!");
        $this->assertTrue($d2->exists(),"La directory non e' stata rinominata con successo!!");
        $f2 = new File("/test/rename_test/target/my_file.txt");
        $this->assertTrue($f2->exists(),"Il file non e' stato spostato insieme alla directory!!");

        $d3 = new Dir("/test/rename_test/existing_dir/");
        $this->assertFalse($d2->rename("existing_dir"),"Il rename e' stato effettuato su una directory che gia' esiste!!");

        $this->assertFalse($d2->isEmpty(),"La directory non spostata non contiene piu' il suo file!!");
        $this->assertTrue($d3->isEmpty(),"La directory gia' esistente e' stata riempita con pattume!!");

        
        try {
            $d4 = new Dir("/test/rename_test/another_target/buh/");
            $this->assertFalse($d2->rename("another_target/buh"),"Rename con spostamento andato a buon fine!!");

            $this->fail();
        }
        catch (IOException $ex) {
            
        }
        
        $d2->delete(true);
    }
    
    function testRandomRename() {
        $d = new Dir("/test/more_dir_tests/");
        
        $subdir = $d->newRandomSubdir();
        
        $old_name = $subdir->getFullName();
        
        $this->assertEquals($old_name,$subdir->getName(),"Il nome non coincide con quello appena ottenuto!!");
        $this->assertEquals($old_name,$subdir->getFullName(),"Il nome non coincide con quello appena ottenuto!!");
        
       
        $subdir->randomRename();
        
        $this->assertFalse($subdir->exists(),"L'oggetto ha aggiornato il suo percorso!!");
        
        $this->assertTrue($d->hasOnlyOneSubdir(),"La directory non risulta avere una sola sottocartella!!");

        $this->assertEquals($old_name,$subdir->getName(),"Il nome non è stato cambiato!!");
        $this->assertEquals($old_name,$subdir->getFullName(),"Il nome non è stato cambiato!!");
        
        $subdir2 = $d->getUniqueSubdir();
        
        $subdir2->delete();
        
        $this->assertFalse($d->hasOnlyOneSubdir(),"La directory rinominata non è stata eliminata!!");
        
    }
    
    function testAsDir() {
        $d = new Dir("/test/more_dir_tests/");
        
        $this->assertTrue(Dir::asDir($d) instanceof Dir,"L'istanza ritornata non è una Dir.'");
        $this->assertEquals("/test/more_dir_tests/",Dir::asDir($d)->getPath(),"Il percorso ritornato non corrisponde!!");
        
        $this->assertTrue(Dir::asDir("/test/more_dir_tests/") instanceof Dir,"L'istanza ritornata non è una Dir.'");
        $this->assertEquals("/test/more_dir_tests/",Dir::asDir("/test/more_dir_tests/")->getPath(),"Il percorso ritornato non corrisponde!!");
    }
    
    function testGetInfo() {
        $d = new Dir("/test/more_dir_tests/");
        
        $info = $d->getInfo();
        
        $this->assertTrue(isset($info["full_path"]),"La chiave full_path non è presente!!");
        $this->assertTrue(isset($info["path"]),"La chiave path non è presente!!");
        $this->assertEquals("more_dir_tests",$info["name"],"Il valore della chiave name non corrisponde!!");
        $this->assertEquals("dir",$info["type"],"Il tipo non corrisponde!!");
        $this->assertTrue($info["empty"],"La directory non risulta vuota!!");
    }

    function testPatternHiddenFiles()
    {
        $pattern = Dir::SHOW_HIDDEN_FILES_PATTERN;
        
        $this->assertTrue(preg_match($pattern,".")==1);
        $this->assertTrue(preg_match($pattern,"..")==1);
        $this->assertFalse(preg_match($pattern,".htaccess")==1);
        $this->assertFalse(preg_match($pattern,"prova.txt")==1);
    }

    function testPatternNoHiddenFiles()
    {
        $pattern = Dir::NO_HIDDEN_FILES_PATTERN;
        
        $this->assertTrue(preg_match($pattern,".")==1);
        $this->assertTrue(preg_match($pattern,"..")==1);
        $this->assertTrue(preg_match($pattern,".htaccess")==1);
        $this->assertFalse(preg_match($pattern,"prova.txt")==1);
    }

    function testListElements()
    {
        $d = new Dir("/test/list_files_test/");
        
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS);
        $this->assertEquals(count(array_merge($r[0],$r[1])),2,"Il numero di file col list di default non corrisponde!!");
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS,Dir::DEFAULT_EXCLUDES);
        $this->assertEquals(count(array_merge($r[0],$r[1])),2,"Il numero di file col list di default non corrisponde!!");
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS,Dir::NO_HIDDEN_FILES);
        $this->assertEquals(count(array_merge($r[0],$r[1])),2,"Il numero di file col list di default non corrisponde!!");
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS,Dir::SHOW_HIDDEN_FILES);
        $this->assertTrue(count(array_merge($r[0],$r[1]))>=3,"Il numero di file col list dei file nascosti non corrisponde!!");
           
        $expected_names = array(".htaccess",".svn","plain.txt","a_dir");
        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS,Dir::SHOW_HIDDEN_FILES);
        $all_files = array_merge($r[0],$r[1]);
        
        foreach ($all_files as $f)
        {
            if ($f->isDir())
                $this->assertTrue(array_search($f->getName(),$expected_names)!==false);
            else
                $this->assertTrue(array_search($f->getFullName(),$expected_names)!==false);
        }
    }
    
    function testVisitNoDeep() {
        $visitor = new FolderVisitor(false);
        
        $d = new Dir("/test/test_dir/");
        
        $d->visit($visitor);
        
        $visited_folders = $visitor->getVisitedFolders();
        
        $this->assertEquals(1,count($visited_folders),"Il numero delle cartelle visitate non corrisponde!!");
    
        $this->assertEquals("/test/test_dir/",$visited_folders[0],"La cartella 0 non corrisponde!!");
        
    }
     
    function testVisitDeep() {
        $visitor = new FolderVisitor(true);
        
        $d = new Dir("/test/test_dir/");
        
        $d->visit($visitor);
        
        $visited_folders = $visitor->getVisitedFolders();
        
        $this->assertEquals(6,count($visited_folders),"Il numero delle cartelle visitate non corrisponde!!");
    
        $this->assertEquals("/test/test_dir/",$visited_folders[0],"La cartella 0 non corrisponde!!");
        $this->assertEquals("/test/test_dir/content_dir/",$visited_folders[1],"La cartella 0 non corrisponde!!");
        $this->assertEquals("/test/test_dir/content_dir/another_dir/",$visited_folders[2],"La cartella 0 non corrisponde!!");
        $this->assertEquals("/test/test_dir/empty_dir/",$visited_folders[3],"La cartella 0 non corrisponde!!");
        $this->assertEquals("/test/test_dir/single_subdir/",$visited_folders[4],"La cartella 0 non corrisponde!!");
        $this->assertEquals("/test/test_dir/single_subdir/blablablax/",$visited_folders[5],"La cartella 0 non corrisponde!!");
        
        
    }

    function testDeleteEmptyWithHidden()
    {
        $d = new Dir("/test/delete_test_dir_empty/the_dir/");
        $this->assertTrue($d->exists(),"La cartella dal eliminare non esiste!!");

        $r = $d->listElements(Dir::MODE_FILES_AND_FOLDERS,Dir::SHOW_HIDDEN_FILES);

        if (count($r[0])==0 && count($r[1])==0)
        {
            $d->delete();
            $this->assertFalse($d->exists(),"La cartella dal eliminare e' stata eliminata!!");
        }
        else
        {
            $d->delete();
            $this->assertTrue($d->exists(),"La cartella dal eliminare non e' stata eliminata!!");
        }
        $d->touch();
        
    }

    function testDeleteRealEmpty()
    {
        $d = new Dir("/test/delete_test_dir_empty/real_empty_dir/");
        $this->assertFalse($d->exists(),"La cartella dal eliminare non esiste!!");

        $d->touch();

        $this->assertTrue($d->exists(),"La cartella da eliminare non è stata creata!!");
        $d->delete();
        $this->assertFalse($d->exists(),"La cartella da eliminare non è stata eliminata!!");

    }

    function testDeleteRecursive()
    {
        $d = new Dir("/test/delete_test_dir/");
        $this->assertTrue($d->exists(),"La cartella dal eliminare non esiste!!");
        $this->assertTrue($d->isEmpty(),"La cartella da popolare non e' vuota!!");

        $the_dir = $d->newSubdir("the_dir");

        $blabla = $the_dir->newFile("blabla.ini");
        $blabla->setContent("[section]\n\nchiave=valore\n\n");
        $hidden_test = $the_dir->newSubdir("hidden_test");
        $htaccess = $hidden_test->newFile(".htaccess");
        $htaccess->setContent("RewriteEngine on\n\n");
        
        $prova = $hidden_test->newFile("prova.txt");
        $prova->setContent("Questo e' un file con un testo di prova");
        
        $the_dir->delete(true);
        $this->assertFalse($the_dir->exists(),"La directory non e' stata eliminata!!");
        $this->assertTrue($d->isEmpty(),"Il contenuto della cartella non e' stato rimosso completamente!!");

    }

    function testGetPathRelative()
    {
        $my_included_file = new Dir("/test/include_teiop/");

        //$rel_path = $my_included_file->getPath(new Dir(__DIR__));
        //$this->assertEquals("/io/include_teiop/",$rel_path,"Il percorso relativo non viene elaborato correttamente!! : ".$rel_path);

        $rel_path = $my_included_file->getPath(new Dir("/test/"));
        $this->assertEquals("/include_teiop/",$rel_path,"Il percorso relativo non viene elaborato correttamente!! : ".$rel_path);

        try {
            $this->assertEquals("/include_teiop/",$my_included_file->getPath(new Dir("/pluto/tests/io/include_test")),"Il percorso relativo non viene elaborato correttamente!!");
            $this->fail();
        
        }
        catch (IOException $ex) {
            
        }
    }

     
}

?>