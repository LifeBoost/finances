<?php

declare(strict_types=1);

ini_set('memory_limit', '1G');

use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\SuperfluousWhitespaceSniff;
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\ArrayNotation\ReturnToYieldFromFixer;
use PhpCsFixer\Fixer\ArrayNotation\YieldFromArrayToYieldsFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\Basic\SingleLineEmptyBodyFixer;
use PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedTypesFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\NullableTypeDeclarationFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use PhpCsFixer\Fixer\Whitespace\TypeDeclarationSpacesFixer;
use PhpCsFixerCustomFixers\Fixer\MultilineCommentOpeningClosingAloneFixer;
use PhpCsFixerCustomFixers\Fixer\NoCommentedOutCodeFixer;
use PhpCsFixerCustomFixers\Fixer\NoDuplicatedArrayKeyFixer;
use PhpCsFixerCustomFixers\Fixer\NoDuplicatedImportsFixer;
use PhpCsFixerCustomFixers\Fixer\NoPhpStormGeneratedCommentFixer;
use PhpCsFixerCustomFixers\Fixer\NoTrailingCommaInSinglelineFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessDirnameCallFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessParenthesisFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocNoSuperfluousParamFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocParamOrderFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocSingleLineVarFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocTypesCommaSpacesFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocTypesTrimFixer;
use PhpCsFixerCustomFixers\Fixer\PhpUnitAssertArgumentsOrderFixer;
use PhpCsFixerCustomFixers\Fixer\PhpUnitDedicatedAssertFixer;
use PhpCsFixerCustomFixers\Fixer\SingleSpaceAfterStatementFixer;
use PhpCsFixerCustomFixers\Fixer\SingleSpaceBeforeStatementFixer;
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

    $ecsConfig->sets([
        SetList::PSR_12,
        SetList::ARRAY,
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

    $ecsConfig->rules([
        ReturnToYieldFromFixer::class,
        SingleLineEmptyBodyFixer::class,
        TypeDeclarationSpacesFixer::class,
        YieldFromArrayToYieldsFixer::class,
        DeclareStrictTypesFixer::class,
        SingleQuoteFixer::class,
        NoUnusedImportsFixer::class,
        NoEmptyPhpdocFixer::class,
        MultilineCommentOpeningClosingAloneFixer::class,
        NoCommentedOutCodeFixer::class,
        NoDuplicatedArrayKeyFixer::class,
        NoDuplicatedImportsFixer::class,
        NoPhpStormGeneratedCommentFixer::class,
        NoTrailingCommaInSinglelineFixer::class,
        NoUselessDirnameCallFixer::class,
        NoUselessParenthesisFixer::class,
        PhpdocNoSuperfluousParamFixer::class,
        PhpdocParamOrderFixer::class,
        PhpdocSingleLineVarFixer::class,
        PhpdocTypesCommaSpacesFixer::class,
        PhpdocTypesTrimFixer::class,
        PhpUnitAssertArgumentsOrderFixer::class,
        PhpUnitDedicatedAssertFixer::class,
        SingleSpaceAfterStatementFixer::class,
        SingleSpaceBeforeStatementFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(ClassDefinitionFixer::class, [
        'multi_line_extends_each_single_line' => true,
        'space_before_parenthesis' => true,
    ]);
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationSpacesFixer::class, [
        'before_array_assignments_colon' => false,
    ]);
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationArrayAssignmentFixer::class, [
        'operator' => ':',
    ]);
    $ecsConfig->ruleWithConfiguration(NoExtraBlankLinesFixer::class, [
        'tokens' => [
            'attribute',
            'case',
            'continue',
            'curly_brace_block',
            'default',
            'extra',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'throw',
            'use',
        ],
    ]);
    $ecsConfig->ruleWithConfiguration(NullableTypeDeclarationFixer::class, [
        'syntax' => 'question_mark',
    ]);
    $ecsConfig->ruleWithConfiguration(OrderedTypesFixer::class, [
        'null_adjustment' => 'always_last',
    ]);
    $ecsConfig->ruleWithConfiguration(ArraySyntaxFixer::class, [
        'syntax' => 'short',
    ]);
    $ecsConfig->ruleWithConfiguration(BracesFixer::class, [
        'allow_single_line_anonymous_class_with_empty_body' => true,
    ]);
    $ecsConfig->ruleWithConfiguration(NoSuperfluousPhpdocTagsFixer::class, [
        'remove_inheritdoc' => true,
        'allow_mixed' => true,
    ]);
    $ecsConfig->ruleWithConfiguration(SuperfluousWhitespaceSniff::class, [
        'ignoreBlankLines' => false,
    ]);

    $ecsConfig->fileExtensions(['php']);
    $ecsConfig->cacheDirectory('cache/.ecs_cache');
};
