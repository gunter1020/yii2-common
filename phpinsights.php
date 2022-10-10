<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Preset
    |--------------------------------------------------------------------------
    |
    | This option controls the default preset that will be used by PHP Insights
    | to make your code reliable, simple, and clean. However, you can always
    | adjust the `Metrics` and `Insights` below in this configuration file.
    |
    | Supported: "default", "laravel", "symfony", "magento2", "drupal"
    |
    */

    'preset' => 'yii',

    /*
    |--------------------------------------------------------------------------
    | IDE
    |--------------------------------------------------------------------------
    |
    | This options allow to add hyperlinks in your terminal to quickly open
    | files in your favorite IDE while browsing your PhpInsights report.
    |
    | Supported: "textmate", "macvim", "emacs", "sublime", "phpstorm",
    | "atom", "vscode".
    |
    | If you have another IDE that is not in this list but which provide an
    | url-handler, you could fill this config with a pattern like this:
    |
    | myide://open?url=file://%f&line=%l
    |
    */

    'ide' => 'vscode',

    /*
    |--------------------------------------------------------------------------
    | Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may adjust all the various `Insights` that will be used by PHP
    | Insights. You can either add, remove or configure `Insights`. Keep in
    | mind, that all added `Insights` must belong to a specific `Metric`.
    |
    */

    'exclude' => [],

    'add' => [],

    'remove' => [
        // 禁用 setter 函式
        NunoMaduro\PhpInsights\Domain\Sniffs\ForbiddenSetterSniff::class,
        // 禁用公開變數
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
        // 禁止定義公開變數
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineGlobalConstants::class,
        // 閉包中禁用 $this
        SlevomatCodingStandard\Sniffs\Functions\StaticClosureSniff::class,
        // 禁用無用參數
        SlevomatCodingStandard\Sniffs\Functions\UnusedParameterSniff::class,
        // 必須開啟嚴格模式
        SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff::class,
        // 禁用 Mixed 偽型別
        SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff::class,
        // 禁用隱含類型陣列
        SlevomatCodingStandard\Sniffs\Arrays\DisallowImplicitArrayCreationSniff::class,
        // 禁用公開屬性
        SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        // 禁用後靜態綁定
        SlevomatCodingStandard\Sniffs\Classes\DisallowLateStaticBindingForConstantsSniff::class,
        // 驚嘆號後面需連接一個空格符號
        PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterNotSniff::class,
        // 禁用無用覆寫方法
        PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UselessOverridingMethodSniff::class,
        // 禁用一般類別
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
    ],

    'config' => [],

    /*
    |--------------------------------------------------------------------------
    | Requirements
    |--------------------------------------------------------------------------
    |
    | Here you may define a level you want to reach per `Insights` category.
    | When a score is lower than the minimum level defined, then an error
    | code will be returned. This is optional and individually defined.
    |
    */

    'requirements' => [
        // 'min-quality' => 0,
        // 'min-complexity' => 0,
        // 'min-architecture' => 0,
        // 'min-style' => 0,
        // 'disable-security-check' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Threads
    |--------------------------------------------------------------------------
    |
    | Here you may adjust how many threads (core) PHPInsights can use to perform
    | the analyse. This is optional, don't provide it and the tool will guess
    | the max core number available. This accept null value or integer > 0.
    |
    */

    'threads' => null,

];
