<?php

use Mbcraft\Piol\File;
use Mbcraft\Piol\Utils\IniPropertiesUtils;

class IniPropertiesUtilsTest extends PHPUnit_Framework_TestCase
{
    function testReadPlainFromFile()
    {
        //si presume che vada ma non si sa mai ...
        
        $props_file = new File("/test/properties_dir/props_plain.ini");
        
        $props = IniPropertiesUtils::readFromFile($props_file, false);
        
        $this->assertEquals($props["prop_01"],"ciao");
        $this->assertEquals($props["prop_02"],"mondo");
    }
    
    function testReadSectionsFromFile()
    {
        //si presume che vada ma non si sa mai ...
        
        $props_file = new File("/test/properties_dir/props_sections.ini");
        
        $props = IniPropertiesUtils::readFromFile($props_file, true);
        
        $this->assertEquals(count($props),2,"Il numero delle sezioni non corrisponde!! : ".count($props));
        
        $this->assertEquals($props["ciao"]["chiave"],"mondo");
        $this->assertEquals($props["hello"]["chiave"],"spank");

    }
    
    function testReadNumberedSectionsFromFile()
    {
        //si presume che vada ma non si sa mai ...
        
        $props_file = new File("/test/properties_dir/props_sections_numbered.ini");
        
        $props = IniPropertiesUtils::readFromFile($props_file, true);
       
        $this->assertEquals(count($props),3,"Il numero delle sezioni non corrisponde!! : ".count($props));
        
        $this->assertEquals($props[1]["menu_title"],"Home");
        $this->assertEquals($props[2]["menu_link"],"http://www.mbcraft.it/credits.php");
        $this->assertEquals($props[3]["menu_title"],"Dove siamo");
        $this->assertEquals($props[3]["menu_description"],"Raggiungerci è molto semplice, prendete l'autobus AX8!!");

        //E' necessario usare l'escape per le stringhe!!!
        //ok viene già fatto.
    }
    
    
    function testSaveToFileWithSections()
    {
        $values = array("ciao" => array("prova" => "ok",3 => "world"),2 => array("ancora" => "funziona","strano" => "Questa frase è strana, contiene gli ' e le ()!=£$%&/ !!!"));
    
        $store = new File("/test/properties_dir/storage/save_sections_test.ini"); 
        
        IniPropertiesUtils::saveToFile($store, $values, true);
        
        $read_props = IniPropertiesUtils::readFromFile($store, true);
        
        $this->assertEquals($read_props[2]["ancora"],"funziona");
        $this->assertEquals($read_props[2]["strano"],"Questa frase è strana, contiene gli ' e le ()!=£$%&/ !!!");
        
        $this->assertEquals($read_props["ciao"]["prova"],"ok");
        $this->assertEquals($read_props["ciao"][3],"world"); 
    }
    
        function testSaveToFilePlain()
        {
            $values = array(3 => 12,"ciao" => "prova" , "ok" => 3 , "world" => 2 , "ancora" => "funziona","strano" => "Questa frase è strana, contiene gli ' e le ()!=£$%&/ !!!");

            $store = new File("/test/properties_dir/storage/save_sections_plain.ini"); 

            IniPropertiesUtils::saveToFile($store, $values, false);

            $read_props = IniPropertiesUtils::readFromFile($store, false);

            $this->assertEquals($read_props[3],12);
            $this->assertEquals($read_props["ciao"],"prova");
            $this->assertEquals($read_props["ok"],3);
            $this->assertEquals($read_props["world"],2);
            $this->assertEquals($read_props["ancora"],"funziona");
            $this->assertEquals($read_props["strano"],"Questa frase è strana, contiene gli ' e le ()!=£$%&/ !!!");

        }
        
        function testaddEntriesToFile()
        {
            $file = new File("/test/properties_dir/test_folder_2/add_props.ini");
            if ($file->exists()) $file->delete();
            
            IniPropertiesUtils::addEntriesToFile($file, true, array("prova" => array("ciao" => 1,"mondo" => 2, 3 => "pluto")));
            
            $this->assertTrue($file->exists(),"Il file delle properties non è stato creato!!");
            $props = IniPropertiesUtils::readFromFile($file, true);
            
            $this->assertTrue(count($props)==1,"Il numero delle properties non corrisponde!");
            $entry = $props["prova"];
            $this->assertTrue(count($entry)==3,"Il numero delle voci non corrisponde!");
            $this->assertEquals($entry["ciao"],1);
            $this->assertEquals($entry["mondo"],2);
            $this->assertEquals($entry[3],"pluto");
            
            $file->delete();
            
        }
        
        function testremoveEntriesFromFile()
        {
            $file = new File("/test/properties_dir/test_folder_2/remove_props.ini");
            
            $properties = array("entry1" => array("one" => 1,"two" => 2, "mondo" => "mah"), "entry2" => array("one" => 7,"two" => 16, "mondo" => "blah"), 3 => array("pizza" => 5,"problems"  => 0));
            
            IniPropertiesUtils::saveToFile($file, $properties, true);
            IniPropertiesUtils::removeEntriesFromFile($file, true, "entry2");
            
            $removed = IniPropertiesUtils::readFromFile($file, true);
            
            $this->assertTrue(count($removed)==2,"Il numero delle proprietà non corrisponde!!");
            $this->assertTrue(isset($removed["entry1"]),"entry1 è stata cancellata!!");
            $this->assertFalse(isset($removed["entry2"]),"entry2 non è stata cancellata!!");
            $this->assertTrue(isset($removed[3]),"'3' è stata cancellata!!");
            
            $this->assertEquals($removed["entry1"]["one"],1,"Il valore della properties non corrisponde!");
            $this->assertEquals($removed["entry1"]["two"],2,"Il valore della properties non corrisponde!");
            $this->assertEquals($removed["entry1"]["mondo"],"mah","Il valore della properties non corrisponde!");
            
            $this->assertTrue(count($removed["entry1"])==3,"Il numero delle chiavi non corrisponde!");
            
            $this->assertEquals($removed[3]["pizza"],5,"Il valore della properties non corrisponde!");
            $this->assertEquals($removed[3]["problems"],0,"Il valore della properties non corrisponde!");
            
            $this->assertTrue(count($removed[3])==2,"Il numero delle chiavi non corrisponde!");
            
        }

        function testReadFromString1()
        {
            $myString = <<<END_OF_STRING
[1]
menu_title = Home
menu_link = http://www.mbcraft.it
menu_style = small_font

[2 abu jafar]
menu_title = Credits
menu_link = http://www.mbcraft.it/credits.php

[3]
menu_title = Dove siamo
menu_link = http://www.mbcraft.it/dove_siamo.php
menu_description = "Raggiungerci è molto semplice, prendete l'autobus AX8!!"
END_OF_STRING;
            $props = IniPropertiesUtils::readFromString($myString,true);

            $this->assertTrue(count($props)==3,"Il numero di sezioni non corrisponde!!");
            $this->assertEquals($props[1]["menu_title"],"Home","La properties non corrisponde!! : ".$props[1]["menu_title"]);
            $this->assertEquals($props["2 abu jafar"]["menu_title"],"Credits","La properties non corrisponde!! : ".$props["2 abu jafar"]["menu_title"]);

            $this->assertEquals($props[3]["menu_link"],"http://www.mbcraft.it/dove_siamo.php","La properties non corrisponde!!");
            $this->assertEquals($props[3]["menu_description"],"Raggiungerci è molto semplice, prendete l'autobus AX8!!","La properties non corrisponde!!");

        }

            function testReadFromString2()
        {
            $myString = <<<END_OF_STRING

proprieta_01 = Home
altra_prop = http://www.mbcraft.it
menu_style = small_font

ancora_props = Ancora proprieta
; Questo è un commento
ultima_props = L'ultima prop

END_OF_STRING;

            $props = IniPropertiesUtils::readFromString($myString,false);

            $this->assertTrue(count($props)==5,"Il numero di properties non corrisponde!! : ".count($props));
            $this->assertEquals($props["menu_style"],"small_font","La properties non corrisponde!!");
            $this->assertEquals($props["ancora_props"],"Ancora proprieta","La properties non corrisponde!!");
            $this->assertEquals($props["ultima_props"],"L'ultima prop","La properties non corrisponde!! : ".$props["ultima_props"]);

        }
    
}

?>