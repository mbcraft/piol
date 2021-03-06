<?php
/**
 * This file contains the ZipUtils class.
 */
namespace Mbcraft\Piol {

    use ZipArchive;

    /**
     * This class provides utilities methods for extracting and compressing files
     * using the php 'zip' extension. It has only static methods.
     * Actually it provides the two following operations :
     * - extract an archive to a target folder
     * - create an archive from a target folder, optionally putting the archived data
     * in a custom folder.
     */
    class ZipUtils extends PiolObject {

        /**
         * 
         * Extracts the archive in the specified folder. 
         * 
         * @param \Mbcraft\Piol\File|string $zip_file File or string path of the zip file.
         * @param \Mbcraft\Piol\Dir|string $target_folder Dir or string path of the folder to use for file extraction.
         * 
         * @api 
         */
        public static function expandArchive($zip_file, $target_folder) {
            $zip_archive = new \ZipArchive();

            $real_zip_file = File::asFile($zip_file);

            $target_dir = Dir::asDir($target_folder);

            $zip_archive->open($real_zip_file->getFullPath());

            $zip_archive->extractTo($target_dir->getFullPath());

            $zip_archive->close();
        }

        /**
         * 
         * Creates a zip archive with the content of the specified folder and saves it to the specified file name.
         * 
         * @param \Mbcraft\Piol\File|string $save_file The \Piol\File instance or the string to use as save path for the archive.
         * @param \Mbcraft\Piol\Dir|string $folder_to_zip The \Piol\Dir or the string path pointing to a folder with the elements to archive.
         * @param string $local_dir The directory to use to contain the files inside the archive, defaults to archive root ('/').
         *
         * @throws IOException if an input/output error occurs.
         * 
         * @api
         */
        public static function createArchive($save_file, $folder_to_zip, $local_dir = "/") {

            $real_save_file = File::asFile($save_file);
            
            $dir_to_zip = Dir::asDir($folder_to_zip);

            $zip_archive = new \ZipArchive();

            $zip_archive->open($real_save_file->getFullPath(), \ZipArchive::CREATE);

            ZipUtils::recursiveZipFolder($zip_archive, $dir_to_zip, $local_dir);

            $zip_archive->close();
        }

        /**
         * 
         * Recursively inflate the folder into che provided archive, using local_dir as a prefix for archived files.
         * 
         * @param ZipArchive $zip_archive the current archive
         * @param Dir $current_folder the current folder to compress
         * @param string $local_dir the local dir prefix to use for compressed files.
         * 
         * @internal
         */
        private static function recursiveZipFolder($zip_archive, $current_folder, $local_dir) {
            $results = $current_folder->listElements();
            $all_elements = array_merge($results[0],$results[1]);
            
            foreach ($all_elements as $dir_entry) {
                if ($dir_entry->isFile()) {
                    $zip_archive->addFile($dir_entry->getFullPath(), $local_dir . $dir_entry->getFullName());
                } else {
                    $zip_archive->addEmptyDir($local_dir . $dir_entry->getName() . DS);
                    ZipUtils::recursiveZipFolder($zip_archive, $dir_entry, $local_dir . $dir_entry->getName() . DS);
                }
            }
        }

    }

}
