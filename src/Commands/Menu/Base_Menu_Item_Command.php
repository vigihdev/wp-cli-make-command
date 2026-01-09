<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu;

use Vigihdev\WpCliMake\Exceptions\{MakeHandlerException, MakeHandlerExceptionInterface};
use Vigihdev\WpCliModels\DTOs\Args\Menu\CustomItemMenuArgsDto;
use Vigihdev\WpCliModels\UI\WpCliStyle;
use WP_CLI_Command;

abstract class Base_Menu_Item_Command extends WP_CLI_Command
{

    protected ?string $menu = null;

    protected ?string $title = null;

    protected ?string $link = null;

    protected string $filepath = '';

    protected WpCliStyle $io;

    protected MakeHandlerExceptionInterface $exceptionHandler;

    public function __construct(
        private readonly string $name
    ) {

        parent::__construct();
        $this->io = new WpCliStyle();
        $this->exceptionHandler = new MakeHandlerException();
    }

    public function __invoke(array $args, array $assoc_args) {}

    protected function instanceCustomMenuItem(array $assoc_args): CustomItemMenuArgsDto
    {
        return CustomItemMenuArgsDto::fromArray(array_merge($assoc_args, [
            'menu' => $this->menu,
            'title' => $this->title,
            'link' => $this->link,
        ]));
    }
}
