<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\PostType;

use Throwable;
use WP_CLI\Utils;
use Vigihdev\Support\Collection;
use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;
use Vigihdev\WpCliMake\DTOs\PostDto;
use Vigihdev\WpCliMake\Validators\PostFactoryValidator;
use Vigihdev\WpCliMake\Validators\PostTypeValidator;
use Vigihdev\WpCliMake\Validators\TaxonomyValidator;
use Vigihdev\WpCliModels\Entities\PostEntity;
use Vigihdev\WpCliModels\Enums\PostStatus;

final class Post_Type_Make_Command extends Base_Post_Command
{


    public function __construct()
    {
        parent::__construct(name: 'make:post-type');
    }

    /**
     * Create a new post type with the given title and taxonomies
     *
     * ## OPTIONS
     * 
     * <title>
     * : The title of the post type to create
     * 
     * --post_type=<post_type>
     * : The post type to create
     * 
     * --post_content=<post_content>
     * : The content of the post type to create
     * 
     * --tax_input=<tax_input>
     * : Array of taxonomy terms keyed by their taxonomy name. Default empty.
     * 
     * [--dry-run]
     * : Run the command in dry-run mode to preview the data that would be inserted.
     * 
     * ## EXAMPLES
     *  
     *     # Create a new post type
     *     wp make:post-type event --post_type=event --post_content="Event Content" --tax_input="{'category':['event']}" --dry-run
     *     
     *     # Create a new post type with custom taxonomies
     *     wp make:post-type event --post_type=event --post_content="Event Content" --tax_input="{'category':['event'],'post_tag':['concert']}" --dry-run
     *     
     * @param array $args array index
     * @param array $assoc_args array of associative arguments
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        parent::__invoke($args, $assoc_args);
        $this->title = $args[0];
        $this->post_content = Utils\get_flag_value($assoc_args, 'post_content');
        $postType = Utils\get_flag_value($assoc_args, 'post_type');
        $taxInput = Utils\get_flag_value($assoc_args, 'tax_input');
        $taxInput = json_decode($taxInput, true) ?? [];
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        try {

            // Process Data
            $this->setPostContent();
            $postDto = new PostDto(
                title: $this->title,
                content: $this->post_content,
                type: $postType,
                taxInput: $taxInput,
            );

            $this->postData = array_merge($this->postData, $this->mapPostData(), $assoc_args, [
                'post_content' => $this->post_content,
                'post_type'    => $postType,
            ]);
            PostTypeValidator::validate($postDto)
                ->mustHaveRegisteredPostType()
                ->mustHaveRegisteredTaxonomies()
                ->mustAllowTaxonomiesForPostType()
                ->mustHaveExistingTerms();
            PostFactoryValidator::validate($this->postData)->validateCreate();

            if ($dryRun) {
                $this->dryRun();
                return;
            }
            $this->process();
        } catch (Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }

    private function dryRun(): void
    {

        $io = $this->io;

        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Post Type");
        $io->note('Tidak ada perubahan ke database');

        $io->newLine();
        $io->definitionList(
            'Detail Post Type',
            [
                'Title' => $this->title,
                'Post Type' => $this->postData['post_type'],
                'Tax Input' => 'category[event]',
                // 'Tax Input' => json_encode($this->postData['tax_input'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            ]
        );

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }

    private function process(): void
    {

        $io = $this->io;

        // Task
        $io->newLine();
        $io->section("Start Insert Post Type: {$this->title}");
        $insert = PostEntity::create($this->postData);
        if (is_wp_error($insert)) {
            $io->errorBlock(sprintf("Post Type %s failed to create: %s", $this->title, $insert->get_error_message()));
            return;
        }
        $io->successBlock(sprintf("Post Type %s created successfully with ID: %d", $this->title, $insert));
        $io->newLine();
    }

    private function mapPostData(): array
    {
        $postDefault = $this->loadDefaultPost($this->title);
        $postData = array_merge(
            $postDefault->toArray(),
            [
                'post_author' => $this->author,
                'post_status'  => PostStatus::PUBLISH->value,
            ]
        );
        return $postData;
    }
}
