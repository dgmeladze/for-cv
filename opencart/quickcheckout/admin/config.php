<?php
// HTTP
define('HTTP_SERVER', 'yourDomain/admin/');
define('HTTP_CATALOG', 'yourDomain/');

// HTTPS
define('HTTPS_SERVER', 'yourDomain/admin/');
define('HTTPS_CATALOG', 'yourDomain/');

// DIR
define('DIR_APPLICATION', 'pathToSiteDirectory/admin/');
define('DIR_SYSTEM', 'pathToSiteDirectory/system/');
define('DIR_IMAGE', 'pathToSiteDirectory/image/');
define('DIR_STORAGE', 'pathToSiteDirectory/storagenewoptomsumka/');
define('DIR_CATALOG', 'pathToSiteDirectory/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'u1412697_dev');
define('DB_PASSWORD', 'wE3oA2sA8v');
define('DB_DATABASE', 'testoptomsumka');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');


define('SESSION_PREFIX', 'session');

//redis
define('SESSION_HOSTNAME', '127.0.0.1');
define('SESSION_PORT', '6379');
define('SESSION_PASSWORD', '');
define('SESSION_PREFIX', 'sumka_');


define('CACHE_ENGINE', 'memcached');
define('CACHE_HOSTNAME', '127.0.0.1'); 
define('CACHE_PORT', '11211'); 
define('CACHE_PREFIX', 'oc_');