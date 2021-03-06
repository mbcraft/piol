<?php
/**
 * This file contains the FileReader class.
 */
namespace Mbcraft\Piol {

    /**
     * This class enables you to easily read data from files, providing methods
     * for reading characters, plain string lines or CSV data, and a scanf like method.
     * It also contains method for moving inside the file.
     * 
     * To obtain a FileReader for a given file use the File::openReader() instance method.
     */
    class FileReader extends PiolObject implements IReader {

        /**
         * 
         * @var string Contains the unget character. Used for correct line ending parsing. 
         * 
         * @internal
         */
        private $ch = null;
        /**
         * 
         * @var mixed The file handle for this FileReader, as returned from the fopen call. 
         * 
         * @internal
         */
        protected $my_handle;
        /**
         * 
         * @var boolean Contains the 'opened' status of this readed. 
         * 
         * @internal
         */
        protected $open;

        /**
         *
         * @var int The current length of this file.
         */
        protected $current_size;
        /**
         * Constructs a file reader from a given file handle. You should usually not need
         * to call this constructor since instances of FileReader are obtained throught File::openReader()
         * method.
         * 
         * @param mixed $handle The handle to use for reading data from this file.
         *
         * @throws IOException if the handle is null
         * 
         * @internal
         */
        function __construct($handle) {
            if ($handle==null)
                throw new IOException("The handle is null");
            $this->my_handle = $handle;
            $this->current_size = fstat($handle)["size"];
            $this->open = true;
        }
        
        /**
         * Returns the number of bytes available for reading from the current
         * position.
         */
        function available() {
            return $this->current_size - $this->pos();
        }

        /**
         * 
         * Checks if this file reader is closed. If so, throws an IOException.
         * 
         * @throws \Mbcraft\Piol\IOException if this reader is closed.
         * 
         * @internal
         */
        protected function checkNotClosed() {
            if (!$this->open)
                throw new IOException("Lo stream risulta essere chiuso!!");
        }

        /**
         * 
         * Checks if this reader is open.
         * 
         * @return boolean true if this file reader is open, false otherwise.
         * 
         * @api
         */
        public function isOpen() {
            return $this->open;
        }

        /**
         * 
         * Reads data from this reader following the 'scanf' parameter convention.
         * 
         * @param string $format The format string of the data to read
         * @return array An array of ordered values readed following the format provided.
         * 
         * @api
         */
        public function scanf($format) {
            $this->checkNotClosed();

            return fscanf($this->my_handle, $format);
        }

        /**
         * 
         * Reads n bytes from this stream.
         * 
         * @param int $length the number of bytes to read.
         * @return string|FALSE the readed string or FALSE on failure.
         * 
         * @api
         */
        public function read($length) {
            $this->checkNotClosed();

            return fread($this->my_handle, $length);
        }

        /**
         * 
         * Reads a line from this reader, ended by a CR or CRLF.
         * 
         * @return string the readed line.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         * 
         * @api
         */
        public function readLine() {
            $this->checkNotClosed();

            $line = "";

            do {

                $c = $this->nextChar();

                if ($c !== null && ord($c) !== 13 && ord($c) !== 10) {
                    $line.=$c;
                } else {
                    if ($c == null)
                        return strlen($line) === 0 ? null : $line;

                    if (ord($c) === 13) {
                        $nc = $this->nextChar();
                        if ($nc !== null && ord($nc) !== 10)
                            $this->ungetChar($nc);
                    }

                    return $line;
                }

            } while (true);

            throw new IOException("Should never happen ...");
        }

        /**
         * 
         * Pushes a character back.
         * 
         * @param string $ch The caracter to push back.
         * @throws \Mbcraft\Piol\IOException If a character is already in the push back variable.
         * 
         * @internal
         */
        private function ungetChar($ch) {
            if ($this->ch !== null || $ch === null)
                throw new IOException("Can't unget more than one character.");
            $this->ch = $ch;
        }

        /**
         * 
         * Returns the next character readed from the current position. If a character
         * is set in the push back cache, return that character instead.
         * 
         * @return string The next readed character.
         * 
         * @internal
         */
        private function nextChar() {
            if ($this->ch !== null) {
                $r = $this->ch;
                $this->ch = null;
                return $r;
            }
            if (feof($this->my_handle))
                return null;
            return fgetc($this->my_handle);
        }

        /**
         * 
         * Reads one character from this file reader.
         * 
         * @return string a string of one character readed from the stream.
         * @throws \Mbcraft\Piol\IOException If  the parameter is not a string of one character.
         * 
         * @api
         */
        public function readChar() {
            $this->checkNotClosed();

            return $this->nextChar();
        }

        /**
         * 
         * Moves the read pointer at the initial position of the file, as calling seek(0).
         * 
         * @return boolean true if the operation succeded, false otherwise.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed. 
         * 
         * @api
         */
        public function reset() {
            $this->checkNotClosed();

            return rewind($this->my_handle);
        }

        /**
         * 
         * Moves the read pointer at the specified location.
         * 
         * @param int $location The location at which point the reader.
         * @return boolean true if the operation succeded, false otherwise.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed. 
         * 
         * @api
         */
        public function seek($location) {
            $this->checkNotClosed();

            return fseek($this->my_handle, $location, SEEK_SET)==0;
        }

        /**
         * 
         * Skip bytes from the current position of the stream.
         * 
         * @param int $offset The number of bytes to skip.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         * 
         * @api
         */
        public function skip($offset) {
            $this->checkNotClosed();

            fseek($this->my_handle, $offset, SEEK_CUR);
        }

        /**
         * 
         * Returns the current position inside the opened file.
         * 
         * @return int the byte index of the reader inside the file.
         * @throws IOException if this reader is already closed.
         * 
         * @api
         */
        public function pos() {
            $this->checkNotClosed();

            return ftell($this->my_handle);
        }

        /**
         * 
         * Checks if the stream is ended. This is not done only using feof PHP function
         * since you need to read an empty record before feof returns true.
         * 
         * @return true if the end of the stream is reached, false otherwise.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         * 
         * @api
         */
        public function isEndOfStream() {
            $this->checkNotClosed();

            return ($this->available()==0 || feof($this->my_handle));
        }

        /**
         * 
         * Closes this file reader, releasing the locks acquired.
         * 
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         * 
         * @api
         */
        public function close() {
            if ($this->open) {
                fflush($this->my_handle);
                flock($this->my_handle, LOCK_UN);
                fclose($this->my_handle);

                $this->open = false;
                $this->my_handle = null;
            } else
                throw new IOException("Reader/Writer already closed.");
        }

    }

}

