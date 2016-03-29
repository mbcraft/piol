<?php
/**
 * This file contains the DataStorage class definition.
 */
namespace Mbcraft\Piol\StorageDrivers {

    use \Mbcraft\Piol\Storage;
    use \Mbcraft\Piol\StorageFactory;
        
    /**
     * This class contains operations available only for data storages : reading and writing
     * all the data from the storage.
     * 
     * To obtain a data storage use StorageFactory.
     */
    class DataStorage extends Storage {
        
        /**
         * 
         * Constructs an DataStorage.
         * 
         * @param string $folder the name of the folder in which this storage file is placed.
         * @param string $name the name of this storage.
         * 
         * @internal
         */
        function __construct($folder, $name) {
            parent::__construct($folder, $name, StorageFactory::DATA_STORAGE);
        }

        /**
         * 
         * Reads all the data from the storage, returning a string.
         * 
         * @return string the storage data as a string.
         * 
         * @api
         */
        public function readData() {
            $this->create();
            return $this->storage_file->getContent();
        }

        /**
         * 
         * Saves all the data inside the storage.
         * 
         * @param type $data the data to write into the storage, can be a string, an array or a stream resource.
         * @return int the number of bytes written into the file, or FALSE if an error occurred.
         * 
         * @api
         */
        public function saveData($data) {
            $this->create();
            return $this->storage_file->setContent($data);
        }

    }

}
 
?>