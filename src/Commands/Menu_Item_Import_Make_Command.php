<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use WP_CLI;
use WP_CLI_Command;

final class Menu_Item_Import_Make_Command extends WP_CLI_Command
{
    /**
     * 
     * @param array $args Positional arguments: [menu_id, title, url]
     * @param array $assoc_args Associative arguments for menu item options
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        WP_CLI::success(
            sprintf('Execute Command from class %s', self::class)
        );
    }
}
