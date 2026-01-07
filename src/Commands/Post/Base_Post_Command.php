<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post;

use RuntimeException;
use Throwable;
use Vigihdev\Support\Collection;
use Symfony\Component\Filesystem\Path;
use Vigihdev\WpCliMake\Exceptions\{MakeHandlerException, MakeHandlerExceptionInterface};
use Vigihdev\WpCliMake\Support\ImportIoSpinner;
use Vigihdev\WpCliModels\Contracts\Fields\FieldInterface;
use Vigihdev\WpCliModels\DTOs\Fields\DefaultPostFieldDto;
use Vigihdev\WpCliModels\Entities\UserEntity;
use Vigihdev\WpCliModels\Enums\PostStatus;
use Vigihdev\WpCliModels\Support\Transformers\FilepathDtoTransformer;
use Vigihdev\WpCliModels\UI\WpCliStyle;
use Vigihdev\WpCliTools\Validators\FileValidator;
use WP_CLI_Command;

abstract class Base_Post_Command extends WP_CLI_Command
{

    protected const ALLOW_EXTENSION_EXPORT = ['json'];

    protected int $author = 0;

    protected string $title;

    protected string $post_content;

    protected array $postData = [];

    protected string $filepath = '';

    protected bool $force = false;

    protected string $fields = '';

    protected FieldInterface $field;

    protected WpCliStyle $io;

    protected ImportIoSpinner $importIo;

    protected MakeHandlerExceptionInterface $exceptionHandler;

    public function __construct(
        private readonly string $name
    ) {

        parent::__construct();
        $this->io = new WpCliStyle();
        $this->exceptionHandler = new MakeHandlerException();
        $this->importIo = new ImportIoSpinner($this->io);
    }

    public function __invoke(array $args, array $assoc_args)
    {
        $this->author = UserEntity::findOne()?->getId() ?? 0;
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

    protected function validateFilepathTxt(): void
    {
        FileValidator::validate($this->filepath)
            ->mustExist()
            ->mustBeExtension('txt')
            ->mustBeReadable();
    }

    protected function setPostContent(): void
    {
        $post_content = $this->post_content;
        if (!$post_content) {
            return;
        }

        if (str_starts_with($post_content, '@')) {
            $this->post_content = $this->readFilePostContent($post_content);
        }
    }

    protected function readFilePostContent(string $filepath): string
    {

        $post_content = '';
        try {
            $filepath = ltrim($filepath, '@');
            $filepath = Path::isAbsolute($filepath) ? $filepath : Path::join(getcwd() ?? '', $filepath);

            FileValidator::validate($filepath)
                ->mustBeExtension('txt')
                ->mustExist()
                ->mustBeReadable();

            $handle = fopen($filepath, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $post_content .= $line;
                }
                fclose($handle);
            }
            return $post_content;
        } catch (Throwable $e) {
            throw new RuntimeException($e->getMessage());
            return $post_content;
        }
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

    protected function loadDefaultPost(string $title)
    {
        return new DefaultPostFieldDto(title: $title);
    }

    protected function loadAuthorStatus(): array
    {
        return [
            'post_author' => $this->author,
            'post_status' => PostStatus::PUBLISH->value,
        ];
    }

    private function getOneAuthor() {}
    private function hasDuplicateTitle() {}
    private function hasDuplicateUrl() {}
    private function hasDuplicateName() {}
}
