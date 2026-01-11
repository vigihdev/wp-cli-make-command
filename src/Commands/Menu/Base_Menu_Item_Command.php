<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Menu;

use Symfony\Component\Filesystem\Path;
use Vigihdev\WpCliMake\Exceptions\{MakeHandlerException, MakeHandlerExceptionInterface};
use Vigihdev\WpCliMake\Support\ImportIoSpinner;
use Vigihdev\WpCliModels\DTOs\Args\Menu\CustomItemMenuArgsDto;
use Vigihdev\WpCliModels\Enums\PostStatus;
use Vigihdev\WpCliModels\Fields\MenuItemCustomField;
use Vigihdev\WpCliModels\UI\WpCliStyle;
use Vigihdev\WpCliModels\Validators\FileValidator;
use WP_CLI_Command;

abstract class Base_Menu_Item_Command extends WP_CLI_Command
{
    protected ?string $menu = null;

    protected ?string $title = null;

    protected ?string $link = null;

    protected string $filepath = '';

    protected MenuItemCustomField $field;

    protected WpCliStyle $io;

    protected ImportIoSpinner $importIo;

    protected MakeHandlerExceptionInterface $exceptionHandler;

    public function __construct(
        private readonly string $name
    ) {
        parent::__construct();
        $this->io = new WpCliStyle();
        $this->exceptionHandler = new MakeHandlerException();
        $this->field = new MenuItemCustomField();
        $this->importIo = new ImportIoSpinner($this->io);
    }

    public function __invoke(array $args, array $assoc_args)
    {
    }

    protected function instanceCustomMenuItem(array $assoc_args): CustomItemMenuArgsDto
    {
        $defaults = [
            'menu' => $this->menu,
            'title' => $this->title,
            'link' => $this->link,
        ];
        $data = array_merge($assoc_args, $defaults);
        return CustomItemMenuArgsDto::fromArray($this->field->dtotransform($data));
    }

    public function transformMenuItemData(array $assoc_args): array
    {
        $data = array_merge($assoc_args, [
            'title' => $this->title,
            'url' => $this->link,
            'status' => PostStatus::PUBLISH->value
        ]);
        return (new MenuItemCustomField())->transform($data);
    }

    protected function normalizeFilePath(): self
    {
        $this->filepath = Path::isAbsolute($this->filepath) ?
            $this->filepath : Path::join(getcwd() ?? '', $this->filepath);
        return $this;
    }

    protected function validateFilepathJson(): void
    {
        FileValidator::validate($this->filepath)
            ->mustExist()
            ->mustBeExtension('json')
            ->mustBeReadable()
            ->mustBeValidJson();
    }
}
