<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 05/08/16
 * Time: 17.23
 */
namespace Mbcraft\Piol\Utils {

    use Mbcraft\Piol\IReader;
    use Mbcraft\Piol\IWriter;
    use Mbcraft\Piol\IOException;

    /**
     * Class CsvUtils
     *
     * This class contains generic methods useful for reading and writing csv data
     * with IReader and IWriter implementations.
     *
     * @package Mbcraft\Piol\Utils
     *
     */
    class CsvUtils {

        /**
         *
         * Reads a line from this file as a CSV (comma separated value) entry.
         *
         * @param \Mbcraft\Piol\IReader $reader the reader to use for reading this entry
         * @param string $delimiter The delimiter char used, defaults to ','.
         * @param string $enclosure The enclosure char used, defaults to '"'.
         * @param string $escape The escape char used, defaults to '\'.
         * @return array an array of ordered fields readed.
         * @throws \Mbcraft\Piol\IOException If the parameters are not valid or the entry contains unexpected characters.
         *
         * @api
         */
        public static function read(IReader $reader,$delimiter = ",", $enclosure = '"', $escape = '\\') {

            if ($reader->isOpen()) {
                StringUtils::checkChar($delimiter, "The delimiter is not a valid character.");
                StringUtils::checkChar($enclosure, "The enclosure is not a valid character.");
                StringUtils::checkChar($escape, "The escape is not a valid character.");

                $line = $reader->readLine();

                if (strlen(trim($line)) === 0)
                    return null;

                $fields = array();
                $current_field = "";
                $i = 0;
                $escaped = false;
                $e = 0;
                while ($i < strlen($line)) {
                    $c = $line[$i++];
                    //echo $c."\n";
                    if ($escaped) {
                        $current_field .= $c;
                        $escaped = false;
                        continue;
                    }
                    if ($c === $escape) {
                        $escaped = true;
                        continue;
                    }
                    if (($c === $enclosure) && (strlen($current_field) === 0) && ($e === 0)) {
                        $e++;
                        //skip
                        continue;
                    }
                    if (($c === $enclosure) && ($e === 1)) {
                        $e++;
                        //skip
                        continue;
                    }
                    if (($c === $delimiter) && (($e === 2) || ($e === 0 && strlen($current_field) > 0))) {
                        $fields[] = $current_field;
                        $current_field = "";
                        $e = 0;
                        continue;
                    }
                    if ($e === 2)
                        throw new IOException("Error in CSV data format. Delimiter not found after enclosure.");
                    $current_field .= $c;
                }

                $fields[] = $current_field;

                return $fields;
            } else
                throw new IOException("The reader is not open!!");
        }

        /**
         *
         * Writes a line into this writer following the CSV (comma separated values) convention.
         *
         * @param \Mbcraft\Piol\IWriter $writer the writer to use for writing this entry
         * @param array $values the ordered array of values to write.
         * @param string $delimiter the one string character to use as a delimiter.
         * @param string $enclosure the one string character to use as an enclosure.
         * @param string $escape the one string character to use as an escape character.
         *
         * @return int the write result code
         *
         * @throws \Mbcraft\Piol\IOException if this writer is already closed.
         *
         * @api
         */
        public static function write(IWriter $writer,$values, $delimiter = ",", $enclosure='"', $escape='\\') {

            if ($writer->isOpen()) {

                StringUtils::checkChar($delimiter, "The delimiter is not a valid character.");
                StringUtils::checkChar($enclosure, "The enclosure is not a valid character.");
                StringUtils::checkChar($escape, "The escape is not a valid character.");

                $final_string = "";

                foreach ($values as $v) {

                    if (is_string($v)) {
                        $st_value = "" . $v;
                        $st_value = str_replace($escape, $escape . $escape, $st_value);
                        $v_final = str_replace($enclosure, $escape . $enclosure, $st_value);
                    } else {
                        $v_final = $v;
                    }
                    $final_string .= $enclosure;
                    $final_string .= $v_final;
                    $final_string .= $enclosure . $delimiter;
                }

                $result = $writer->writeln(substr($final_string, 0, strlen($final_string) - 1));

                return $result;

            } else
                throw new \IOException("The writer is not open!");
        }

    }
}