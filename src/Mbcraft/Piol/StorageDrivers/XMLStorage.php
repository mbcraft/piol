<?php
/**
 * This file contains the XMLStorage class definition.
 */
namespace Mbcraft\Piol\StorageDrivers {
    
    use \Mbcraft\Piol\Storage;
    use \Mbcraft\Piol\StorageFactory;
    /**
     * This class contains operations available only for XML storages : reading and writing the
     * whole xml content. It uses the SimpleXMLElement class for its operations.
     * 
     * To obtain a data storage use StorageFactory.
     */
    class XMLStorage extends Storage {

        /**
         * 
         * Constructs an XMLStorage.
         * 
         * @param string $folder the name of the folder in which this storage file is placed.
         * @param string $name the name of this storage.
         * 
         * @internal
         */
        function __construct($folder, $name) {
            parent::__construct($folder, $name, StorageFactory::XML_STORAGE);
        }

        /**
         * 
         * Reads all the xml data inside the storage, returning a SimpleXMLElement of it.
         * 
         * @return \SimpleXMLElement the data of this storage as a SimpleXMLElement.
         * 
         * @api
         */
        public function readXML() {
            $this->create();
            $this->checkStorageFile();
            return new \SimpleXMLElement($this->storage_file->getContent());
        }

        /**
         * 
         * Writes the xml data inside the storage.
         * 
         * @param \SimpleXMLElement $xml_data the document root of the xml data to write into the storage.
         * 
         * @api
         */
        public function saveXML($xml_data) {
            $this->create();
            $this->checkStorageFile();
            $this->storage_file->setContent($xml_data->asXML());
        }

    }

}

