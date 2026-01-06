<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\Page;

use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;
use Vigihdev\WpCliModels\DTOs\Fields\DefaultPostFieldDto;
use Vigihdev\WpCliModels\Entities\UserEntity;
use Vigihdev\WpCliModels\Enums\PostStatus;
use Vigihdev\WpCliModels\Enums\PostType;
use Vigihdev\WpCliModels\Validators\PostCreationValidator;
use WP_CLI\Utils;

final class Post_Page_Make_Command extends Base_Post_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:post-page');
    }


    /**
     * Membuat post halaman baru di WordPress.
     *
     * ## OPTIONS
     *
     * <title>
     * : Judul post yang akan dibuat
     * required: true
     * 
     * [--post_author=<post_author>]
     * : ID pengguna yang akan menjadi author post (default: 1)
     * default: 1
     * 
     * [--post_date=<post_date>] 
     * : Waktu pembuatan post dalam format MySQL (default: sekarang)
     * default: ''
     * 
     * [--post_date_gmt=<post_date_gmt>] 
     * : Waktu pembuatan post dalam format GMT (default: sekarang)
     * default: ''
     * 
     * [--post_content=<post_content>]
     * : Isi konten post (default: "")
     * required: true
     * 
     * [--post_content_filtered=<post_content_filtered>]
     * : Isi konten post yang difilter (default: "")
     * default: ''
     * 
     * [--post_excerpt=<post_excerpt>]
     * : Ringkasan post (default: "")
     * default: ''
     * 
     * [--post_status=<post_status>]
     * : Status post (default: "publish")
     * default: publish
     * options: 
     *  - publish
     *  - draft
     *  - pending
     *  - private
     * 
     * [--post_type=<post_type>] 
     * : Tipe post (default: "post")
     * default: post
     * 
     * [--comment_status=<comment_status>] 
     * : Status komentar (default: "open")
     * default: open
     * 
     * [--ping_status=<ping_status>] 
     * : Status pingback (default: "open")
     * default: open
     * 
     * [--post_password=<post_password>] 
     * : Password post (default: "")
     * default: ''
     * 
     * [--post_name=<post_name>] 
     * : Slug post (default: "")
     * default: ''
     * 
     * [--from-post=<post_id>]
     * : ID post yang akan dicloning (default: 0)
     * default: 0
     * 
     * [--to_ping=<to_ping>] 
     * : Daftar URL yang akan diping (default: "")
     * default: ''
     * 
     * [--pinged=<pinged>] 
     * : Daftar URL yang sudah diping (default: "")
     * default: ''
     * 
     * [--post_modified=<post_modified>]
     * : Waktu modifikasi post dalam format MySQL (default: sekarang)
     * default: ''
     * 
     * [--post_modified_gmt=<post_modified_gmt>] 
     * : Waktu modifikasi post dalam format GMT (default: sekarang)
     * default: ''
     * 
     * [--post_modified_gmt=<post_modified_gmt>]
     * : Waktu modifikasi post dalam format GMT (default: sekarang)
     * default: ''
     * 
     * [--post_parent=<post_parent>]
     * : ID post parent (default: 0)
     * default: 0
     * 
     * [--menu_order=<menu_order>] 
     * : Urutan menu (default: 0)
     * default: 0
     * 
     * [--post_mime_type=<post_mime_type>] 
     * : Tipe MIME post (default: "")
     * default: ''
     * 
     * [--guid=<guid>] 
     * : GUID post (default: "")
     * default: ''
     * 
     * [--post_category=<post_category>] 
     * : Daftar kategori post (default: "")
     * default: ''
     * 
     * [--tags_input=<tags_input>] 
     * : Daftar tag post (default: "")
     * default: ''
     * 
     * [--tax_input=<tax_input>] 
     * : Daftar taxonomi post (default: "")
     * default: ''
     * 
     * [--meta_input=<meta_input>] 
     * : Daftar meta post (default: "")
     * default: ''
     * 
     * [--<field>=<value>] 
     * : Field tambahan post (default: "")
     * default: ''
     * 
     * [--edit] 
     * : Apakah post akan diedit setelah dibuat (default: false)
     * default: false
     * 
     * [--porcelain] 
     * : Output dalam format yang dapat diproses oleh mesin (default: false)
     * default: false
     * 
     * [--dry-run]
     * : Melakukan simulasi tanpa benar-benar membuat post
     * default: false
     * 
     * ## EXAMPLES
     *
     *     # Membuat post draft
     *     $ wp make:post "Judul Post"
     *
     *     # Membuat halaman publik
     *     $ wp make:post "Halaman About" --type=page --status=publish
     *
     *     # Membuat post dengan konten dan author tertentu
     *     $ wp make:post "Post dengan Konten" --content="Ini isi post" --author=2
     *
     * @param array $args Argumen posisi
     * @param array $assoc_args Argumen opsional
     * @return void
     */
    public function __invoke(array $args, array $assoc_args): void
    {
        parent::__invoke($args, $assoc_args);
        $this->title = $args[0];

        $dryRun = Utils\get_flag_value($assoc_args, 'dry-run', false);

        $io = $this->io;
        try {

            $this->postData = [
                'post_title' => $this->title,
                'post_type' => PostType::PAGE->value,
                'post_status' => PostStatus::PUBLISH->value,
                'post_author' => $this->author,
                'meta_input' => [
                    '_wp_page_template' => 'parts/daftar-harga.php',
                    '_wp_page_query' => 'luar_kota',

                ],
                'post_content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras congue enim ut rutrum euismod. Morbi enim lorem, aliquet id bibendum non, luctus quis mauris. Donec volutpat venenatis elit, ornare malesuada augue dignissim id. Phasellus quis gravida turpis. Curabitur fringilla augue a convallis laoreet. In facilisis, magna sit amet bibendum interdum, sem sem scelerisque sapien, vestibulum bibendum erat odio at elit. Aenean quis pretium ex. Praesent iaculis ligula ac tincidunt rhoncus. Quisque risus elit, posuere id elementum vel, dictum quis orci. Vivamus iaculis metus dui, quis tincidunt sem fringilla nec. Aenean efficitur blandit fermentum. Nunc vitae mi vitae lorem viverra efficitur.

Phasellus consectetur imperdiet lacus at vulputate. Suspendisse dignissim placerat odio, nec dictum orci vestibulum vel. Etiam a dui quis sem rhoncus dignissim. Curabitur ullamcorper fringilla lectus, quis pulvinar ante viverra vitae. Aliquam erat volutpat. Fusce non ornare turpis. Ut ornare tellus vel dolor mattis faucibus. Morbi ut orci eget nunc auctor sagittis nec sed purus. Aliquam erat volutpat. Integer id aliquet magna. Fusce tempor ipsum id libero varius, sollicitudin laoreet metus faucibus. Duis molestie nisi sed facilisis interdum. Aliquam erat volutpat. Donec sit amet tempus erat. Nam id ligula nec quam vulputate sodales vitae ut tellus.

Cras efficitur diam ut vulputate accumsan. Cras lacinia massa consequat nulla pellentesque volutpat nec quis odio. Integer et commodo sapien. Vestibulum fermentum cursus pulvinar. Vivamus enim lacus, feugiat non aliquet in, fermentum id dui. Vestibulum ac pellentesque sem. In ut feugiat turpis. Maecenas elementum massa quis dolor efficitur, in tincidunt ex eleifend. Etiam feugiat eleifend sapien, eget vestibulum urna sagittis ut.

Nunc malesuada porta tincidunt. Donec semper, mi non hendrerit malesuada, tellus quam auctor augue, id euismod ipsum risus quis metus. Sed nec nisi id neque ullamcorper sollicitudin. Integer venenatis, mauris at tincidunt ullamcorper, lacus ligula ultrices sapien, a eleifend massa nisi eget mauris. Pellentesque non arcu a justo mattis lobortis at id sapien. Aenean a diam sit amet dolor eleifend congue. Aenean ut hendrerit ipsum. Donec placerat sapien eget quam condimentum volutpat. Ut eu pellentesque leo, et accumsan nisi. Fusce sodales egestas urna et tempus. Duis tempus ornare leo nec pellentesque. Nulla interdum, mauris vel pulvinar facilisis, tortor lectus accumsan turpis, quis accumsan ante tortor nec ligula.

Aenean vehicula, purus sed cursus sodales, diam est consequat arcu, ut dictum sapien ligula non purus. Etiam sit amet odio ullamcorper, pellentesque dui eget, vestibulum lacus. Sed bibendum quam id hendrerit egestas. In felis lorem, suscipit quis lectus id, imperdiet consequat magna. Integer risus nisl, vulputate sed gravida ultricies, pharetra a velit. Cras consequat eget felis tincidunt gravida. Duis id congue nisl. Ut quis ante pulvinar, efficitur erat id, bibendum eros. Nam cursus varius neque vel porttitor. Proin vel semper nisi, eget mattis est.",
            ];

            $defaultPost = new DefaultPostFieldDto(title: $this->title);
            $this->postData = array_merge($this->postData, $defaultPost->toArray(), $assoc_args);

            PostCreationValidator::validate($this->postData)
                ->mustHaveUniqueTitle($this->title)
                ->mustHaveUniqueName(sanitize_title($this->title))
                ->mustBeValidAuthor($this->author)
                ->mustBeCreatable();

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
        $io->success(sprintf('Dry run: %s', $this->title));
    }
    /**
     * Process the post creation.
     *
     * @return void
     */

    private function process(): void
    {
        $io = $this->io;

        $insert = wp_insert_post($this->postData);
        if (is_wp_error($insert) && $insert instanceof \WP_Error) {
            $io->errorBlock($insert->get_error_message());
        } else {
            $io->successBlock(sprintf("Post created with ID: %d", $insert));
        }
    }
}
