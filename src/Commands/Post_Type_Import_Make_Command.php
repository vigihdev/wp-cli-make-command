<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Symfony\Component\Filesystem\Path;
use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\DTOs\Fields\{DefaultPostFieldDto, PostTypeFieldDto};
use Vigihdev\WpCliModels\Entities\PostEntity;
use Vigihdev\WpCliModels\Exceptions\FileException;
use Vigihdev\WpCliModels\Support\Transformers\FilepathDtoTransformer;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\UI\Components\{ImportSummary, ProgressLog};
use Vigihdev\WpCliModels\Validators\Support\FileValidator;
use WP_CLI;
use WP_CLI\Utils;

final class Post_Type_Import_Make_Command extends Base_Command
{

    public function __construct()
    {
        parent::__construct(name: 'wp make:post-type-import');
    }

    private function validateFilePath(?string $filepath, CliStyle $io): void
    {
        if (!$filepath) {
            WP_CLI::error('❌ Path file harus disediakan.');
        }
    }

    private function normalizeFilePath(string $filepath): string
    {
        if (Path::isRelative($filepath)) {
            return Path::normalize(Path::join(getcwd() ?? '', $filepath));
        }

        return $filepath;
    }

    private function validateFile(string $filepath, CliStyle $io): void
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

    private function handleFileError(FileException $e, CliStyle $io): void
    {
        $errorMsg = sprintf(
            "❌ %s\n   📁 %s\n   💡 %s",
            $e->getMessage(),
            $e->getFilePath(),
            $e->getSuggestion()
        );

        WP_CLI::error($errorMsg);
    }

    /**
     * Import Post Type dari file JSON.
     *
     * ## OPTIONS
     *
     * [<file>]
     * : Jalankan tanpa perubahan database.
     * 
     * [--dry-run]
     * : Jalankan tanpa perubahan database.
     * 
     * ## EXAMPLES
     *
     *     wp make:post-type-import /path/to/posts.json
     *     wp make:post-type-import /path/to/posts.json --dry-run
     *
     * @when after_wp_load
     * 
     * @param array $args
     * @param array $assoc_args
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        $filepath = isset($args[0]) ? $args[0] : null;
        $io = new CliStyle();

        $this->validateFilePath($filepath, $io);
        $filepath = $this->normalizeFilePath($filepath);
        $this->validateFile($filepath, $io);

        $this->executeCommand($filepath, $assoc_args, $io);
    }

    protected function executeCommand(string $filepath, array $assoc_args, CliStyle $io): void
    {
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run');
        try {
            $collection = $this->loadData($filepath, $io);
            if ($dryRun) {
                $this->proccessDryRun($filepath, $collection, $io);
                return;
            }
            $this->proccessImport($filepath, $collection, $io);
        } catch (\Exception $e) {
            $io->errorWithIcon('❌ Error load data import: ' . $e->getMessage());
        }
    }

    private function proccessDryRun(string $filepath, Collection $collection, CliStyle $io)
    {
        $io->title('🔍 DRY RUN - Preview Data Import');
        $io->note('Tidak ada perubahan ke database');

        // Display table
        $headers = ['No', 'Title', 'Type', 'Status', 'Author', 'Taxonomies'];
        $rows = [];

        foreach ($collection->getIterator() as $index => $post) {
            $taxonomies = [];
            foreach ($post->getTaxonomiInputs() as $tax) {
                $taxonomy = $tax->getTaxonomy();
                $terms = $tax->getTerms();
                $taxonomies[] = sprintf('%s: [%s]', $taxonomy, implode(', ', $terms));
            }

            $rows[] = [
                $index + 1,
                $post->getTitle(),
                $post->getType(),
                $post->getStatus(),
                $post->getAuthor() ?: '1 (default)',
                $taxonomies ? implode('; ', $taxonomies) : 'none'
            ];
        }

        $io->table($rows, $headers);

        // Summary dengan definition list
        $io->newLine();
        $io->hr('-', 75);
        $io->definitionList([
            'Total Posts' => (string) $collection->count(),
            'Mode' => 'Dry Run',
            'File' => basename($filepath)
        ]);
        $io->hr('-', 75);

        $io->successWithIcon('Dry run selesai!');
        $io->block('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.', 'note');
    }

    private function proccessImport(string $filepath, Collection $collection, CliStyle $io)
    {
        $io->title('🚀 Memulai Import Posts');
        $io->note('Mode: EXECUTE - Data akan dimasukkan ke database');
        $io->hr();
        $start_time = microtime(true);

        $log = new ProgressLog(io: $io, total: $collection->count());

        $io->info("📊 Menemukan {$collection->count()} post(s) untuk diimport.");
        $io->newLine();
        $log->start();

        $summary = new ImportSummary();
        foreach ($collection->getIterator() as $post) {
            /** @var PostTypeFieldDto $post */
            $title = $post->getTitle();
            $log->processing($title);

            // Check if post already exists
            $exists = PostEntity::existsByName(sanitize_title($title));
            $exists = !$exists ? PostEntity::existsByTitle($title) : $exists;

            $default = new DefaultPostFieldDto(title: $title);
            $postData = array_merge($default->toArray(), $post->toArray());

            if ($exists) {
                $log->warning("Post '{$title}' sudah ada, dilewati.");
                $summary->addSkipped();
                continue;
            }

            // Create post
            $create = PostEntity::create($postData);
            if (is_wp_error($create)) {
                $io->errorLog("  ❌ Gagal create '{$title}': " . $create->get_error_message());
                $summary->addFailed();
            } else {
                $io->success("  ✅ Berhasil create post ID {$create}");
                $summary->addSuccess();
            }
        }
        $log->end();

        $summary->renderCompact($io, $filepath, (microtime(true) - $start_time));
    }

    private function loadData(string $filepath, CliStyle $io): Collection
    {
        try {
            $postDtos = FilepathDtoTransformer::fromFileJson($filepath, PostTypeFieldDto::class);
            $postDtos = is_array($postDtos) ? $postDtos : [$postDtos];

            return new Collection(data: $postDtos);
        } catch (\Exception $e) {
            $io->errorWithIcon('❌ Error load data import: ' . $e->getMessage());
            return new Collection([]);
        }
    }
}
