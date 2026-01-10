<?php


$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,

        // 🔥 ARRAY ALIGNMENT
        'binary_operator_spaces' => [
            'default'   => 'single_space',
            'operators' => [
                '=>' => 'align_single_space',
                '='  => 'align_single_space',
            ],
        ],

        // array pendek & rapi
        'array_syntax'      => ['syntax' => 'short'],
        'trim_array_spaces' => true,
    ])
    ->setFinder($finder);
