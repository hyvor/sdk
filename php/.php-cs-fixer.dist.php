<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'declare_strict_types' => true,
        'strict_comparison' => true,
        'strict_param' => true,
        'logical_operators' => true,
        'php_unit_strict' => true,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
;
