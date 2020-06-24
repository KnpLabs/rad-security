<?php

$finder = PhpCsFixer\Finder::create()
    ->in('spec/')
    ->in('src/')
;

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules([
        'binary_operator_spaces' => [],
        'concat_space' => [],
        'header_comment' => [],
        'not_operator_with_space' => true,
        'array_syntax' => [],
        'linebreak_after_opening_tag' => true,
        'ordered_imports' => [],
        'phpdoc_order' => true,
        'PedroTroller/phpspec' => true,
        'PedroTroller/single_line_comment' => [],
    ])
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
;
