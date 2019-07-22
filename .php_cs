<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2'                           => true,
        '@PHP71Migration'                 => true,
        'ordered_class_elements'          => [
            'order'                       => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public',
                'method_protected',
                'method_private',
            ],
            'sortAlgorithm' => 'none',
        ],
        'array_syntax'                    => [
            'syntax' => 'short',
        ],
        'cast_spaces'                     => true,
        'concat_space'                    => [
            'spacing' => 'one',
        ],
        'no_unused_imports'               => true,
        'ordered_imports'                 => true,
        'phpdoc_align'                    => true,
        'phpdoc_single_line_var_spacing'  => true,
        'return_type_declaration'         => [
            'space_before' => 'none',
        ],
        'self_accessor'                   => true,
        'single_quote'                    => true,
        'short_scalar_cast'               => true,
        'standardize_not_equals'          => true,
        'trailing_comma_in_multiline_array' => true,
        'trim_array_spaces'               => true,
        'whitespace_after_comma_in_array' => true,
        'declare_strict_types'            => true,
    ])
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRiskyAllowed(true);
