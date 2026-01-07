<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\Page;

use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;
use Vigihdev\WpCliMake\Validators\CategoryValidator;
use Vigihdev\WpCliMake\Validators\PostFactoryValidator;
use Vigihdev\WpCliModels\Entities\PostEntity;
use Vigihdev\WpCliModels\Enums\PostStatus;
use Vigihdev\WpCliModels\Enums\PostType;
use WP_CLI\Utils;

final class Post_Page_Make_Command extends Base_Post_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:post-page');
    }


    /**
     * Create a new post page.
     * 
     * ## OPTIONS
     *
     * <title>
     * : The title of the post page.
     * 
     * --post_content=<post_content>
     * : The content of the post page.
     * 
     * [--post_name=<post_name>]
     * : The post name. Default is the sanitized post title when creating a new post.
     * 
     * [--post_status=<post_status>]
     * : Status of the post (default: publish)
     * 
     * [--post_author=<post_author>]
     * : The ID of the user who added the post. Default is the current user ID.
     * 
     * [--post_date=<post_date>]
     * : The date of the post. Default is the current time.
     * 
     * [--post_date_gmt=<post_date_gmt>]
     * : The date of the post in the GMT timezone. Default is the value of $post_date.
     * 
     * [--post_content_filtered=<post_content_filtered>]
     * : The filtered post content. Default empty.
     * 
     * [--post_title=<post_title>]
     * : The post title. Default empty.
     * 
     * [--post_excerpt=<post_excerpt>]
     * : The post excerpt. Default empty.
     * 
     * [--post_status=<post_status>]
     * : The post status. Default 'draft'.
     * 
     * [--post_type=<post_type>]
     * : The post type. Default 'post'.
     * 
     * [--comment_status=<comment_status>]
     * : Whether the post can accept comments. Accepts 'open' or 'closed'. Default is the value of 'default_comment_status' option.
     * 
     * [--ping_status=<ping_status>]
     * : Whether the post can accept pings. Accepts 'open' or 'closed'. Default is the value of 'default_ping_status' option.
     * 
     * [--post_password=<post_password>]
     * : The password to access the post. Default empty.
     * 
     * [--post_name=<post_name>]
     * : The post name. Default is the sanitized post title when creating a new post.
     * 
     * [--from-post=<post_id>]
     * : Post id of a post to be duplicated.
     * 
     * [--to_ping=<to_ping>]
     * : Space or carriage return-separated list of URLs to ping. Default empty.
     * 
     * [--pinged=<pinged>]
     * : Space or carriage return-separated list of URLs that have been pinged. Default empty.
     * 
     * [--post_modified=<post_modified>]
     * : The date when the post was last modified. Default is the current time.
     * 
     * [--post_modified_gmt=<post_modified_gmt>]
     * : The date when the post was last modified in the GMT timezone. Default is the current time.
     * 
     * [--post_parent=<post_parent>]
     * : Set this for the post it belongs to, if any. Default 0.
     * 
     * [--menu_order=<menu_order>]
     * : The order the post should be displayed in. Default 0.
     * 
     * [--post_mime_type=<post_mime_type>]
     * : The mime type of the post. Default empty.
     * 
     * [--guid=<guid>]
     * : Global Unique ID for referencing the post. Default empty.
     * 
     * [--post_category=<post_category>]
     * : Array of category names, slugs, or IDs. Defaults to value of the 'default_category' option.
     * 
     * [--tags_input=<tags_input>]
     * : Array of tag names, slugs, or IDs. Default empty.
     * 
     * [--tax_input=<tax_input>]
     * : Array of taxonomy terms keyed by their taxonomy name. Default empty.
     * 
     * [--meta_input=<meta_input>]
     * : Array in JSON format of post meta values keyed by their post meta key. Default empty.
     * 
     * [--dry-run]
     * : Preview data insert post without actual execution
     * 
     * ## EXAMPLES
     *
     *     # Create a new post page with the title "My Post Page".
     *     $ wp make:post-page "My Post Page"
     * 
     *     # Create a new post page with the title "My Post Page" and content "Hello World!".
     *     $ wp make:post-page "My Post Page" --post_content="Hello World!"
     * 
     * @when after_wp_load
     * 
     * @param array $args Argumen posisi
     * @param array $assoc_args Argumen opsional
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {

        parent::__invoke($args, $assoc_args);
        $this->title = $args[0];
        $this->post_content = Utils\get_flag_value($assoc_args, 'post_content');
        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        $io = $this->io;
        try {

            // Process Data
            $this->setPostContent();

            $this->postData = array_merge($this->postData, $this->mapPostData(), $assoc_args, [
                'post_content' => $this->post_content,
            ]);

            PostFactoryValidator::validate($this->postData)->validateCreate();
            array_map(fn($value) => CategoryValidator::validate($value)->mustExist(), $this->post_category);

            if ($dryRun) {
                $this->dryRun();
                return;
            }

            $this->process($assoc_args);
        } catch (\Throwable $e) {
            $this->exceptionHandler->handle($e);
        }
    }

    private function dryRun(): void
    {
        $io = $this->io;

        $io->newLine();
        $io->title("🔍 DRY RUN - Preview Data Insert Post Page");
        $io->note('Tidak ada perubahan ke database');

        $io->newLine();
        $io->definitionList("Detail Post Page", [
            'Title' => $this->title,
            'Status' => $this->postData['post_status'] ?? 'N/A',
            'Type' => $this->postData['post_type'] ?? 'N/A',
            'Author' => $this->postData['post_author'] ?? 'N/A',
        ]);

        $io->success('Dry run selesai!');
        $io->infoBlock('Gunakan tanpa --dry-run untuk eksekusi sebenarnya.');
        $io->newLine();
    }

    /**
     * Process the post creation.
     *
     * @return void
     */
    private function process(): void
    {
        $io = $this->io;

        $insert = PostEntity::create($this->postData);
        if (is_wp_error($insert)) {
            $io->errorBlock(sprintf("Failed created post page with title: %s : %s", $this->title, $insert->get_error_message()));
        } else {
            $io->successBlock(sprintf("Success created post page with ID: %d and title: %s", $insert, $this->title));
        }
    }

    private function mapPostData(): array
    {
        $postDefault = $this->loadDefaultPost($this->title);
        $postData = array_merge(
            $postDefault->toArray(),
            [
                'post_author' => $this->author,
                'post_status'  => PostStatus::PUBLISH->value,
                'post_type'    => PostType::PAGE->value,
            ]
        );
        return $postData;
    }
}
