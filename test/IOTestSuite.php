<?php

class IOTestSuite extends PHPUnit_Framework_TestSuite {
    public function __construct() {
        parent::__construct();

        $this->addTestSuite(new ReflectionClass("StringBufferTest"));
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
