<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Term;


use Vigihdev\Support\Collection;
use Vigihdev\WpCliMake\Commands\Term\Base_Term_Command;
use Vigihdev\WpCliMake\DTOs\TermDto;
use Vigihdev\WpCliMake\Support\DtoJsonTransformer;
use Vigihdev\WpCliMake\Validators\TermValidator;
use Vigihdev\WpCliModels\Entities\TermEntity;
use WP_CLI\Utils;

final class Term_Import_Make_Command extends Base_Term_Command
{


    /**
     * @var Collection<TermDto> $collection
     */
    private ?Collection $collection = null;

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
    public function __invoke(array $args, array $assoc_args): void
    {
        parent::__invoke($args, $assoc_args);
        $this->filepath = $args[0];
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        $io = $this->io;
        try {

            $this->normalizeFilePath();
            $this->validateFilepathJson();
            $this->collection = DtoJsonTransformer::fromFile($this->filepath, TermDto::class);
            if ($dryRun) {
                $this->dryRun();
                return;
            }

            $this->process();
        } catch (\Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }

    private function dryRun(): void
    {

        $io = $this->io;
        $collection = $this->collection;

        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Term");
        $io->note('Tidak ada perubahan ke database');

        $io->write(['<fg=cyan>Source File:</>', "<fg=yellow>{$this->filepath}</>"]);
        $io->write(['<fg=cyan>Total Items Term:</>', "<fg=yellow>{$collection->count()}</>"]);

        $io->newLine();
        $io->table(
            headers: ['Term', 'Taxonomy', 'Description'],
            rows: $collection->map(function (TermDto $term) {
                return [
                    $term->getTerm(),
                    $term->getTaxonomy(),
                    $term->getDescription() ?? 'N/A',
                ];
            })->toArray(),
        );

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }

    private function process(): void
    {

        $io = $this->io;
        $collection = $this->collection;
        $importIo = $this->importIo;

        // Task
        $io->newLine();
        $io->section("Start Insert Term: {$collection->count()} items");

        foreach ($collection->getIterator() as $index => $term) {

            $importIo->start($term->getTerm());

            usleep(2000000);
            try {
                TermValidator::validate($term)
                    ->validateCreate();

                $insert = TermEntity::create($term->getTerm(), $term->getTaxonomy(), $term->toArray());
                if (is_wp_error($insert)) {
                    $importIo->failed(
                        sprintf("%s : %s", $term->getTerm(), $insert->get_error_message())
                    );
                    continue;
                }

                $importIo->success(sprintf("Insert term: %s : term_id %s", $term->getTerm(), $insert['term_id']));
            } catch (\Throwable $e) {
                if ($e->getCode() === 409) {
                    $importIo->skipped(sprintf("%s", $e->getMessage()));
                } else {
                    $importIo->skipped(sprintf("%s", $e->getMessage()));
                }
            }
        }
    }
}
