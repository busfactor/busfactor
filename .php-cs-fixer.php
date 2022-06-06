<?php

use PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new Config())
    ->setRules(
        [
            '@PSR12' => true,
            '@PHP80Migration' => true,
            '@PHP80Migration:risky' => true,
            'ordered_class_elements' => [
                'order' => [
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
                'sort_algorithm' => 'none',
            ],
            'array_syntax' => [
                'syntax' => 'short',
            ],
            'cast_spaces' => true,
            'concat_space' => [
                'spacing' => 'one',
            ],
            'no_unused_imports' => true,
            'phpdoc_align' => true,
            'phpdoc_single_line_var_spacing' => true,
            'self_accessor' => true,
            'single_quote' => true,
            'standardize_not_equals' => true,
            'trim_array_spaces' => true,
            'whitespace_after_comma_in_array' => true,
            'declare_strict_types' => true,
        ]
    )
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRiskyAllowed(true);
