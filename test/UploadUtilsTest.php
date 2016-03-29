<?php

use Mbcraft\Piol\UploadUtils;
use Mbcraft\Piol\Upload;

class UploadUtilsTest extends PHPUnit_Framework_TestCase {
    
    function testFetchUploadData() {
        $sample_FILES_data = array ( 'download' => array ( 'name' => array ( 'ciccio' => 'BitRock InstallBuilder Professional.desktop', 'pluto' => 'Screenshot from 2015-05-11 08:27:34.png', ), 'type' => array ( 'ciccio' => 'application/x-desktop', 'pluto' => 'image/png', ), 'tmp_name' => array ( 'ciccio' => '/tmp/phpXELWbK', 'pluto' => '/tmp/phpa6ixSd', ), 'error' => array ( 'ciccio' => 0, 'pluto' => 0, ), 'size' => array ( 'ciccio' => 234, 'pluto' => 295694, ), ), 'other' => array ( 'name' => 'Screenshot from 2015-05-12 10:18:13.png', 'type' => 'image/png', 'tmp_name' => '/tmp/phplezozH', 'error' => 0, 'size' => 744768, ), ) ;

        $this->assertTrue(UploadUtils::hasUploadedFiles($sample_FILES_data),"Non sono stati trovati dati per l'upload!");
        $fetched_data = UploadUtils::getUploadedFiles($sample_FILES_data);
        
        $this->assertTrue($fetched_data["other"] instanceof Upload,"Il campo other non contiene un'istanza di Upload!!");
        $this->assertTrue($fetched_data["download"]["ciccio"] instanceof Upload,"Il campo other non contiene un'istanza di Upload!!");
        $this->assertTrue($fetched_data["download"]["pluto"] instanceof Upload,"Il campo other non contiene un'istanza di Upload!!");    
    }
    
}
