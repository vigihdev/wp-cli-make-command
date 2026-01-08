<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Term;

use Symfony\Component\Filesystem\Path;
use Vigihdev\WpCliMake\Exceptions\{MakeHandlerExceptionInterface, MakeHandlerException};
use Vigihdev\WpCliModels\UI\WpCliStyle;
use WP_CLI_Command;
use Vigihdev\WpCliMake\DTOs\TermDto;
use Vigihdev\WpCliMake\Support\ImportIoSpinner;
use Vigihdev\WpCliModels\Validators\FileValidator;

abstract class Base_Term_Command extends WP_CLI_Command
{

    protected string $fields = '';
    protected ?string $term = null;
    protected ?string $taxonomy = null;
    protected ?string $slug = null;
    protected ?string $description = null;

    protected string $filepath = '';

    protected MakeHandlerExceptionInterface $exceptionHandler;
    protected WpCliStyle $io;
    protected ImportIoSpinner $importIo;



    public function __construct(
        protected readonly string $name
    ) {
        parent::__construct();
        $this->exceptionHandler = new MakeHandlerException();
        $this->io = new WpCliStyle();
        $this->importIo = new ImportIoSpinner($this->io);
    }

    public function __invoke(array $args, array $assoc_args) {}

    protected function setSlug()
    {
        if (!$this->slug) {
            $this->slug = sanitize_title($this->term);
        }
    }

    protected function getTermDto()
    {
        return new TermDto(
            taxonomy: $this->taxonomy,
            term: $this->term,
            slug: $this->slug,
            description: $this->description,
        );
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
