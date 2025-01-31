<?php
// HTTP
define('HTTP_SERVER', 'yourDomain/');

// HTTPS
define('HTTPS_SERVER', 'yourDomain/');

// DIR
define('DIR_APPLICATION', 'pathToSiteDirectory/catalog/');
define('DIR_SYSTEM', 'pathToSiteDirectory/system/');
define('DIR_IMAGE', 'pathToSiteDirectory/image/');
define('DIR_STORAGE', 'pathToSiteDirectory/storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli_memcached');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'username');
define('DB_PASSWORD', 'password');
define('DB_DATABASE', 'dbname');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');


define('SESSION_PREFIX', 'session');

//redis
define('SESSION_HOSTNAME', '127.0.0.1');
define('SESSION_PORT', '6379');
define('SESSION_PASSWORD', '');
define('SESSION_PREFIX', 'prefix_');

define('CACHE_ENGINE', 'memcached');


define('CACHE_HOSTNAME', '127.0.0.1');
define('CACHE_PORT', '11211');
define('CACHE_PREFIX', 'oc_');



