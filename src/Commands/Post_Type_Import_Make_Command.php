<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Symfony\Component\Filesystem\Path;
use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\DTOs\Fields\DefaultPostFieldDto;
use Vigihdev\WpCliModels\DTOs\Fields\PostTypeFieldDto;
use Vigihdev\WpCliModels\Entities\PostEntity;
use Vigihdev\WpCliModels\Exceptions\FileException;
use Vigihdev\WpCliModels\Support\Transformers\FilepathDtoTransformer;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\UI\Components\{ImportSummary, ProgressLog};
use Vigihdev\WpCliModels\Validators\Support\FileValidator;
use WP_CLI;
use WP_CLI\Utils;
use WP_CLI_Command;
use WP_Query;

final class Post_Type_Import_Make_Command extends WP_CLI_Command
{

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
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run');
        $io = new CliStyle();

        // Validasi file path
        if (!$filepath) {
            WP_CLI::error('❌ Path file harus disediakan.');
        }

        $filepath = Path::isRelative($filepath) ?
            Path::normalize(Path::join(getcwd() ?? '', $filepath))
            : $filepath;

        try {
            $validator = new FileValidator($filepath);
            $filepath = $validator
                ->mustBeFile()
                ->mustExist()
                ->mustBeJson()
                ->validateForImport();

            if ($dryRun) {
                $this->dryRun($filepath, $io);
                return;
            }
            $this->executeImport($filepath, $io);
        } catch (FileException $e) {
            $errorMsg = sprintf(
                "❌ %s\n   📁 %s\n   💡 %s",
                $e->getMessage(),
                $e->getFilePath(),
                $e->getSuggestion()
            );

            WP_CLI::error($errorMsg);
        }


        // WP_CLI::success(
        //     sprintf('Execute Command from class %s', self::class)
        // );
    }

    private function dryRun(string $filepath, CliStyle $io): void
    {
        $io->title('🔍 DRY RUN - Preview Data Import');
        $io->note('Tidak ada perubahan ke database');
        $io->hr();

        try {
            /** @var PostTypeFieldDto[] $postDtos */
            $postDtos = FilepathDtoTransformer::fromFileJson(
                $filepath,
                PostTypeFieldDto::class
            );

            if (empty($postDtos)) {
                $io->warning('Tidak ada data ditemukan dalam file.');
                return;
            }

            // Display table
            $headers = ['No', 'Title', 'Type', 'Status', 'Author', 'Taxonomies'];
            $rows = [];

            foreach ($postDtos as $index => $post) {
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
            $io->definitionList([
                'Total Posts' => (string) count($postDtos),
                'Mode' => 'Dry Run',
                'File' => basename($filepath)
            ]);

            $io->successWithIcon('Dry run selesai!');
            $io->block('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.', 'note');
        } catch (\Exception $e) {
            $io->errorWithIcon('Error: ' . $e->getMessage());
        }
    }
    private function executeImport(string $filepath, CliStyle $io): void
    {
        $io->title('🚀 Memulai Import Posts');
        $io->note('Mode: EXECUTE - Data akan dimasukkan ke database');
        $io->hr();

        $start_time = microtime(true);

        $colection = new Collection(data: $this->getPostTypeDto($filepath, $io));
        $io->info("📊 Menemukan {$colection->count()} post(s) untuk diimport.");

        foreach ($colection->getIterator() as $post) {
        }

        try {

            /** @var PostTypeFieldDto[] $postDtos */
            $postDtos = FilepathDtoTransformer::fromFileJson(
                $filepath,
                PostTypeFieldDto::class
            );

            $total = count($postDtos);
            $io->info("📊 Menemukan {$total} post(s) untuk diimport.");

            if ($total === 0) {
                $io->warning('Tidak ada data untuk diimport.');
                return;
            }

            $summary = new ImportSummary();
            $log = new ProgressLog(io: $io, total: count($postDtos));
            $io->newLine();
            $log->start();


            foreach ($postDtos as $index => $post) {
                $current = $index + 1;
                $title = $post->getTitle();

                $io->text("[{$current}/{$total}] 📝 Processing: {$title}");

                // Check if post already exists
                $exists = PostEntity::existsByName(sanitize_title($title));
                $exists = !$exists ? PostEntity::existsByTitle($title) : $exists;

                if ($exists) {
                    $log->warn("Post '{$title}' sudah ada, dilewati.");
                    $summary->addSkipped();
                    continue;
                }

                // Prepare post data
                $postData = $this->preparePostData($post, $io);
                if (empty($postData)) {
                    $io->warning("⚠️  Data post kosong, dilewati.");
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

            $summary->render($io, $filepath, (microtime(true) - $start_time));
        } catch (\Exception $e) {
            $io->errorWithIcon('❌ Error selama import: ' . $e->getMessage());
        }
    }

    private function getPostTypeDto(string $filepath, CliStyle $io): array
    {
        try {

            /** @var PostTypeFieldDto[] $postDtos */
            $postDtos = FilepathDtoTransformer::fromFileJson(
                $filepath,
                PostTypeFieldDto::class
            );

            $postDtos = !is_array($postDtos) ? [$postDtos] : $postDtos;

            if (empty($postDtos)) {
                $io->warning('Tidak ada data untuk diimport.');
                return [];
            }
            return $postDtos;
        } catch (\Exception $e) {
            $io->errorWithIcon('❌ Error selama import: ' . $e->getMessage());
            return [];
        }
    }


    /**
     * Helper: Prepare post data from DTO
     */
    private function preparePostData(PostTypeFieldDto $post, CliStyle $io): array
    {
        $title = $post->getTitle();

        // Prepare default values
        $default = new DefaultPostFieldDto(title: $title);
        $postData = array_merge($default->toArray(), $post->toArray());

        // Handle taxonomies jika ada
        $tax_input = [];
        foreach ($post->getTaxonomiInputs() as $tax) {
            $taxonomy = $tax->getTaxonomy();
            $terms = $tax->getTerms();

            if (!empty($terms)) {
                $tax_input[$taxonomy] = $terms;
                $io->text("  🏷️  Taxonomy: {$taxonomy} => " . implode(', ', $terms));
            }
        }

        if (!empty($tax_input)) {
            $postData['tax_input'] = $tax_input;
        }

        return $postData;
    }
}
