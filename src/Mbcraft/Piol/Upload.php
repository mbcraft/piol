<?php

/**
 * This file contains the Upload class.
 */
namespace Mbcraft\Piol {
    
    /**
     * This class models a single file upload. It contains method for saving the uploaded file
     * to a target file or directory, and for obtaining information about the uploaded file as well
     * as the upload status.
     */
    class Upload extends PiolObject {
        /**
         * 
         * @var string Contains the upload file name 
         * 
         * @internal
         */
        private $my_name;
        /**
         * 
         * @var string Contains the mime type of this upload 
         * 
         * @internal
         */
        private $my_type;
        /**
         * 
         * @var type Contains the upload temporary file name
         * 
         * @internal
         */
        private $my_tmp_name;
        /**
         * 
         * @var int Contains the upload status 
         * 
         * @internal
         */
        private $my_error;
        /**
         * 
         * @var int Contains the size of the upload
         * 
         * @internal
         */
        private $my_size;
        
        /**
         * Build an Upload instance. You should not usually call this method, but use the UploadUtils fetch methods instead.
         * 
         * @param string $name The name of the uploaded file.
         * @param string $type The mime type of the uploaded file, as declared by the browser.
         * @param string $tmp_name The temporary save path of the uploaded file.
         * @param int $error The upload status code.
         * @param int $size The uploaded file size.
         * 
         * @internal
         */
        function __construct($name, $type, $tmp_name, $error, $size) {
            $this->my_name = $name;
            $this->my_type = $type;
            $this->my_tmp_name = $tmp_name;
            $this->my_error = $error;
            $this->my_size = $size;
        }
        
        /**
         * 
         * Returns the mime type of this uploaded file, as declared by the uploader.
         * 
         * @return string the mime type.
         * 
         * @api
         */
        public function getMimeType() {
            return $this->my_type;
        }
        
        /**
         * 
         * Returns the uploaded temporary path of this uploaded file.
         * 
         * @return string the path of the uploaded file as a string.
         * 
         * @internal
         */
        private function getTmpPath() {
            return $this->tmp_name;
        }

        /**
         * 
         * Returns the full file name of the uploaded file.
         * 
         * @return string the file name of this uploaded file as sended by the client.
         * 
         * @api
         */
        public function getFullName() {
            return $this->my_name;
        }
        
        /**
         * 
         * Returns the error code of this upload.
         * 
         * @return int the error code, as described in the php documentation.
         * 
         * @api
         */
        public function getErrorCode() {
            return $this->my_error;
        }

        /**
         * 
         * Checks if an upload is successfull.
         * 
         * @return boolean true if the upload is successful, false otherwise.
         * 
         * @api
         */
        public function isSuccessful() {
            return $this->my_error === UPLOAD_ERR_OK;
        }

        /**
         * 
         * Returns a string describing the upload error occurred.
         * 
         * @return string a description of the error occurred.
         * 
         * @api
         */
        public function getErrorMessageString() {
            $error_code = $this->my_error;

            switch ($error_code) {
                case UPLOAD_ERR_INI_SIZE:
                    return 'The file upload size exceeds the cunfigured php.ini max size.';
                case UPLOAD_ERR_FORM_SIZE:
                    return 'The file upload size exceeds the size configured in the html form (MAX_FILE_SIZE input field).';
                case UPLOAD_ERR_PARTIAL:
                    return 'The upload happened partially.';
                case UPLOAD_ERR_NO_FILE:
                    return 'No file uploaded.';
                case UPLOAD_ERR_NO_TMP_DIR:
                    return 'Temporary upload directory does not exists.';
                case UPLOAD_ERR_CANT_WRITE:
                    return 'Unable to write data in the upload folder. Check permissions or available disk space.';
                case UPLOAD_ERR_EXTENSION:
                    return 'A php extension blocked the uploaded file.';
                default:
                    return 'Upload error unknown.';
            }
        }
        
        /**
         * 
         * Returns the size of this uploaded file.
         * 
         * @return int the uploaded file size.
         * 
         * @api
         */
        public function getSize() {
            return $this->my_size;
        }
        
        /**
         * 
         * Saves the uploaded file in the path specified by the provided file.
         * The temporary upload file is deleted.
         * 
         * @param \Mbcraft\Piol\File|string $file The file instance or string path where to save the uploaded file.
         * @return boolean true if the operation was successfull, false otherwise. 
         * 
         * @api
         */
        public function moveAs($file) {
            
            if (file_exists($this->my_tmp_name)) {
            
                $final_file = File::asFile($file);

                $tmp_path = $this->getTmpPath();

                return move_uploaded_file($tmp_path, $final_file->getFullPath());
            
            } else
                return false;
            
        }

        /**
         * 
         * Saves the uploaded file into the specified folder optionally overriding the filename used during upload.
         * The temporary upload file is deleted.
         * 
         * @param \Mbcraft\Piol\Dir|string $dir The Dir instance or string path of the folder in which save the uploaded file.
         * @param string $new_name the optional name to use for the file (overrides upload filename).
         * @return \Mbcraft\Piol\File a File instance pointing to the saved file, or null if an error occurred.
         *
         * @throws \Mbcraft\Piol\IOException if something goes wrong (eg. the provided filename is not valid).
         * 
         * @api
         */
        public function moveTo($dir, $new_name = null) {
            $final_dir = Dir::asDir($dir);
            
            FileSystemUtils::checkValidFilename($new_name);

            if ($new_name == null)
                $real_filename = $this->getFullName();
            else
                $real_filename = $new_name;

            $final_file = $final_dir->newFile($real_filename);

            return $this->saveAs($final_file);
        }

        /**
         * 
         * 
         * Deletes the temporary file created after an upload.
         * 
         * @return true if the delete operation is successfull, false otherwise.
         * 
         * @api
         */
        public function deleteTmpFile() {
            if (file_exists($this->my_tmp_name)) {
                @unlink($this->getTmpPath());
            }
            
            return file_exists($this->my_tmp_name);
        }
        
        
    }
}

