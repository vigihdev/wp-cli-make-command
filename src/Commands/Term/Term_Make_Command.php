<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Term;

use Vigihdev\WpCliMake\Validators\TermValidator;
use Vigihdev\WpCliModels\Entities\TermEntity;
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
        $this->term = $args[0];
        $this->taxonomy = Utils\get_flag_value($assoc_args, 'taxonomy');
        $this->slug = Utils\get_flag_value($assoc_args, 'slug');
        $this->description = Utils\get_flag_value($assoc_args, 'description');
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        try {
            $this->setSlug();

            TermValidator::validate($this->getTermDto())
                ->validateCreate();
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
        $io->title("🔍 DRY RUN - Preview Data Term");
        $io->note('Tidak ada perubahan ke database');

        $io->newLine();
        $io->definitionList(
            'Term Detail',
            [
                'Term' => $this->term ?? 'N/A',
                'Taxonomy' => $this->taxonomy ?? 'N/A',
                'Slug' => $this->slug ?? 'N/A',
                'Description' => $this->description ?? 'N/A',
            ]
        );

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }

    /**
     * Process the term creation
     *
     * @return void
     */
    private function process()
    {
        $io = $this->io;
        $io->newLine();
        $io->section("🔧 Process - Insert Term");

        $term = $this->getTermDto();
        $insert = TermEntity::create($term->getTerm(), $term->getTaxonomy(), $term->toArray());

        if (is_wp_error($insert)) {
            $io->errorBlock("Error insert term: " . $insert->get_error_message());
            return;
        }

        $io->successBlock(
            sprintf("Term created successfully with term_id: %s and name: %s", (string) $insert['term_id'], $term->getTerm())
        );
        $io->newLine();
    }
}
