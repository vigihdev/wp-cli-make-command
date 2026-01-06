<?php

use Vigihdev\WpCliMake\Commands\{
    User_Import_Make_Command,
    User_Make_Command
};

use Vigihdev\WpCliMake\Commands\Term\{
    Term_Import_Make_Command,
    Term_Make_Command,
};

use Vigihdev\WpCliMake\Commands\Menu\Item\{
    Menu_Item_Children_Import_Make_Command,
    Menu_Item_Children_Make_Command,
    Menu_Item_Custom_Make_Command,
    Menu_Item_Import_Make_Command,
    Menu_Item_Make_Command,
    Menu_Item_PostType_Make_Command,
    Menu_Make_Command,
};

use Vigihdev\WpCliMake\Commands\Post\Post\{
    Post_Make_Command,
    Post_Import_Make_Command
};

use Vigihdev\WpCliMake\Commands\Post\Page\{
    Post_Page_Make_Command,
    Post_Page_Import_Make_Command,
};

use Vigihdev\WpCliMake\Commands\Post\PostType\{
    Post_Type_Import_Make_Command,
    Post_Type_Make_Command,
};

if (! class_exists('WP_CLI')) {
    return;
}

$autoloader = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoloader)) {
    require_once $autoloader;
}

// Block Post
WP_CLI::add_command('make:post', new Post_Make_Command());
WP_CLI::add_command('make:post-import', new Post_Import_Make_Command());
WP_CLI::add_command('make:post-page', new Post_Page_Make_Command());
WP_CLI::add_command('make:post-page-import', new Post_Page_Import_Make_Command());
WP_CLI::add_command('make:post-type', new Post_Type_Make_Command());
WP_CLI::add_command('make:post-type-import', new Post_Type_Import_Make_Command());

// Block Menu
WP_CLI::add_command('make:menu', new Menu_Make_Command());
WP_CLI::add_command('make:menu-item', new Menu_Item_Make_Command());
WP_CLI::add_command('make:menu-item-import', new Menu_Item_Import_Make_Command());
WP_CLI::add_command('make:menu-item-custom', new Menu_Item_Custom_Make_Command());
WP_CLI::add_command('make:menu-item-post-type', new Menu_Item_PostType_Make_Command());
WP_CLI::add_command('make:menu-item-children', new Menu_Item_Children_Make_Command());
WP_CLI::add_command('make:menu-item-children-import', new Menu_Item_Children_Import_Make_Command());

WP_CLI::add_command('make:term', new Term_Make_Command());
WP_CLI::add_command('make:term-import', new Term_Import_Make_Command());

WP_CLI::add_command('make:user', new User_Make_Command());
WP_CLI::add_command('make:user-import', new User_Import_Make_Command());
