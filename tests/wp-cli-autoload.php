<?php

if (!defined('HOME')) {
    define('HOME', getenv('HOME') ?? '');
}

if (!defined('STDOUT')) {
    define('STDOUT', fopen('php://stdout', 'w'));
}

if (!defined('STDERR')) {
    define('STDERR', fopen('php://stderr', 'w'));
}

if (!defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

define('BASEPATH', realpath(__DIR__ . '/../'));
define('SOURCE_JSON_PATH', BASEPATH . '/source/data/json');
define('SOURCE_FIELDS_JSON_PATH', SOURCE_JSON_PATH . '/fields');
define('SOURCE_MOCK_JSON_PATH', SOURCE_JSON_PATH . '/mock');

define('WP_CLI_VENDOR', HOME . '/VigihDev/PackagistDev/wp-cli-dev/vendor');
define('WP_CLI_AUTOLOAD', WP_CLI_VENDOR . '/autoload.php');
define('WP_CLI_UTILS', WP_CLI_VENDOR . '/wp-cli/wp-cli/php/utils.php');
define('WP_CLI_WP_CLI', WP_CLI_VENDOR . '/wp-cli/wp-cli/php/wp-cli.php');
define('WP_CLI_BOOT', WP_CLI_VENDOR . '/wp-cli/wp-cli/php/boot-fs.php');


if (file_exists(WP_CLI_AUTOLOAD)) {
    require WP_CLI_UTILS;
    require WP_CLI_AUTOLOAD;
}
