<?php
namespace Session;

class Redis
{

    public $expire = '';

    protected $prefix = 'sumka_';

    public function __construct($expire)
    {
        if (! extension_loaded('redis')) {
            die('The server does not support redis extension!');
        }

        $this->cache = new \Redis();
        $this->cache->pconnect(SESSION_HOSTNAME, SESSION_PORT);

        if (! empty(SESSION_PASSWORD)) {
            if (! $this->cache->auth(SESSION_PASSWORD)) {
                die('redis: wrong password！');
            }
        }

        $this->expire = ini_get('session.gc_maxlifetime');
    }

    public function read($session_id)
    {
        if ($this->cache->exists($this->prefix . $session_id)) {
            return json_decode($this->cache->get($this->prefix . $session_id), true);
        }
        return false;
    }

    public function write($session_id, $data)
    {
        $status = $this->cache->set($this->prefix . $session_id, json_encode($data));
        if ($status) {
            $this->cache->expire($this->prefix . $session_id, $this->expire);
        }
        return $status;
    }

    public function destroy($session_id)
    {
        if ($this->cache->exists($this->prefix . $session_id)) {
            $this->cache->del($this->prefix . $session_id);
        }

        return true;
    }

    public function gc($expire)
    {
        // $this->cache->del($this->prefix . $expire);
        return true;
    }
}