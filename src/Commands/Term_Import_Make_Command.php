<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Vigihdev\Support\Collection;
use Vigihdev\WpCliModels\DTOs\Fields\TermFieldDto;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\UI\Components\{ImportSummary, ProgressLog};
use WP_CLI\Utils;
use WP_CLI;

final class Term_Import_Make_Command extends Base_Import_Command
{

    private const ALLOW_EXTENSION = [
        'csv',
        'json'
    ];


    public function __construct()
    {
        parent::__construct(name: 'make:term-import');
    }


    /**
     * Import terms from CSV or JSON file.
     *
     * ## OPTIONS
     *
     * <file>
     * : Path to CSV or JSON file.
     * 
     * [--dry-run]
     * : Menjalankan perintah dalam mode simulasi tanpa membuat perubahan apa pun. 
     *  
     * ## EXAMPLES
     *
     *     wp make:term-import kota.csv
     *     wp make:term-import kota.json
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args)
    {
        $filepath = isset($args[0]) ? $args[0] : null;
        $io = new CliStyle();

        $this->validateFilePath($filepath, $io);
        $filepath = $this->normalizeFilePath($filepath);
        $this->validateFileJson($filepath, $io);

        // $this->executeCommand($filepath, $assoc_args, $io);
    }

    protected function executeCommand(string $filepath, array $assoc_args, CliStyle $io): void
    {
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run');
        try {
            $collection = $this->loadDataDto($filepath, $io, TermFieldDto::class);
            if ($dryRun) {
                $this->proccessDryRun($filepath, $collection, $io);
                return;
            }
            $this->proccessImport($filepath, $collection, $io);
        } catch (\Exception $e) {
            $io->errorWithIcon('❌ Error load data import: ' . $e->getMessage());
            WP_CLI::error($e);
        }
    }


    private function proccessDryRun(string $filepath, Collection $collection, CliStyle $io)
    {
        $io->title('🔍 DRY RUN - Preview Data Import');
        $io->note('Tidak ada perubahan ke database');

        // Display table
        $headers = ['No', 'Title', 'Type', 'Status', 'Author', 'Taxonomies'];
        $rows = [];

        foreach ($collection->getIterator() as $index => $term) {
            $rows[] = [];
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
        foreach ($collection->getIterator() as $term) {
            /** @var TermFieldDto $term */
        }
        $log->end();

        $summary->renderCompact($io, $filepath, (microtime(true) - $start_time));
    }
}
