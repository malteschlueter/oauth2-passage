<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__,
        dirname(__DIR__, 3) . '/src',
        dirname(__DIR__, 3) . '/tests',
    ])
;

return (new Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setCacheFile(dirname(__DIR__) . '/cache/.php-cs-fixer.dist.cache')
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP82Migration' => true,
        '@PHP80Migration:risky' => true,
        '@PHPUnit84Migration:risky' => true,

        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'method_chaining_indentation' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'new_line_for_chained_calls'],
        'no_useless_else' => true,
        'no_useless_return' => true,
        'self_static_accessor' => true,
        'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        'php_unit_method_casing' => ['case' => 'snake_case'],
    ])
;
