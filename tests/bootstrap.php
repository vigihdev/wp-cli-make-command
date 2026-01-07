<?php

use Symfony\Component\Filesystem\Path;

error_reporting(-1);

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

require_once $fileWpInstallations;
require_once $fileCliAutoLoad;
