<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Factory;

use WP_Term;

final class MenuFactory
{

    /**
     *
     * @param array $args
     * @return WP_Term[]
     */
    public static function wp_get_nav_menus(array $args = []): array
    {

        $defaults = array(
            'taxonomy'   => 'nav_menu',
            'hide_empty' => false,
            'orderby'    => 'name',
        );

        $args = wp_parse_args($args, $defaults);
        return apply_filters('wp_get_nav_menus', get_terms($args), $args);
    }



    /**
     *
     * @param int|string|\WP_Term $menu Menu ID, slug, name, or object. $current_menu
     * @return array|false Array of menu items, otherwise false.
     */
    public static function wp_get_nav_menu_items(int|string|WP_Term $menu = 'primary', array $args = []): array
    {
        return wp_get_nav_menu_items($menu, $args);
    }
}
