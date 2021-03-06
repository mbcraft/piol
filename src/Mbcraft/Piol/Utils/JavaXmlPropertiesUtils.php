<?php
/**
 * This file contains the JavaXmlPropertiesUtils class.
 */

namespace Mbcraft\Piol\Utils;

use Mbcraft\Piol\PiolObject;
use Mbcraft\Piol\File;

/**
 * This class has static methods for handling java xml properties.
 */
class JavaXmlPropertiesUtils extends PiolObject {

    const XML_PRELUDE = <<<'END_XML_PRELUDE'
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE properties
  SYSTEM "http://java.sun.com/dtd/properties.dtd">
END_XML_PRELUDE;
    
    const XML_ROOT_OPEN = <<<'TAG'
<properties>
TAG;

    const XML_ROOT_CLOSE = <<<'TAG'
</properties>
TAG;


    /**
     * Adds entries to a java xml properties file.
     * 
     * @param mixed $file a path or a File
     * @param array $entries
     * 
     * @api
     */
    public static function addEntriesToFile($file, $entries) {
        $properties = self::readFromFile($file);
        foreach ($entries as $key => $value) {
            $properties[$key] = $value;
        }

        self::saveToFile($file, $properties);
    }

    /**
     * Removes entries to a java xml properties file.
     * 
     * @param mixed $file a path or a File
     * @param array $entries The entries to remove.
     * 
     * @api
     */
    public static function removeEntriesFromFile($file, $entries) {
        $properties = self::readFromFile($file);
        if (is_string($entries)) {
            unset($properties[$entries]);
        } else {
            foreach ($entries as $i => $key) {
                unset($properties[$key]);
            }
        }
        self::saveToFile($file, $properties);
    }

    /**
     * Reads the properties from a string.
     * 
     * @param string $string The string, in xml java properties format.
     * 
     * @return array The properties array
     * 
     * @api
     */
    public static function readFromString($string) {

        $xml = new \DOMDocument();
        $xml->loadXML($string);

        $result = [];

        $props = $xml->childNodes->item($xml->childNodes->length-1)->childNodes;
        
        for ($i=0; $i<$props->length;$i++) {
            $entry = $props->item($i);
            if ($entry->nodeName=="entry")
                $result[$entry->attributes->getNamedItem("key")->nodeValue] = $entry->textContent;
        }
        
        return $result;
    }

    /**
     * Reads the properties from a file, in xml java properties format.
     * 
     * @param mixed $file a path or a File
     * 
     * @return array The properties array
     * 
     * @api
     */
    public static function readFromFile($file) {
        $real_file = File::asFile($file);

        if ($real_file->exists())
            return self::readFromString($file->getContent());
        else
            return array();
    }

    /**
     * Saves the properties to a string.
     * 
     * @param array $properties The properties, as an array.
     * 
     * @return string the string containing the properties in xml java properties format.
     * 
     * @api
     */
    public static function saveToString($properties) {
        $xn = new \SimpleXMLElement(self::XML_ROOT_OPEN.self::XML_ROOT_CLOSE,LIBXML_NOXMLDECL);
            
        foreach ($properties as $key => $value) {
            $xn->addChild("entry", htmlspecialchars($value,ENT_XML1))->addAttribute("key",htmlspecialchars($key,ENT_XML1));
        }
        //LIBXML_NOXMLDECL is not supported, so replace the xml declaration to include also the dtd
        return preg_replace('/\<\?.*\?\>/', self::XML_PRELUDE, $xn->asXML());
        
    }

    /**
     * Saves the properties to file.
     * 
     * @param mixed  $file a path or a File
     * @param array $properties The properties, as an array.
     * 
     * @api
     */
    public static function saveToFile($file, $properties) {
        $prop_string = self::saveToString($properties);

        $real_file = File::asFile($file);

        if (!$real_file->exists()) {
            $real_file->touch();
        }
        $real_file->setContent($prop_string);
    }

}
