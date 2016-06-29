<?php

use Mbcraft\Piol\Dir;
use Mbcraft\Piol\Cache\FlatDirCache;
use Mbcraft\Piol\IOException;

class FlatDirCacheTest extends PHPUnit_Framework_TestCase
{
    /**
    @test
     */
    function testPlainDirCache()
    {
        
        $f = new Dir("/test/cache_test/cc/");
        
        $this->assertFalse($f->exists(),"La directory della cache esiste già!!");
        
        $cache = new FlatDirCache($f);
        
        $this->assertFalse($f->exists(),"La directory della cache esiste già!!");
        
        $cache->init();
        $this->assertTrue($f->exists(),"La directory della cache non è stata creata!!");
        
        $this->assertFalse($cache->has_key("prova"),"La chiave prova esiste!!");
        
        $cache->set("prova", "Hello!! ");
        
        $this->assertTrue($cache->has_key("prova"),"La chiave prova non esiste!!");
        $this->assertEquals("Hello!! ", $cache->get("prova"),"Il valore salvato non corrisponde!!");
        
        
        $this->assertFalse($cache->has_key("12345"),"La chiave 12345 esiste!!");
        
        $cache->set("12345", "Hello11!! ");
        
        $this->assertTrue($cache->has_key("12345"),"La chiave 12345 non esiste!!");
        $this->assertEquals("Hello11!! ", $cache->get("12345"),"Il valore salvato non corrisponde!!");

        $cache->set("prova", "New content for prova key");
        
        $this->assertTrue($cache->has_key("prova"),"La chiave prova non esiste!!");
        
        $this->assertEquals("New content for prova key", $cache->get("prova"),"Il valore salvato non corrisponde!!");
        
        $cache->delete_key("12345");
        
        $this->assertFalse($cache->has_key("12345"),"La chiave 12345 esiste!!");
        $this->assertTrue($cache->has_key("prova"),"La chiave prova non esiste!!");
        
        $cache->garbage_collect();
        
        $this->assertFalse($cache->has_key("12345"),"La chiave 12345 esiste!!");
        $this->assertTrue($cache->has_key("prova"),"La chiave prova non esiste!!");
        
        $cache->set_entry_expire_time(1);
        $this->assertEquals(1, $cache->get_entry_expire_time());
        
        sleep(5);
        
        $this->assertFalse($cache->has_key("12345"),"La chiave 12345 esiste!!");
        $this->assertFalse($cache->has_key("prova"),"La chiave prova non esiste!!");
        
        $cache->set_entry_expire_time(FlatDirCache::DEFAULT_ENTRY_EXPIRE_TIME);
        
        $cache->set("prova", "Hello!! ");
        $cache->set("12345", "Hello11!! ");
        
        $cache->clean_cache();
        
        $this->assertFalse($cache->has_key("prova"),"La chiave prova non esiste!!");
        $this->assertFalse($cache->has_key("12345"),"La chiave 12345 non esiste!!");
        
        try {
            $cache->get("prova");
            $this->fail();
        } catch (IOException $ex) {

        }
        
        try {
            $cache->get("12345");
            $this->fail();
        } catch (IOException $ex) {

        }
        
        $cache->set_garbage_collection_interval(5);
        $this->assertEquals(5,$cache->get_garbage_collection_interval(),"L'intervallo di garbage collection non corrisponde!!'");

        $v = "another content!\"£$%&/()=?'ì^*é[]@#ù-_.:,;'";
        $v2 = "".$v;
        $cache->set("newkey", $v);
        $this->assertEquals($v2, $cache->get("newkey"),"Il valore nella cache non corrisponde!!");
        
        $cache->delete_key("newkey");
        
        $this->assertFalse($cache->has_key("newkey"),"La chiave newkey non è stata eliminata!!");
        
        $f->delete();
        
        $this->assertFalse($f->exists(),"La directory della cache non è stata eliminata!!");
    }
}

