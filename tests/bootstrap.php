<?php

use Symfony\Component\Filesystem\Path;

error_reporting(-1);

define('HOME', getenv('HOME') ?? '');
// define('STDIN', fopen('php://stdin', 'r'));
// define('STDOUT', fopen('php://stdout', 'w'));
// define('STDERR', fopen('php://stderr', 'w'));

define('BASEPATH', realpath(__DIR__ . '/../'));
define('SOURCE_JSON_PATH', BASEPATH . '/source/data/json');
define('SOURCE_FIELDS_JSON_PATH', SOURCE_JSON_PATH . '/fields');
define('SOURCE_MOCK_JSON_PATH', SOURCE_JSON_PATH . '/mock');

define('WP_INSTALLATION_AUTOLOAD', HOME . '/Sites/okkarent-group/gannettrans/wp-load.php');
define('WP_CLI_VENDOR', HOME . '/VigihDev/PackagistDev/wp-cli-dev/vendor');
define('WP_CLI_AUTOLOAD', WP_CLI_VENDOR . '/autoload.php');
define('WP_CLI_UTILS', WP_CLI_VENDOR . '/wp-cli/wp-cli/php/utils.php');
define('WP_CLI_WP_CLI', WP_CLI_VENDOR . '/wp-cli/wp-cli/php/wp-cli.php');
define('WP_CLI_BOOT', WP_CLI_VENDOR . '/wp-cli/wp-cli/php/boot-fs.php');


/** @var Composer\Autoload\ClassLoader $autoload  */
$autoload = require __DIR__ . '/../vendor/autoload.php';

// Wp Load and Cli
$pathWpInstallation = Path::join(getenv('HOME') ?? '', 'Sites', 'okkarent-group', 'omahtrans');
$fileWpInstallations = Path::join($pathWpInstallation, 'wp-load.php');
$fileCliAutoLoad = Path::join(getenv('HOME') ?? '', 'VigihDev', 'PackagistDev', 'wp-cli-dev', 'vendor', 'autoload.php');

if (! file_exists($fileCliAutoLoad)) {
    throw new RuntimeException("Error File Load Not Found {$fileWpInstallations}");
}


if (! file_exists($fileCliAutoLoad)) {
    throw new RuntimeException("Error File Load Not Found {$fileCliAutoLoad}");
}


if (file_exists(WP_CLI_AUTOLOAD)) {
    require WP_CLI_UTILS;
    require WP_CLI_AUTOLOAD;
}
require_once $fileWpInstallations;
require_once $fileCliAutoLoad;
