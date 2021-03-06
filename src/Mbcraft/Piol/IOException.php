<?php

/**
 * This file contains the IOException class.
 */
namespace Mbcraft\Piol {

    /**
     * This class is used as exception class inside the Piol library.
     */
    class IOException extends \Exception {

        /**
         * Constructs a new IOException instance.
         * 
         * @param string $message The string message for this exception.
         * @param int $code An integer code, defaults to null.
         * @param mixed $previous The previous exception in the stack, default to null.
         */
        function __construct($message, $code = null, $previous = null) {
            parent::__construct($message, $code, $previous);
        }
    }

}

