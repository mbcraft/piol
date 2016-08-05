<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 05/08/16
 * Time: 17.23
 */
namespace Mbcraft\Piol\Utils {

    use Mbcraft\Piol\IOException;

    /**
     * Class StringUtils
     *
     * This class contains methods for working with strings.
     *
     * @package Mbcraft\Piol\Utils
     */
    class StringUtils {

        /**
         *
         * Checks if the parameter is a string of exactly one character.
         *
         * @param string $ch The string to check
         * @param string $message message to use for exception
         * @return bool true if the parameter is a character, false otherwise.
         * @throws IOException If  the parameter is not a string of one character.
         *
         * @api
         */
        public static function checkChar($ch, $message="The parameter is not a string of length 1!!")
        {
            if (is_string($ch) && strlen($ch) === 1) return true;
            else
                throw new IOException($message);
        }
    }

}