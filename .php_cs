<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in('spec/')
    ->in('src/')
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        'align_double_arrow',
        'align_equals',
        'concat_with_spaces',
        'header_comment',
        'logical_not_operators_with_spaces',
        'long_array_syntax',
        'newline_after_open_tag',
        'ordered_use',
        'phpdoc_order',
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\PhpspecFixer())
    ->finder($finder)
;
