<?php
/**
 * This file contains all the requires that loads all the library classes. You can require
 * this class and forget all other require and dependency stuff.
 * 
 */
namespace Mbcraft\Piol {
    
    require_once("PiolObject.php");
    require_once("FileSystemUtils.php");
    
    FileSystemUtils::checkPiolRootPath();

    require_once("IOException.php");
    require_once("__FileSystemElement.php");
    require_once("Utils/IniPropertiesUtils.php");
    require_once("Utils/JavaXmlPropertiesUtils.php");
    require_once("FileReader.php");
    require_once("FileWriter.php");
    require_once("File.php");
    require_once("Dir.php");
    require_once("Storage.php");
    require_once("StorageDrivers/DataStorage.php");
    require_once("StorageDrivers/PropertiesStorage.php");
    require_once("StorageDrivers/XMLStorage.php");
    require_once("Upload.php");
    require_once("UploadUtils.php");
    require_once("Cache/ICache.php");
    require_once("Cache/FlatDirCache.php");
    if (defined("ZipArchive"))
        require_once("ZipUtils.php");
    
}