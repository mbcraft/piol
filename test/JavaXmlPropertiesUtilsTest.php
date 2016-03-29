<?php

use Mbcraft\Piol\File;
use Mbcraft\Piol\Utils\JavaXmlPropertiesUtils;

class JavaXmlPropertiesUtilsTest extends PHPUnit_Framework_TestCase {

    function testReadPlainFromFile() {
        //si presume che vada ma non si sa mai ...

        $props_file = new File("/test/java_xml_properties_dir/props_plain.xml");

        $props = JavaXmlPropertiesUtils::readFromFile($props_file);

        $this->assertEquals($props["prop_01"], "ciao");
        $this->assertEquals($props["prop_02"], "mondo");
    }

    function testSaveToFilePlain() {
        $values = array(3 => 12, "ciao" => "prova", "ok" => 3, "world" => 2, "ancora" => "funziona", "strano" => "Questa frase è strana, contiene gli ' e le ()!=£$%&/ !!!");

        $store = new File("/test/java_xml_properties_dir/storage/save_sections_plain.xml");

        JavaXmlPropertiesUtils::saveToFile($store, $values);

        $read_props = JavaXmlPropertiesUtils::readFromFile($store);

        $this->assertEquals($read_props[3], 12);
        $this->assertEquals($read_props["ciao"], "prova");
        $this->assertEquals($read_props["ok"], 3);
        $this->assertEquals($read_props["world"], 2);
        $this->assertEquals($read_props["ancora"], "funziona");
        $this->assertEquals($read_props["strano"], "Questa frase è strana, contiene gli ' e le ()!=£$%&/ !!!");
    }

    function testAddEntriesToFile() {
        $file = new File("/test/java_xml_properties_dir/test_folder_2/add_props.xml");
        if ($file->exists())
            $file->delete();

        JavaXmlPropertiesUtils::addEntriesToFile($file, array("ciao" => 1, "mondo" => 2, 3 => "pluto"));

        $this->assertTrue($file->exists(), "Il file delle properties non è stato creato!!");
        $props = JavaXmlPropertiesUtils::readFromFile($file);

        $this->assertTrue(count($props) == 3, "Il numero delle properties non corrisponde!");

        $this->assertEquals($props["ciao"], 1);
        $this->assertEquals($props["mondo"], 2);
        $this->assertEquals($props[3], "pluto");

        $file->delete();
    }

    function testRemoveEntriesFromFile() {
        $file = new File("/test/java_xml_properties_dir/test_folder_2/remove_props.xml");

        $properties = array("one" => 1, "two" => 2, "mondo" => "mah");

        JavaXmlPropertiesUtils::saveToFile($file, $properties);
        JavaXmlPropertiesUtils::removeEntriesFromFile($file, array("two"));

        $removed = JavaXmlPropertiesUtils::readFromFile($file);

        $this->assertTrue(count($removed) == 2, "Il numero delle proprietà non corrisponde!!");
        $this->assertFalse(isset($removed["two"]), "two è stata cancellata!!");

        $this->assertTrue(isset($removed["one"]), "'one' è stata cancellata!!");

        $this->assertEquals($removed["one"], 1, "Il valore della properties non corrisponde!");
        $this->assertEquals($removed["mondo"], "mah", "Il valore della properties non corrisponde!");
    }

    function testReadFromString2() {
        $myString = <<<END_OF_STRING
<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE properties
  SYSTEM "http://java.sun.com/dtd/properties.dtd">
<properties>
    <entry key="proprieta_01">Home</entry>
    <entry key="altra_prop">http://www.mbcraft.it</entry>
    <entry key="menu_style">small_font</entry>
    <entry key="ancora_props">Ancora proprieta</entry>
    <entry key="ultima_props">L'ultima prop</entry>
</properties>
END_OF_STRING;

        $props = JavaXmlPropertiesUtils::readFromString($myString);

        $this->assertTrue(count($props) == 5, "Il numero di properties non corrisponde!! : " . count($props));
        $this->assertEquals($props["menu_style"], "small_font", "La properties non corrisponde!!");
        $this->assertEquals($props["ancora_props"], "Ancora proprieta", "La properties non corrisponde!!");
        $this->assertEquals($props["ultima_props"], "L'ultima prop", "La properties non corrisponde!! : " . $props["ultima_props"]);
    }

}

?>