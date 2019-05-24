
<?php
$header = <<<EOF
This file is part of the GooglBigQueryLogger package.
(c) Elixis Digital <support@elixis.com>
For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules(
        [
            '@Symfony' => true,
            'header_comment' => ['header' => $header],
            'phpdoc_var_without_name' => false,
            'logical_operators' => true,
            'phpdoc_separation' => false,
            'psr4' => true,
            'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
            'array_syntax' => ['syntax' => 'short']
        ]
    )
    ->setCacheFile(__DIR__.'/.php_cs.cache')
    ->setFinder(
        PhpCsFixer\Finder::create()
             ->in(__DIR__)
             ->files()
             ->name('*.php')
             ->in(__DIR__.'/src')
    );
