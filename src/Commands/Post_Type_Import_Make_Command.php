<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Symfony\Component\Filesystem\Path;
use Vigihdev\WpCliModels\DTOs\Fields\DefaultPostFieldDto;
use Vigihdev\WpCliModels\DTOs\Fields\PostTypeFieldDto;
use Vigihdev\WpCliModels\Entities\PostEntity;
use Vigihdev\WpCliModels\Support\Transformers\FilepathDtoTransformer;
use Vigihdev\WpCliModels\UI\CliStyle;
use WP_CLI;
use WP_CLI_Command;
use WP_Query;

final class Post_Type_Import_Make_Command extends WP_CLI_Command
{

    /**
     * Import posts from a JSON file.
     *
     *
     * ## OPTIONS
     * 
     * [--file=<path>]
     *  : opsi file JSON
     * 
     * [--dry-run]
     * : Preview data without importing.
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

        // Validasi file path
        if (!$filepath) {
            WP_CLI::error('❌ Path file harus disediakan.');
        }

        $filepath = Path::isRelative($filepath) ?
            Path::normalize(Path::join(getcwd() ?? '', $filepath))
            : $filepath;

        // Validasi file ada
        if (!file_exists($filepath)) {
            WP_CLI::error(
                sprintf('❌ File %s tidak ditemukan.', $io->textError($filepath))
            );
        }

        // Validasi file dapat dibaca
        if (!is_readable($filepath)) {
            WP_CLI::error(
                sprintf('❌ File %s tidak dapat dibaca.', $io->textError($filepath))
            );
        }

        $this->dryRun($filepath, $io);

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

            $successCount = 0;
            $skipCount = 0;
            $errorCount = 0;

            $io->newLine();
            $io->text("⏳ Memproses...");

            foreach ($postDtos as $index => $post) {
                $current = $index + 1;
                $title = $post->getTitle();
                $post_type = $post->getType();

                $io->text("[{$current}/{$total}] 📝 Processing: {$title}");

                // Check if post already exists
                $exists = false;

                // Coba dengan method yang ada di PostEntity
                if (method_exists(PostEntity::class, 'existsByName')) {
                    $exists = PostEntity::existsByName(sanitize_title($title));
                }

                if (!$exists && method_exists(PostEntity::class, 'existsByTitle')) {
                    $exists = PostEntity::existsByTitle($title);
                }

                // Fallback: cek dengan WP_Query
                if (!$exists) {
                    $query = new WP_Query([
                        'title' => $title,
                        'post_type' => $post_type,
                        'post_status' => 'any',
                        'posts_per_page' => 1
                    ]);
                    $exists = $query->have_posts();
                    wp_reset_postdata();
                }

                if ($exists) {
                    $io->warning("  ⏭️  Post '{$title}' sudah ada, dilewati.");
                    $skipCount++;
                    continue;
                }

                // Prepare post data
                $postData = $this->preparePostData($post, $io);

                if (empty($postData)) {
                    $io->warning("  ⚠️  Data post kosong, dilewati.");
                    $skipCount++;
                    continue;
                }

                // Create post
                $create = PostEntity::create($postData);

                if (is_wp_error($create)) {
                    $io->errorLog("  ❌ Gagal create '{$title}': " . $create->get_error_message());
                    $errorCount++;
                } else {
                    $io->success("  ✅ Berhasil create post ID {$create}");
                    $successCount++;
                }
            }

            // Calculate execution time
            $execution_time = number_format(microtime(true) - $start_time, 2);

            // Display summary
            $io->newLine(2);
            $io->title('📋 SUMMARY IMPORT');
            $io->hr();

            $summary = [
                ['Status', 'Count', 'Percentage'],
                ['✅ Berhasil', $successCount, $total > 0 ? round(($successCount / $total) * 100) . '%' : '0%'],
                ['⏭️  Dilewati', $skipCount, $total > 0 ? round(($skipCount / $total) * 100) . '%' : '0%'],
                ['❌ Gagal', $errorCount, $total > 0 ? round(($errorCount / $total) * 100) . '%' : '0%'],
                ['Total', $total, '100%'],
            ];

            $io->table(
                array_map(fn($row) => array_slice($row, 0, 2), array_slice($summary, 1, 3)),
                ['Status', 'Count']
            );

            $io->newLine();
            $io->definitionList([
                '⏱️  Waktu Eksekusi' => "{$execution_time} detik",
                '📁 File Source' => basename($filepath),
                '📅 Tanggal' => date('Y-m-d H:i:s')
            ]);

            if ($errorCount === 0 && $successCount > 0) {
                $io->newLine();
                $io->block('🎉 Import selesai dengan sukses! Semua data berhasil dimasukkan.', 'success');
            } elseif ($successCount > 0) {
                $io->newLine();
                $io->block("⚠️  Import selesai dengan {$errorCount} error.", 'warning');
            } else {
                $io->newLine();
                $io->block('ℹ️  Tidak ada data yang diimport.', 'info');
            }
        } catch (\Exception $e) {
            $io->errorWithIcon('❌ Error selama import: ' . $e->getMessage());
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
