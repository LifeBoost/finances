<?php

declare(strict_types=1);

ini_set('memory_limit', '1G');

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->paths(
        [
            __DIR__ . '/config',
            __DIR__ . '/public',
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    );

    $ecsConfig->rule(DeclareStrictTypesFixer::class);
    $ecsConfig->rule(SingleQuoteFixer::class);
    $ecsConfig->rule(NoUnusedImportsFixer::class);

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::CLEAN_CODE,
        SetList::DOCTRINE_ANNOTATIONS,
        SetList::DOCBLOCK,
        SetList::NAMESPACES,
        SetList::COMMENTS,
    ]);

    $ecsConfig->skip(
        [
            MultilineWhitespaceBeforeSemicolonsFixer::class => null,
            ParamReturnAndVarTagMalformsFixer::class => null,
            YodaStyleFixer::class => null,
            SwitchCaseSemicolonToColonFixer::class => null,
        ]
    );

    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, ['syntax' => 'short']);
    $ecsConfig->ruleWithConfiguration(ClassDefinitionFixer::class, [
        'multi_line_extends_each_single_line' => true,
        'space_before_parenthesis' => true,
    ]);
    $ecsConfig->ruleWithConfiguration(BracesFixer::class, [
        'allow_single_line_anonymous_class_with_empty_body' => true,
    ]);
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationSpacesFixer::class, [
        'before_array_assignments_colon' => false
    ]);
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationArrayAssignmentFixer::class, ['operator' => ':']);

    $ecsConfig->fileExtensions(['php']);
    $ecsConfig->cacheDirectory('cache/.ecs_cache');
};
