<?php

if (!defined('HOME')) {
    define('HOME', getenv('HOME') ?? '');
}

define('WP_INSTALLATION_AUTOLOAD', HOME . '/Sites/okkarent-group/gannettrans/wp-load.php');

if (! file_exists(WP_INSTALLATION_AUTOLOAD)) {
    throw new RuntimeException("Error File Load Not Found " . WP_INSTALLATION_AUTOLOAD);
}

require_once WP_INSTALLATION_AUTOLOAD;
