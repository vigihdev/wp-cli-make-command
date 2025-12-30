<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands;

use Throwable;
use Vigihdev\WpCliModels\Entities\TermEntity;
use WP_CLI\Utils;
use Vigihdev\WpCliModels\UI\CliStyle;
use Vigihdev\WpCliModels\Validators\TaxonomyValidator;
use Vigihdev\WpCliModels\Validators\TermValidator;

final class Term_Make_Command extends Base_Command
{

    private ?string $term = null;

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
        $this->term = $args[0] ?? null;
        $taxonomy = Utils\get_flag_value($assoc_args, 'taxonomy');
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        $io = new CliStyle();
        try {

            TaxonomyValidator::validate($taxonomy)
                ->mustExist($taxonomy);

            TermValidator::validate($this->term, $taxonomy)
                ->mustHaveUniqueName($this->term);

            if ($dryRun) {
                $this->processDryRun($io, $assoc_args);
                return;
            }

            $this->process($io, $assoc_args);
        } catch (Throwable $e) {
            $this->exceptionHandler->handle($io, $e);
        }
    }


    private function processDryRun(CliStyle $io, array $assoc_args)
    {
        $assoc_args = array_filter($assoc_args, function ($key) {
            return !in_array($key, ['dry-run'], true);
        }, ARRAY_FILTER_USE_KEY);

        $termData = [];
        $termData = array_merge([
            'name' => $this->term,
            'slug' => sanitize_title($this->term),
        ], $assoc_args);

        $dryRun = $io->renderDryRunPreset("New Term");
        $dryRun
            ->addDefinition($termData)
            ->addInfo("1 Term akan dibuat")
            ->render();
    }

    private function process(CliStyle $io, array $assoc_args)
    {

        $termData = array_merge([
            'slug' => sanitize_title($this->term),
        ], $assoc_args);

        $insert = TermEntity::create($this->term, $assoc_args['taxonomy'], $termData);

        if (is_wp_error($insert)) {
            $io->renderBlock("Error insert term: " . $insert->get_error_message())->error();
            return;
        }

        $io->renderBlock(
            sprintf("Term created successfully with term_id: %s and name: %s", (string) $insert['term_id'], $this->term)
        )->success();
    }
}
