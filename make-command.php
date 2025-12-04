<?php

use Vigihdev\WpCliMake\Commands\{Menu_Make_Command, Post_Make_Command, Taxonomy_Make_Command, User_Make_Command};

if (! class_exists('WP_CLI')) {
    return;
}

$autoloader = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
}

WP_CLI::add_command('make:post', new Post_Make_Command());
WP_CLI::add_command('make:menu', new Menu_Make_Command());
WP_CLI::add_command('make:taxonomy', new Taxonomy_Make_Command());
WP_CLI::add_command('make:user', new User_Make_Command());
