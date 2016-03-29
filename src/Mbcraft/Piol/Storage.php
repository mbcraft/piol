<?php

/**
 * This file contains the Storage class.
 */
namespace Mbcraft\Piol {

    /**
     * This abstract class models a protected storage inside a storage folder.
     * The following operations are common to all storage types. They actually cover the following
     * operations : check if the storage exists, create an empty storage, delete the storage, get the storage type,
     * get the storage folder, get the storage name.
     */
    abstract class Storage extends PiolObject {

        /**
         * 
         * @var string The folder containing this storage
         * 
         * @internal
         */
        private $folder;
        /**
         * 
         * @var string The full filename of this storage file 
         * 
         * @internal
         */
        private $name;
        /**
         * 
         * @var \Mbcraft\Piol\Dir the Dir instance of the storage folder. 
         * 
         * @internal
         */
        protected $storage_dir;
        /**
         * 
         * @var \Mbcraft\Piol\File the File instance of the storage file.
         * 
         * @internal
         */
        protected $storage_file;

        /**
         * 
         * Construct a Storage instance placed inside the specified folder with the specified name,
         * of the specified type.
         * 
         * @param string $folder the folder name, as a string.
         * @param string $name the storage file name, as a string.
         * @param string $storage_type the storage file, actually the file extension.
         * @throws \Mbcraft\Piol\IOException if the parameters does not point to a valid storage location.
         * 
         * @internal
         */
        protected function __construct($folder, $name, $storage_type) {
            /*
              if (!preg_match(self::VALID_FOLDER_AND_STORAGE_NAME_PATTERN,$folder))
              throw new IOException("Nome del folder non valido!");

              if (!preg_match(self::VALID_FOLDER_AND_STORAGE_NAME_PATTERN,$name))
              throw new IOException("Nome del file non valido!");
             */

            $this->folder = $folder;
            $this->name = $name;

            $storage_dir = StorageFactory::getProtectedStorage();

            $this->storage_type = $storage_type;
            $p = $storage_dir->getPath() . $folder . DS;
            
            $this->storage_dir = new Dir($p);

            if (!FileSystemUtils::isValidFilename($name .".".$storage_type))
                throw new IOException("The specified string is not a valid filename.");
            
            $p = $storage_dir->getPath() . $folder . DS . $name. DOT . $storage_type;
            $this->storage_file = new File($p);
        }



        /**
         * 
         * Returns the storage type of this storage.
         * 
         * @return string the storage type.
         * 
         * @api
         */
        public function getStorageType() {
            return $this->storage_type;
        }

        /**
         * 
         * Returns the folder name of this storage.
         * 
         * @return string the folder name in which this storage file is placed.
         * 
         * @api
         */
        public function getFolder() {
            return $this->folder;
        }

        /**
         * 
         * The name of this storage, actually the file name without extension.
         * 
         * @return string the name of this storage
         * 
         * @api
         */
        public function getName() {
            return $this->name;
        }

        /**
         * 
         * Returns true if this Storage file exists, false otherwise.
         * 
         * @return boolean true if this Storage exists, false otherwise.
         * 
         * @api
         */
        public function exists() {
            return $this->storage_file->exists();
        }

        /**
         * 
         * Creates this storage and its folder if not already existing.
         * 
         * @api
         */
        public function create() {
            $this->storage_dir->touch();
            $this->storage_file->touch();
        }

        /**
         * 
         * Deletes this storage file.
         * 
         * @api
         */
        public function delete() {
            $this->storage_file->delete();
        }

    }

}
?>