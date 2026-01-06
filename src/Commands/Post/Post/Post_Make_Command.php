<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Commands\Post\Post;

use Vigihdev\WpCliMake\Commands\Post\Base_Post_Command;

final class Post_Make_Command extends Base_Post_Command
{

    public function __construct()
    {
        parent::__construct(name: 'make:post');
    }


    /**
     * Membuat post baru di WordPress.
     *
     * ## OPTIONS
     *
     * <title>
     * : Judul post yang akan dibuat
     * required: true
     * ---
     * 
     * [--post_author=<post_author>]
     * : ID pengguna yang akan menjadi author post (default: 1)
     * default: 1
     * ---
     * 
     * [--post_date=<post_date>] 
     * : Waktu pembuatan post dalam format MySQL (default: sekarang)
     * default: ''
     * ---
     * 
     * [--post_date_gmt=<post_date_gmt>] 
     * : Waktu pembuatan post dalam format GMT (default: sekarang)
     * default: ''
     * ---
     * 
     * [--post_content=<post_content>]
     * : Isi konten post (default: "")
     * default: ''
     * ---
     * 
     * [--post_content_filtered=<post_content_filtered>]
     * : Isi konten post yang difilter (default: "")
     * default: ''
     * ---
     * 
     * [--post_excerpt=<post_excerpt>]
     * : Ringkasan post (default: "")
     * default: ''
     * ---
     * 
     * [--post_status=<post_status>]
     * : Status post (default: "publish")
     * default: publish
     * ---
     * options: 
     *  - publish
     *  - draft
     *  - pending
     *  - private
     * ---
     * 
     * [--post_type=<post_type>] 
     * : Tipe post (default: "post")
     * default: post
     * ---
     * 
     * [--comment_status=<comment_status>] 
     * : Status komentar (default: "open")
     * default: open
     * ---
     * 
     * [--ping_status=<ping_status>] 
     * : Status pingback (default: "open")
     * default: open
     * ---
     * 
     * [--post_password=<post_password>] 
     * : Password post (default: "")
     * default: ''
     * ---
     * 
     * [--post_name=<post_name>] 
     * : Slug post (default: "")
     * default: ''
     * ---
     * 
     * [--from-post=<post_id>]
     * : ID post yang akan dicloning (default: 0)
     * ---
     * default: 0
     * ---
     * 
     * [--to_ping=<to_ping>] 
     * : Daftar URL yang akan diping (default: "")
     * default: ''
     * ---
     * 
     * [--pinged=<pinged>] 
     * : Daftar URL yang sudah diping (default: "")
     * default: ''
     * ---
     * 
     * [--post_modified=<post_modified>]
     * : Waktu modifikasi post dalam format MySQL (default: sekarang)
     * default: ''
     * ---
     * 
     * [--post_modified_gmt=<post_modified_gmt>] 
     * : Waktu modifikasi post dalam format GMT (default: sekarang)
     * default: ''
     * ---
     * 
     * [--post_modified_gmt=<post_modified_gmt>]
     * : Waktu modifikasi post dalam format GMT (default: sekarang)
     * default: ''
     * ---
     * 
     * [--post_parent=<post_parent>]
     * : ID post parent (default: 0)
     * default: 0
     * ---
     * 
     * [--menu_order=<menu_order>] 
     * : Urutan menu (default: 0)
     * default: 0
     * ---
     * 
     * [--post_mime_type=<post_mime_type>] 
     * : Tipe MIME post (default: "")
     * default: ''
     * ---
     * 
     * [--guid=<guid>] 
     * : GUID post (default: "")
     * default: ''
     * ---
     * 
     * [--post_category=<post_category>] 
     * : Daftar kategori post (default: "")
     * default: ''
     * ---
     * 
     * [--tags_input=<tags_input>] 
     * : Daftar tag post (default: "")
     * default: ''
     * ---
     * 
     * [--tax_input=<tax_input>] 
     * : Daftar taxonomi post (default: "")
     * default: ''
     * ---
     * 
     * [--meta_input=<meta_input>] 
     * : Daftar meta post (default: "")
     * default: ''
     * ---
     * 
     * [--<field>=<value>] 
     * : Field tambahan post (default: "")
     * default: ''
     * ---
     * 
     * [--edit] 
     * : Apakah post akan diedit setelah dibuat (default: false)
     * default: false
     * ---
     * 
     * [--porcelain] 
     * : Output dalam format yang dapat diproses oleh mesin (default: false)
     * default: false
     * ---
     * 
     * [--dry-run]
     * : Melakukan simulasi tanpa benar-benar membuat post
     * default: false
     * ---
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
    public function __invoke(array $args, array $assoc_args): void {}
}
