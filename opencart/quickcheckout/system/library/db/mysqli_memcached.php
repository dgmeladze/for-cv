<?php

namespace DB;

final class MySQLi_Memcached {

    private $_link;

    private $_memcached;
    private $_memcached_hostname   = 'localhost';
    private $_memcached_port       =  11211;
    private $_memcached_expiration =  3600;

    public function __construct($hostname,
                                $username,
                                $password,
                                $database) {

        $this->_memcached = new \Memcached();
        $this->_memcached->addServer($this->_memcached_hostname,
            $this->_memcached_port);

        $this->_link = new \mysqli($hostname,
            $username,
            $password,
            $database);

        if ($this->_link->connect_error) {
            trigger_error($this->_link->connect_errno . $this->_link->connect_error);
            exit;
        }

        $this->_link->set_charset("utf8");
        $this->_link->query("SET SQL_MODE = ''");
    }

    public function query($sql) {

        $select_id = (0 <= stripos($sql, 'SELECT') && !stripos($sql, 'FROM ' . DB_PREFIX . 'cart')) ? sha1($sql) : false;

        if ($select_id && false !== $data = $this->_memcached->get($select_id)) {

            $num_rows = 0; foreach ($data as $key => $value) $num_rows++;

            $result           = new \stdClass();
            $result->num_rows = $num_rows;
            $result->row      = isset($data[0]) ? $data[0] : array();
            $result->rows     = $data;

            return $result;
        }

        $query = $this->_link->query($sql);

        if (!$this->_link->errno) {

            if ($query instanceof \mysqli_result) {

                $data = array();
                while ($row = $query->fetch_assoc()) {
                    $data[] = $row;
                }

                if ($select_id) {
                    $this->_memcached->set($select_id, $data, $this->_memcached_expiration);
                }

                $result           = new \stdClass();
                $result->num_rows = $query->num_rows;
                $result->row      = isset($data[0]) ? $data[0] : array();
                $result->rows     = $data;

                $query->close();

                return $result;

            } else {
                return true;
            }
        } else {
            trigger_error($this->_link->error  . $this->_link->errno . $sql);

            return false;
        }
    }

    public function escape($value) {
        return $this->_link->real_escape_string($value);
    }

    public function countAffected() {
        return $this->_link->affected_rows;
    }

    public function getLastId() {
        return $this->_link->insert_id;
    }

    public function __destruct() {
        $this->_link->close();
    }
}
