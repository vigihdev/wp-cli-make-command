<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post;

use Throwable;
use Vigihdev\Support\Collection;
use Symfony\Component\Filesystem\Path;
use Vigihdev\WpCliMake\Exceptions\{MakeHandlerException, MakeHandlerExceptionInterface};
use Vigihdev\WpCliModels\Contracts\Fields\FieldInterface;
use Vigihdev\WpCliModels\Support\Transformers\FilepathDtoTransformer;
use Vigihdev\WpCliModels\UI\WpCliStyle;
use Vigihdev\WpCliTools\Validators\FileValidator;
use WP_CLI_Command;

abstract class Base_Post_Command extends WP_CLI_Command
{

    protected const ALLOW_EXTENSION_EXPORT = ['json'];

    protected string $filepath = '';

    protected bool $force = false;

    protected string $fields = '';

    protected FieldInterface $field;

    protected ?WpCliStyle $io = null;

    protected MakeHandlerExceptionInterface $exceptionHandler;

    public function __construct(
        protected readonly string $name
    ) {

        parent::__construct();
        $this->exceptionHandler = new MakeHandlerException();

        if ($this->io === null) {
            $this->io = new WpCliStyle();
        }
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

    /**
     *
     * @param string $dtoClass
     * @return Collection<T>
     */
    protected function transformDto(string $dtoClass): Collection
    {
        try {
            $postDtos = FilepathDtoTransformer::fromFileJson($this->filepath, $dtoClass);
            $postDtos = is_array($postDtos) ? $postDtos : [$postDtos];
            return new Collection(data: $postDtos);
        } catch (Throwable $e) {
            $this->exceptionHandler->handle($e);
            return new Collection([]);
        }
    }
}
