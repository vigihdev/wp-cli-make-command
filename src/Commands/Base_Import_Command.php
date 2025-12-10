<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Symfony\Component\Filesystem\Path;
use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\Exceptions\FileException;
use Vigihdev\WpCliModels\Support\Transformers\FilepathDtoTransformer;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\Validators\Support\FileValidator;
use WP_CLI;
use WP_CLI_Command;

abstract class Base_Import_Command extends WP_CLI_Command
{

    public function __construct(
        protected readonly string $name,
    ) {
        return parent::__construct();
    }

    protected function validateFilePath(?string $filepath): void
    {
        if (!$filepath) {
            WP_CLI::error('❌ Path file harus disediakan.');
        }
    }

    protected function normalizeFilePath(string $filepath): string
    {
        if (Path::isRelative($filepath)) {
            return Path::normalize(Path::join(getcwd() ?? '', $filepath));
        }

        return $filepath;
    }

    protected function validateFileJson(string $filepath, CliStyle $io): void
    {
        try {
            $validator = new FileValidator($filepath);
            $validator
                ->mustBeFile()
                ->mustExist()
                ->mustBeJson()
                ->validateForImport();
        } catch (FileException $e) {
            $this->handleFileError($e, $io);
        }
    }

    protected function handleFileError(FileException $e): void
    {
        $errorMsg = sprintf(
            "❌ %s\n   📁 %s\n   💡 %s",
            $e->getMessage(),
            $e->getFilePath(),
            $e->getSuggestion()
        );

        WP_CLI::error($errorMsg);
    }

    protected function loadDataDto(string $filepath, CliStyle $io, string $dtoClass): Collection
    {
        try {
            $postDtos = FilepathDtoTransformer::fromFileJson($filepath, $dtoClass);
            $postDtos = is_array($postDtos) ? $postDtos : [$postDtos];

            return new Collection(data: $postDtos);
        } catch (\Exception $e) {
            $io->errorWithIcon('Error load data dto import: ' . $e->getMessage());
            return new Collection(data: []);
        }
    }
}
