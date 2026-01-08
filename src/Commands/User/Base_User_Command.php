<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\User;

use WP_CLI_Command;
use Symfony\Component\Filesystem\Path;
use Vigihdev\WpCliMake\Exceptions\{MakeHandlerException, MakeHandlerExceptionInterface};
use Vigihdev\WpCliMake\Support\ImportIoSpinner;
use Vigihdev\WpCliModels\Contracts\Fields\FieldInterface;
use Vigihdev\WpCliModels\UI\WpCliStyle;
use Vigihdev\WpCliTools\Validators\FileValidator;


abstract class Base_User_Command extends WP_CLI_Command
{

    protected string $fields = '';

    protected FieldInterface $field;

    protected string $filepath = '';

    protected bool $force = false;

    protected WpCliStyle $io;

    protected ImportIoSpinner $importIo;

    protected MakeHandlerExceptionInterface $exceptionHandler;

    public function __construct(
        protected readonly string $name
    ) {
        parent::__construct();
        $this->io = new WpCliStyle();
        $this->exceptionHandler = new MakeHandlerException();
        $this->importIo = new ImportIoSpinner($this->io);
    }

    public function __invoke(array $args, array $assoc_args) {}

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
