<?php

require_once("BlackHoleTest.php");
require_once("FilePropsTest.php");
require_once("FlatDirCacheTest.php");
require_once("FileReaderTest.php");
require_once("FileSystemUtilsTest.php");
require_once("FileWriterTest.php");
require_once("DirTest.php");
require_once("FileTest.php");
require_once("IniPropertiesUtilsTest.php");
require_once("JavaXmlPropertiesUtilsTest.php");
require_once("StorageTest.php");
require_once("UploadUtilsTest.php");
require_once("ZipUtilsTest.php");


class IOTestSuite extends PHPUnit_Framework_TestSuite {
    public function __construct() {
        //parent::__construct();

        $this->addTestSuite(new ReflectionClass("BlackHoleTest"));
        $this->addTestSuite(new ReflectionClass("FilePropsTest"));
        $this->addTestSuite(new ReflectionClass("FlatDirCacheTest"));
        $this->addTestSuite(new ReflectionClass("FileReaderTest"));
        $this->addTestSuite(new ReflectionClass("FileSystemUtilsTest"));
        $this->addTestSuite(new ReflectionClass("FileWriterTest"));
        $this->addTestSuite(new ReflectionClass("DirTest"));
        $this->addTestSuite(new ReflectionClass("FileTest"));
        $this->addTestSuite(new ReflectionClass("IniPropertiesUtilsTest"));
        $this->addTestSuite(new ReflectionClass("JavaXmlPropertiesUtilsTest"));
        $this->addTestSuite(new ReflectionClass("StorageTest"));
        $this->addTestSuite(new ReflectionClass("UploadUtilsTest"));
        $this->addTestSuite(new ReflectionClass("ZipUtilsTest"));        
    }
}
