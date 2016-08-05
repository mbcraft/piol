<?php

namespace Mbcraft\Piol {

    /**
     * Interface IReader
     *
     * This interface is used to represent a character reader.
     *
     * @package Mbcraft\Piol
     */
    interface IReader {

        /**
         * Returns the number of bytes available for reading from the current
         * position.
         */
        function available();

        /**
         * Checks if this reader is open.
         *
         * @return boolean true if this reader is open, false otherwise.
         *
         * @api
         */
        function isOpen();

        /**
         * Reads data from this reader following the 'scanf' parameter convention.
         *
         * @param string $format The format string of the data to read
         * @return array An array of ordered values readed following the format provided.
         *
         * @api
         */
        function scanf($format);

        /**
         * Reads n bytes from this stream.
         *
         * @param int $length the number of bytes to read.
         * @return string|FALSE the readed string or FALSE on failure.
         *
         * @api
         */
        function read($length);

        /**
         * Reads a line from this reader, ended by a CR or CRLF.
         *
         * @return string the readed line.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         *
         * @api
         */
        function readLine();

        /**
         * Reads one character from this reader.
         *
         * @return string a string of one character readed from the stream.
         * @throws \Mbcraft\Piol\IOException If  the parameter is not a string of one character.
         *
         * @api
         */
        function readChar();

        /**
         * Moves the read pointer at the initial position of the file, as calling seek(0).
         *
         * @return boolean true if the operation succeded, false otherwise.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         *
         * @api
         */
        function reset();

        /**
         * Moves the read pointer at the specified location.
         *
         * @param int $location The location at which point the reader.
         * @return boolean true if the operation succeded, false otherwise.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         *
         * @api
         */
        function seek($location);

        /**
         * Skip bytes from the current position of the stream.
         *
         * @param int $offset The number of bytes to skip.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         *
         * @api
         */
        function skip($offset);

        /**
         * Returns the current position inside the opened stream.
         *
         * @return int the byte index of the reader inside the stream.
         * @throws IOException if this reader is already closed.
         *
         * @api
         */
        function pos();

        /**
         * Checks if the stream is ended.
         *
         * @return true if the end of the stream is reached, false otherwise.
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         *
         * @api
         */
        function isEndOfStream();

        /**
         * Closes this reader, releasing the locks acquired.
         *
         * @throws \Mbcraft\Piol\IOException if this reader is already closed.
         *
         * @api
         */
        function close();
    }

}