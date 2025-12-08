<?php

declare(strict_types=1);

namespace Vigihdev\WpCliMake\Factory;

use WP_Term;

final class TermFactory
{

    /**
     *
     * @param array $args
     * @param string $deprecated
     * @return WP_Term[]
     */
    public static function get_terms(
        array $args = [
            'hide_empty' => false,
            'taxonomy' => 'category'
        ],
        $deprecated = ''
    ): array {

        return get_terms($args, $deprecated);
    }
}
