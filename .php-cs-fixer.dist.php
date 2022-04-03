<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . DIRECTORY_SEPARATOR . 'app',
        __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap',
        __DIR__ . DIRECTORY_SEPARATOR . 'config',
        __DIR__ . DIRECTORY_SEPARATOR . 'database',
        __DIR__ . DIRECTORY_SEPARATOR . 'lang',
        __DIR__ . DIRECTORY_SEPARATOR . 'resources',
        __DIR__ . DIRECTORY_SEPARATOR . 'routes',
        __DIR__ . DIRECTORY_SEPARATOR . 'tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->append(['.php-cs-fixer.dist.php']);

$rules = [
    '@PSR12' => true,
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'no_unused_imports' => true,
    'not_operator_with_successor_space' => true,
    'trailing_comma_in_multiline' => true,
    'phpdoc_scalar' => true,
    'unary_operator_spaces' => true,
    'binary_operator_spaces' => true,
//    'concat_space' => ['spacing' => 'one'],
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_var_without_name' => true,
    'class_attributes_separation' => [
        'elements' => [
            'method' => 'one',
        ],
    ],
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
    'single_trait_insert_per_statement' => true,
];

return (new PhpCsFixer\Config())
    ->setRules($rules)
    ->setFinder($finder);