<?php


$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,

        // Fix untuk operator assignment dan array
        'binary_operator_spaces' => [
            'default'   => 'single_space',
            'operators' => [
                '='  => 'single_space',
                '=>' => 'single_space',
                // '=' => 'align',   // HATI-HATI: 'align' bisa bikin aneh
                // '=>' => 'align',  // HATI-HATI: 'align' bisa bikin aneh
            ],
        ],

        // Spasi di sekitar konstruk
        'single_space_around_construct' => true,

        // Whitespace
        'no_whitespace_in_blank_line'         => true,
        'no_trailing_whitespace'              => true,
        'no_spaces_around_offset'             => true,
        'no_whitespace_before_comma_in_array' => true,

        // Array
        'array_syntax'      => ['syntax' => 'short'],
        'trim_array_spaces' => true,

        // **TAMBAHKAN RULES INI:**
        'no_multiple_statements_per_line' => true,
        'single_line_after_imports'       => true,
        'braces'                          => [
            'allow_single_line_closure' => true,
        ],

        // Membersihkan spasi berlebih
        'no_spaces_inside_parenthesis'      => true,
        'no_trailing_whitespace_in_comment' => true,
        'single_blank_line_at_eof'          => true,
    ])
    ->setFinder($finder);
