<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 06/08/16
 * Time: 15.23
 *
 * This file contains the StringBuffer class.
 */

namespace Mbcraft\Piol;

/**
 * Class StringBuffer
 * @package Mbcraft\Piol
 *
 * This class models a string buffer, and implements the IReader and IWriter interfaces for reading
 * and writing on it.
 */
class StringBuffer implements IWriter
{

    private $pos = 0;
    private $closed = false;
    private $data;

    /**
     * Constructs a new StringBuffer.
     *
     * StringBuffer constructor.
     * @param string $data The data that is contained in the buffer. If no parameter is passed the buffer is empty by default.
     */
    function __construct($data="") {
        $this->data = "".$data;
    }

    /**
     * Returns the number of bytes available for reading from the current
     * position.
     *
     * @api
     */
    function available()
    {
        return strlen($this->data)-$this->pos;
    }

    /**
     * Checks if this reader is open.
     *
     * @return boolean true if this reader is open, false otherwise.
     *
     * @api
     */
    function isOpen()
    {
        return !$this->closed;
    }

    /**
     * Reads data from this reader following the 'scanf' parameter convention.
     *
     * @param string $format The format string of the data to read
     * @return array An array of ordered values readed following the format provided.
     *
     * @api
     */
    function scanf($format)
    {
        $spec_pattern = "/%[+-]?('.)?[-]?(\d)*(\..\d)?[%bcdeEfFgGosuxX]/";

        $source = substr($this->data,$this->pos);

        $result = sscanf($source,$format);

        if ($result==-1) return;
        else {
            $l = 0;
            foreach ($result as $v) {
                $l += strlen("".$v);
            }
        }

        $no_patterns_format = preg_replace($spec_pattern,"",$format);
        //echo "No patterns format : [".$no_patterns_format."] LEN=".strlen($no_patterns_format);
        $l+=strlen($no_patterns_format);

        $this->pos+=$l;

        return $result;
    }

    /**
     * Reads n bytes from this stream.
     *
     * @param int $length the number of bytes to read.
     * @return string|FALSE the readed string or FALSE on failure.
     *
     * @api
     */
    function read($length)
    {
        $l = $this->pos+$length < strlen($this->data) ? $length : strlen($this->data)-$this->pos;
        $result = substr($this->data,$this->pos,$l);
        $this->pos+=$l;
        return $result;
    }

    /**
     * Checks if the data at the index is on an EOL marker.
     *
     * @param $i The index on which to check
     * @return bool true if the index points to an EOL marker, false otherwise
     *
     * @internal
     */
    private function isEndOfLine($i) {
        $ch = $this->data[$i];

        if ($this->getLineEndingModeCrlf()) {
            if ($ch=="\r") {
                $more_ch = $i + 1 < strlen($this->data);

                if ($more_ch) {
                    $next_n = $this->data[$i + 1] == "\n";

                    if ($next_n) return true;
                }
            }
        } else {
            if ($ch=="\n") return true;
        }
        return false;
    }

    /**
     * Reads a line from this reader, ended by a CR or CRLF.
     *
     * @return string the readed line.
     * @throws \Mbcraft\Piol\IOException if this reader is already closed.
     *
     * @api
     */
    function readLine()
    {
        $i = $this->pos;
        $tot_len = strlen($this->data);
        while ($i<$tot_len && !$this->isEndOfLine($i)) {
            $i++;
        }

        $result = substr($this->data,$this->pos,$i-$this->pos);
        $i++;   //skip first EOL char
        if ($this->getLineEndingModeCrlf()) $i++;  //skip second EOL char if needed
        $this->pos=$i;  //update position
        return $result;
    }

    /**
     * Reads one character from this reader.
     *
     * @return string a string of one character readed from the stream.
     * @throws \Mbcraft\Piol\IOException If  the parameter is not a string of one character.
     *
     * @api
     */
    function readChar()
    {
        return $this->data[$this->pos++];
    }

    /**
     * Moves the read pointer at the initial position of the file, as calling seek(0).
     *
     * @return boolean true if the operation succeded, false otherwise.
     * @throws \Mbcraft\Piol\IOException if this reader is already closed.
     *
     * @api
     */
    function reset()
    {
        $this->pos = 0;
    }

    /**
     * Moves the read pointer at the specified location.
     *
     * @param int $location The location at which point the reader.
     * @return boolean true if the operation succeeded, false otherwise.
     * @throws \Mbcraft\Piol\IOException if this reader is already closed.
     *
     * @api
     */
    function seek($location)
    {
        $this->pos = $location;
    }

    /**
     * Skip bytes from the current position of the stream.
     *
     * @param int $offset The number of bytes to skip.
     * @throws \Mbcraft\Piol\IOException if this reader is already closed.
     *
     * @api
     */
    function skip($offset)
    {
        $this->pos += $offset;
    }

    /**
     * Returns the current position inside the opened stream.
     *
     * @return int the byte index of the reader inside the stream.
     * @throws IOException if this reader is already closed.
     *
     * @api
     */
    function pos()
    {
        return $this->pos;
    }

    /**
     * Checks if the stream is ended.
     *
     * @return true if the end of the stream is reached, false otherwise.
     * @throws \Mbcraft\Piol\IOException if this reader is already closed.
     *
     * @api
     */
    function isEndOfStream()
    {
        return strlen($this->data)==$this->pos;
    }

    /**
     * Closes this reader, releasing the locks acquired.
     *
     * @throws \Mbcraft\Piol\IOException if this reader is already closed.
     *
     * @api
     */
    function close()
    {
        $this->closed = true;
    }

    /**
     * Returns the actual length of the buffer.
     *
     * @return int The total length of the buffer.
     *
     * @api
     */
    function length() {
        return strlen($this->data);
    }


    protected static $defaultLineEndingModeCrlf = true;
    protected $lineEndingModeCrlf = true;
    /**
     *
     * Sets the default line ending mode for all writers.
     *
     * @param boolean $default_line_ending_crlf true if CRLF should be used, false for CR.
     *
     * @api
     */
    static function setDefaultLineEndingModeCrlf($default_line_ending_crlf)
    {
        self::$defaultLineEndingModeCrlf = $default_line_ending_crlf;
    }

    /**
     *
     * Gets the default line ending mode for all writers.
     *
     * @return boolean true if CRLF should be used, false for CR.
     *
     * @api
     */
    static function getDefaultLineEndingModeCrlf()
    {
        return self::$defaultLineEndingModeCrlf;
    }

    /**
     *
     * Sets the line ending mode for this writer.
     *
     * @param boolean $line_ending_mode true for CRLF, false for CR.
     *
     * @api
     */
    function setLineEndingModeCrlf($line_ending_mode)
    {
        $this->lineEndingModeCrlf = $line_ending_mode;
    }

    /**
     *
     * Gets the line ending mode for this writer. If not set uses the default.
     *
     * @return boolean returns true if CRLF should be used, false for CR.
     *
     * @api
     */
    function getLineEndingModeCrlf()
    {
        return $this->lineEndingModeCrlf;
    }

    /**
     *
     * Writes values to this writer following the printf rules. All additional parameters
     * after 'format' are treated as data.
     *
     * @param mixed $format The format to use, following the printf rules.
     *
     * @return int the actual fprintf result.
     *
     * @throws \Mbcraft\Piol\IOException if this writer is already closed.
     *
     * @api
     */
    function printf($format)
    {
        $this->checkNotClosed();

        $args = func_get_args();
        $printf_args = array_slice($args, 1);
        $p = 'return sprintf($format';
        $i = 0;
        foreach ($printf_args as $arg) {

            $p.=',$printf_args[' . $i . ']';
            $i++;
        }
        $p.=");";
        $d = eval($p);
        $result = $this->write($d);
        return $result;
    }

    /**
     *
     * Writes a string inside this stream at the current position. No characters are added.
     *
     * @param string $string The string to write.
     * @return int the number of bytes written, or a negative value if an error occurred.
     *
     * @throws \Mbcraft\Piol\IOException if this writer is already closed.
     *
     * @api
     */
    function write($string)
    {
        $this->checkNotClosed();

        $l = strlen($string);

        $before_data = substr($this->data,0,$this->pos);
        $after_data = substr($this->data,$this->pos+$l);

        $this->data = $before_data.$string.$after_data;
        $this->pos += $l;
    }

    /**
     *
     * Writes a string in this stream, ending it with CRLF characters.
     *
     * @param string $string the string to write
     *
     * @return int the fwrite result code
     *
     * @throws \Mbcraft\Piol\IOException if this writer is already closed.
     *
     * @api
     */
    function writeln($string)
    {
        $this->checkNotClosed();

        $eol = $this->getLineEndingModeCrlf() ? "\r\n" : "\n";
        $this->write($string.$eol);
    }

    /**
     *
     * Truncate this stream at the specified size.
     *
     * @param int $size the maximum size of the data.
     * @return true if the operation was successful, false otherwise.
     *
     * @throws \Mbcraft\Piol\IOException if this writer is already closed.
     *
     * @api
     */
    function truncate($size)
    {
        $this->checkNotClosed();

        if (strlen($this->data > $size)) {
            $this->data = substr($this->data, 0, $size);
        }
    }

    private function checkNotClosed() {
        if ($this->closed) throw new IOException("The StringBuffer is closed!");
    }

    /**
     * Returns the total content of this buffer.
     *
     * @return string
     */
    function __toString() {
        return $this->data;
    }
}