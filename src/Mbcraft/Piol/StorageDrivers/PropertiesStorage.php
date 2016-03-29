<?php
/**
 * This file contains the PropertiesStorage class definition.
 */
namespace Mbcraft\Piol\StorageDrivers {

    use \Mbcraft\Piol\Storage;
    use \Mbcraft\Piol\StorageFactory;
    use \Mbcraft\Piol\Utils\IniPropertiesUtils;
    /**
     * This class contains methods available only for properties storage.
     * It provides methods for reading and writing all properties at once, as well as
     * add and remove a list of entries.
     * 
     * To obtain a data storage use StorageFactory.
     */
    class PropertiesStorage extends Storage {
        /**
         * 
         * Constructs a PropertiesStorage.
         * 
         * @param string $folder the name of the folder in which this storage file is placed.
         * @param string $name the name of this storage.
         * 
         * @internal
         */
        function __construct($folder, $name) {
            parent::__construct($folder, $name, StorageFactory::PROPERTIES_STORAGE);
        }

        /**
         * 
         * Reads all the properties from this storage. By default, the properties storage
         * keeps sections data. If the storage file does not exist
         * it is created.
         * 
         * @return array an array of all properties.
         * 
         * @api
         */
        public function readAll() {
            $this->create();
            return IniPropertiesUtils::readFromFile($this->storage_file, true);
        }

        /**
         * 
         * Saves all properties data to the properties storage. By default the properties storage
         * keeps sections data. If the storage file does not exist
         * it is created.
         * 
         * @param array $props the properties data to save.
         * 
         * @api
         */
        public function saveAll($props) {
            $this->create();
            IniPropertiesUtils::saveToFile($this->storage_file, $props, true);
        }

        /**
         * 
         * Adds the entries to the properties storage file. If the storage file does not exist
         * it is created.
         * 
         * @param array $entries the entries to add or overwrite.
         * 
         * @api
         */
        public function add($entries) {
            $this->create();
            IniPropertiesUtils::addEntriesToFile($this->storage_file, true, $entries);
        }

        /**
         * 
         * Removed all the listed entries from the properties storage file. If the file does not
         * exist it is created. If a key is not found nothing is removed.
         * 
         * @param array $entries the entries list to remove from the storage file.
         * 
         * @api
         */
        public function remove($entries) {
            $this->create();
            IniPropertiesUtils::removeEntriesFromFile($this->storage_file, true, $entries);
        }

    }

}

?>