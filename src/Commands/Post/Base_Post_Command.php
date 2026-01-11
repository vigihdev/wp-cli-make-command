<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post;

use RuntimeException;
use Throwable;
use WP_CLI_Command;
use WP_CLI\Utils;
use Vigihdev\Support\Collection;
use Symfony\Component\Filesystem\Path;
use Vigihdev\WpCliMake\Exceptions\{MakeHandlerException, MakeHandlerExceptionInterface};
use Vigihdev\WpCliMake\Support\ImportIoSpinner;
use Vigihdev\WpCliModels\DTOs\Args\Post\CreatePostArgsDto;
use Vigihdev\WpCliModels\DTOs\Fields\DefaultPostFieldDto;
use Vigihdev\WpCliModels\Entities\UserEntity;
use Vigihdev\WpCliModels\Fields\PostField;
use Vigihdev\WpCliModels\Enums\PostStatus;
use Vigihdev\WpCliModels\Support\Transformers\FilepathDtoTransformer;
use Vigihdev\WpCliModels\UI\WpCliStyle;
use Vigihdev\WpCliTools\Validators\FileValidator;

abstract class Base_Post_Command extends WP_CLI_Command
{
    protected const ALLOW_EXTENSION_EXPORT = ['json'];

    protected int $author = 0;

    protected string $title;

    protected string $post_content;

    protected array $postData = [];

    protected array $post_category = [];

    protected string $filepath = '';

    protected bool $force = false;

    protected string $fields = '';

    protected PostField $field;

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
        $this->field = new PostField();
    }

    /**
     * Run command and create post
     *
     * @param array $args
     * @param array $assoc_args
     * @return void
     */
    public function __invoke(array $args, array $assoc_args)
    {
        $this->author = UserEntity::findOne()?->getId() ?? 0;
        $post_category = Utils\get_flag_value($assoc_args, 'post_category');
        $this->post_category = $post_category ?
            array_map(fn ($value) => $value, explode(',', $post_category)) : [];
    }

    /**
     * Normalize filepath to absolute path
     *
     * @return self
     */
    protected function normalizeFilePath(): self
    {
        $this->filepath = Path::isAbsolute($this->filepath) ?
            $this->filepath : Path::join(getcwd() ?? '', $this->filepath);
        return $this;
    }

    /**
     * Validate filepath json file
     *
     * @return void
     */
    protected function validateFilepathJson(): void
    {
        FileValidator::validate($this->filepath)
            ->mustExist()
            ->mustBeExtension('json')
            ->mustBeReadable()
            ->mustBeValidJson();
    }

    /**
     * Validate filepath txt file
     *
     * @return void
     */
    protected function validateFilepathTxt(): void
    {
        FileValidator::validate($this->filepath)
            ->mustExist()
            ->mustBeExtension('txt')
            ->mustBeReadable();
    }

    /**
     * Set post content from filepath or content
     *
     * @return void
     */
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

    /**
     * Read file post content
     *
     * @param string $filepath
     * @return string
     */
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
     * Inspect assoc arguments
     *
     * @param array $assoc_args
     * @return array<string, mixed>
     */
    protected function inspectAassocArgument(array $assoc_args): array
    {
        $data = [];
        if (isset($assoc_args['post_category'])) {
            $data['post_category'] = explode(',', $assoc_args['post_category']);
        }

        if (isset($assoc_args['tags_input'])) {
            $data['tags_input'] = explode(',', $assoc_args['tags_input']);
        }

        if (isset($assoc_args['tax_input'])) {
            $data['tax_input'] = json_decode($assoc_args['tax_input'], true);
        }

        if (isset($assoc_args['meta_input'])) {
            $data['meta_input'] = json_decode($assoc_args['meta_input'], true);
        }

        return $data;
    }

    /**
     * Transform assoc arguments to dto
     *
     * @param array $assoc_args
     * @return CreatePostArgsDto
     */
    protected function transformAassocArgumentToDto(array $assoc_args): CreatePostArgsDto
    {
        $authorStatus = $this->loadAuthorStatus();
        $loadDefaultPost = $this->loadDefaultPost($this->title);
        $inspectData = $this->inspectAassocArgument($assoc_args);
        $assoc_args = array_merge($loadDefaultPost->toArray(), $authorStatus, $assoc_args, $inspectData);
        $assoc_args = array_merge($assoc_args, [
            'post_title' => $this->title,
        ]);

        array_map(function ($value, $key) use (&$assoc_args) {
            if (in_array($key, ['post_author', 'post_parent'])) {
                $assoc_args[$key] = (int)$value;
            }
        }, $assoc_args, array_keys($assoc_args));

        $data = $this->field->dtoTransform($assoc_args);
        return CreatePostArgsDto::fromArray($data);
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

    /**
     * Load default post field dto
     *
     * @param string $title
     * @return DefaultPostFieldDto
     */
    protected function loadDefaultPost(string $title): DefaultPostFieldDto
    {
        return new DefaultPostFieldDto(title: $title);
    }

    /**
     * Load author status
     *
     * @return array
     */
    protected function loadAuthorStatus(): array
    {
        return [
            'post_author' => $this->author,
            'post_status' => PostStatus::PUBLISH->value,
        ];
    }
}
