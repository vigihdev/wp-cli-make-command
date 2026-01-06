<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Term;

use WP_CLI\Utils;

final class Term_Make_Command extends Base_Term_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:term');
    }

    /**
     * Creates a taxonomy term
     *
     * ## OPTIONS
     *
     * <term>
     * : Term name(s) to create (space-separated for bulk)
     *
     * --taxonomy=<taxonomy>
     * : Taxonomy slug (e.g., category)
     * required: true
     *
     * [--slug=<slug>]
     * : Custom slug (only for single term)
     *
     * [--description=<description>]
     * : Term description (only for single term)
     *
     * [--parent=<parent>]
     * : Parent term slug/ID (only for single term)
     *
     * [--dry-run]
     * : Preview data without creating
     * default: false
     * ---
     * 
     * ## EXAMPLES
     *
     *     # Single term
     *     wp make:term Jakarta --taxonomy=category
     *
     *     # With custom slug
     *     wp make:term "Jakarta Pusat" --taxonomy=category --slug=jakarta-pusat
     *
     * @when after_wp_load
     */
    public function __invoke($args, $assoc_args)
    {
        parent::__invoke($args, $assoc_args);
        $this->term = $args[0] ?? null;
        $this->taxonomy = Utils\get_flag_value($assoc_args, 'taxonomy');
        $this->slug = Utils\get_flag_value($assoc_args, 'slug');
        $this->description = Utils\get_flag_value($assoc_args, 'description');
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);
        try {

            // $this->collection = DtoJsonTransformer::fromFile($this->filepath, PostDto::class);
            if ($dryRun) {
                $this->dryRun();
                return;
            }

            $this->process();
        } catch (\Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }

    private function dryRun()
    {

        $io = $this->io;

        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Post Type");
        $io->note('Tidak ada perubahan ke database');

        // $io->write(['<fg=cyan>Source File:</>', "<fg=yellow>{$this->filepath}</>"]);
        // $io->write(['<fg=cyan>Total Items Post:</>', "<fg=yellow>{$collection->count()}</>"]);
        $io->newLine();

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
    }

    private function process()
    {
        $io = $this->io;
        $io->newLine();
        $io->title("🔧 Process - Insert Post Type");
        $io->note('Membuat post type di database');
    }
}
