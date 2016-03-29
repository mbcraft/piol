<?php
/**
 * This file contains the ICache interface definition.
 */
namespace Mbcraft\Piol\Cache {
    
    /**
     * This is the common interface used for caches. Actually there is only one implementation of it, 
     * the FlatDirCache class.
     */
    interface ICache {

            /**
             * 
             * Gets the cache entry expire time, in seconds.
             * 
             * @return int the cache entry expire time, in seconds.
             * 
             * @api
             */
            public function get_entry_expire_time();

            /**
             * 
             * Sets the cache entry expire time, in seconds.
             * 
             * @param int $expire_seconds the new cache entry expire time.
             * 
             * @api
             */
            public function set_entry_expire_time($expire_seconds);

            /**
             * 
             * Gets the garbage collection interval, after with a garbage collection
             * is forces.
             * 
             * @return int the garbage collection interval, in seconds.
             * 
             * @api
             */
            public function get_garbage_collection_interval();

            /**
             * 
             * Sets the garbage collection interval, after with a garbage collection
             * is forced, in seconds.
             * 
             * @param int $interval_seconds the new garbage collection interval.
             * 
             * @api
             */
            public function set_garbage_collection_interval($interval_seconds);


            /**
             * Initializes the cache. It is called before each cache access.
             */
            function init();

            /**
             * Executes garbage collection of cache entries. Should be called automatically
             * when garbage_collection_interval time passes after the last garbage collection.
             */
            function garbage_collect();

            /**
             * 
             * Deletes all the cache entries.
             * 
             * @api
             */
            public function clean_cache();

            /**
             * 
             * Returns the existence of a cache entry. An entry expires if not accessed
             * after the *entry_expire_time*. If an entry exists it can always be accessed
             * with get, set or delete_key. Always check an entry before accessing.
             * 
             * @param string $key the key of the entry to access.
             * @return boolean true if the entry exists and is readable, false otherwise.
             * 
             * @api
             */
            public function has_key($key);

            /**
             * 
             * Returns the content of an entry as a string. 
             * The cache entry expire time is reset for this entry.
             * If the entry with this key does not exists this method throws an IOException.
             * 
             * @param string $key the string key of the entry to read
             * @return string the content of the entry as a string
             * @throws \Mbcraft\Piol\IOException if the entry is expired or does not exist.
             * 
             * @api
             */
            public function get($key);
            /**
             * 
             * Sets a cache entry with the string content passed as a parameter.
             * The cache entry expire time is reset.
             * @param string $key the entry key.
             * @param string $content the entry content as a string.
             * 
             * @api
             */
            public function set($key, $content);
            /**
             * 
             * Deletes the cache entry with the specified key. If the entry does not exist nothing is done.
             * 
             * @param string $key the key of the entry.
             * 
             * @api
             */
            public function delete_key($key);
    }

}