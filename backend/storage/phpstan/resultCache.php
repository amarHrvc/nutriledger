<?php declare(strict_types = 1);

return [
	'lastFullAnalysisTime' => 1773880133,
	'meta' => array (
  'cacheVersion' => 'v12-linesToIgnore',
  'phpstanVersion' => '2.1.42',
  'fnsr' => false,
  'metaExtensions' => 
  array (
  ),
  'phpVersion' => 80321,
  'projectConfig' => '{conditionalTags: {Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule: {phpstan.rules.rule: %noEnvCallsOutsideOfConfig%}, Larastan\\Larastan\\Rules\\NoModelMakeRule: {phpstan.rules.rule: %noModelMake%}, Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule: {phpstan.rules.rule: %noUnnecessaryCollectionCall%}, Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule: {phpstan.rules.rule: %noUnnecessaryEnumerableToArrayCalls%}, Larastan\\Larastan\\Rules\\OctaneCompatibilityRule: {phpstan.rules.rule: %checkOctaneCompatibility%}, Larastan\\Larastan\\Rules\\UnusedViewsRule: {phpstan.rules.rule: %checkUnusedViews%}, Larastan\\Larastan\\Rules\\NoMissingTranslationsRule: {phpstan.rules.rule: %checkMissingTranslations%}, Larastan\\Larastan\\Rules\\ModelAppendsRule: {phpstan.rules.rule: %checkModelAppends%}, Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule: {phpstan.rules.rule: %checkModelMethodVisibility%}, Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %generalizeEnvReturnType%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension: {phpstan.broker.dynamicMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension: {phpstan.broker.dynamicStaticMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\Rules\\ConfigCollectionRule: {phpstan.rules.rule: %checkConfigTypes%}}, parameters: {universalObjectCratesClasses: [Illuminate\\Http\\Request, Illuminate\\Support\\Optional], earlyTerminatingFunctionCalls: [abort, dd], mixinExcludeClasses: [Eloquent], bootstrapFiles: [bootstrap.php], checkOctaneCompatibility: false, noEnvCallsOutsideOfConfig: true, noModelMake: true, noUnnecessaryCollectionCall: true, noUnnecessaryCollectionCallOnly: [], noUnnecessaryCollectionCallExcept: [], noUnnecessaryEnumerableToArrayCalls: false, squashedMigrationsPath: [], databaseMigrationsPath: [], disableMigrationScan: false, disableSchemaScan: false, configDirectories: [], viewDirectories: [], translationDirectories: [], checkModelProperties: false, checkUnusedViews: false, checkMissingTranslations: false, checkModelAppends: true, checkModelMethodVisibility: false, generalizeEnvReturnType: false, checkConfigTypes: false, checkAuthCallsWhenInRequestScope: false, parseModelCastsMethod: false, enableMigrationCache: false, level: 5, paths: [D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app, D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes, D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests], excludePaths: {analyseAndScan: [{path: D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Middleware\\TrustHosts.php}, {path: D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Middleware\\TrustProxies.php}]}, tmpDir: D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\storage\\phpstan}, rules: [Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessWithFunctionCallsRule, Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessValueFunctionCallsRule, Larastan\\Larastan\\Rules\\DeferrableServiceProviderMissingProvidesRule, Larastan\\Larastan\\Rules\\ConsoleCommand\\UndefinedArgumentOrOptionRule], services: {{class: Larastan\\Larastan\\Methods\\RelationForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\EloquentBuilderForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderTapProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderCollectionProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\StorageMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\Extension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelFactoryMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\RedirectResponseMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\MacroMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ViewWithMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelAccessorExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\HigherOrderCollectionProxyPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\HigherOrderTapProxyExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\Properties\\ModelRelationsExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelOnlyDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelFactoryDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthManagerExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DateExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestFileExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestRouteExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestUserExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentBuilderExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RelationCollectionExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TestCaseExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Support\\CollectionHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AuthExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\CollectExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\NowAndTodayExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ResponseExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValidatorExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\LiteralExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionFilterRejectDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionWhereNotNullDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\NewModelQueryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\FactoryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: true}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: true}}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AppExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValueExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\StrExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\TapExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\StorageDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\GenericEloquentCollectionTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Types\\ViewStringTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Rules\\OctaneCompatibilityRule}, {class: Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule, arguments: {configDirectories: %configDirectories%}}, {class: Larastan\\Larastan\\Rules\\NoModelMakeRule}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule, arguments: {onlyMethods: %noUnnecessaryCollectionCallOnly%, excludeMethods: %noUnnecessaryCollectionCallExcept%}}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule}, {class: Larastan\\Larastan\\Rules\\ModelAppendsRule}, {class: Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule}, {class: Larastan\\Larastan\\Types\\GenericEloquentBuilderTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppFacadeEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\ModelProperty\\ModelPropertyTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension], arguments: {active: %checkModelProperties%}}, {class: Larastan\\Larastan\\Types\\CollectionOf\\CollectionOfTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Properties\\MigrationHelper, arguments: {databaseMigrationPath: %databaseMigrationsPath%, disableMigrationScan: %disableMigrationScan%, parser: @migrationsParser, reflectionProvider: @reflectionProvider}}, iamcalSqlParser: {class: Larastan\\Larastan\\SQL\\IamcalSqlParser, autowired: false}, sqlParserFactory: {class: Larastan\\Larastan\\SQL\\SqlParserFactory, arguments: {iamcalSqlParser: @iamcalSqlParser}}, sqlParser: {type: Larastan\\Larastan\\SQL\\SqlParser, factory: [@sqlParserFactory, create]}, {class: Larastan\\Larastan\\Properties\\SquashedMigrationHelper, arguments: {schemaPaths: %squashedMigrationsPath%, disableSchemaScan: %disableSchemaScan%}}, {class: Larastan\\Larastan\\Properties\\ModelCastHelper, arguments: {parser: @currentPhpVersionSimpleDirectParser, parseModelCastsMethod: %parseModelCastsMethod%}}, {class: Larastan\\Larastan\\Properties\\MigrationCache, arguments: {cacheDirectory: %tmpDir%, enabled: %enableMigrationCache%}}, {class: Larastan\\Larastan\\Properties\\ModelPropertyHelper}, {class: Larastan\\Larastan\\Rules\\ModelRuleHelper}, {class: Larastan\\Larastan\\Methods\\BuilderHelper, arguments: {checkProperties: %checkModelProperties%}}, {class: Larastan\\Larastan\\Rules\\RelationExistenceRule, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Bus\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Events\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Properties\\Schema\\MySqlDataTypeToPhpTypeConverter}, {class: Larastan\\Larastan\\LarastanStubFilesExtension, tags: [phpstan.stubFilesExtension]}, {class: Larastan\\Larastan\\Rules\\UnusedViewsRule}, {class: Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedEmailViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewFacadeMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedRouteFacadeViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewInAnotherViewCollector}, {class: Larastan\\Larastan\\Support\\ViewFileHelper, arguments: {viewDirectories: %viewDirectories%}}, {class: Larastan\\Larastan\\Support\\ViewParser, arguments: {parser: @currentPhpVersionSimpleDirectParser}}, {class: Larastan\\Larastan\\Rules\\NoMissingTranslationsRule, arguments: {translationDirectories: %translationDirectories%}}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationTranslatorCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFacadeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationViewCollector}, {class: Larastan\\Larastan\\ReturnTypes\\ApplicationMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\ArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\OptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasOptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TranslatorGetReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\LangGetReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TransHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DoubleUnderscoreHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeHelper}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationResolver}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationHelper}, {class: Larastan\\Larastan\\Support\\HigherOrderCollectionProxyHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension}, {class: Larastan\\Larastan\\Support\\ConfigParser, arguments: {parser: @currentPhpVersionSimpleDirectParser, configPaths: %configDirectories%, treatPhpDocTypesAsCertain: %treatPhpDocTypesAsCertain%}}, {class: Larastan\\Larastan\\Internal\\ConfigHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\FormRequestSafeDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\ConfigCollectionRule}, {class: Illuminate\\Filesystem\\Filesystem, autowired: self}, migrationsParser: {class: PHPStan\\Parser\\CachedParser, arguments: {originalParser: @currentPhpVersionSimpleDirectParser, cachedNodesByStringCountMax: %cache.nodesByStringCountMax%}, autowired: false}}}',
  'analysedPaths' => 
  array (
    0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app',
    1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes',
    2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests',
  ),
  'scannedFiles' => 
  array (
  ),
  'composerLocks' => 
  array (
    'D:/_Learn/_PhpstormProjects/nutri-ledger/backend/composer.lock' => '25f6eb4afb8ccd717536161db3c32227929a87b21d003b39dffb6ff631b2ac25',
  ),
  'composerInstalled' => 
  array (
    'D:/_Learn/_PhpstormProjects/nutri-ledger/backend/vendor/composer/installed.php' => 
    array (
      'versions' => 
      array (
        'brianium/paratest' => 
        array (
          'pretty_version' => 'v7.19.0',
          'version' => '7.19.0.0',
          'reference' => '7c6c29af7c4b406b49ce0c6b0a3a81d3684474e6',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../brianium/paratest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'brick/math' => 
        array (
          'pretty_version' => '0.14.8',
          'version' => '0.14.8.0',
          'reference' => '63422359a44b7f06cae63c3b429b59e8efcc0629',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../brick/math',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'carbonphp/carbon-doctrine-types' => 
        array (
          'pretty_version' => '3.2.0',
          'version' => '3.2.0.0',
          'reference' => '18ba5ddfec8976260ead6e866180bd5d2f71aa1d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../carbonphp/carbon-doctrine-types',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'cordoval/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'davedevelopment/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'dflydev/dot-access-data' => 
        array (
          'pretty_version' => 'v3.0.3',
          'version' => '3.0.3.0',
          'reference' => 'a23a2bf4f31d3518f3ecb38660c95715dfead60f',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../dflydev/dot-access-data',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/deprecations' => 
        array (
          'pretty_version' => '1.1.6',
          'version' => '1.1.6.0',
          'reference' => 'd4fe3e6fd9bb9e72557a19674f44d8ac7db4c6ca',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../doctrine/deprecations',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'doctrine/inflector' => 
        array (
          'pretty_version' => '2.1.0',
          'version' => '2.1.0.0',
          'reference' => '6d6c96277ea252fc1304627204c3d5e6e15faa3b',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../doctrine/inflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/lexer' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => '31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../doctrine/lexer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragonmantank/cron-expression' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => 'd61a8a9604ec1f8c3d150d09db6ce98b32675013',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../dragonmantank/cron-expression',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'egulias/email-validator' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'reference' => 'd42c8731f0624ad6bdc8d3e5e9a4524f68801cfa',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../egulias/email-validator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fakerphp/faker' => 
        array (
          'pretty_version' => 'v1.24.1',
          'version' => '1.24.1.0',
          'reference' => 'e0ee18eb1e6dc3cda3ce9fd97e5a0689a88a64b5',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../fakerphp/faker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fidry/cpu-core-counter' => 
        array (
          'pretty_version' => '1.3.0',
          'version' => '1.3.0.0',
          'reference' => 'db9508f7b1474469d9d3c53b86f817e344732678',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../fidry/cpu-core-counter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'filp/whoops' => 
        array (
          'pretty_version' => '2.18.4',
          'version' => '2.18.4.0',
          'reference' => 'd2102955e48b9fd9ab24280a7ad12ed552752c4d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../filp/whoops',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fruitcake/php-cors' => 
        array (
          'pretty_version' => 'v1.4.0',
          'version' => '1.4.0.0',
          'reference' => '38aaa6c3fd4c157ffe2a4d10aa8b9b16ba8de379',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../fruitcake/php-cors',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'graham-campbell/result-type' => 
        array (
          'pretty_version' => 'v1.1.4',
          'version' => '1.1.4.0',
          'reference' => 'e01f4a821471308ba86aa202fed6698b6b695e3b',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../graham-campbell/result-type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => 
        array (
          'pretty_version' => '7.10.0',
          'version' => '7.10.0.0',
          'reference' => 'b51ac707cfa420b7bfd4e4d5e510ba8008e822b4',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../guzzlehttp/guzzle',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/promises' => 
        array (
          'pretty_version' => '2.3.0',
          'version' => '2.3.0.0',
          'reference' => '481557b130ef3790cf82b713667b43030dc9c957',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../guzzlehttp/promises',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/psr7' => 
        array (
          'pretty_version' => '2.9.0',
          'version' => '2.9.0.0',
          'reference' => '7d0ed42f28e42d61352a7a79de682e5e67fec884',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../guzzlehttp/psr7',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/uri-template' => 
        array (
          'pretty_version' => 'v1.0.5',
          'version' => '1.0.5.0',
          'reference' => '4f4bbd4e7172148801e76e3decc1e559bdee34e1',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../guzzlehttp/uri-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'hamcrest/hamcrest-php' => 
        array (
          'pretty_version' => 'v2.1.1',
          'version' => '2.1.1.0',
          'reference' => 'f8b1c0173b22fa6ec77a81fe63e5b01eba7e6487',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../hamcrest/hamcrest-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'iamcal/sql-parser' => 
        array (
          'pretty_version' => 'v0.7',
          'version' => '0.7.0.0',
          'reference' => '610392f38de49a44dab08dc1659960a29874c4b8',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../iamcal/sql-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'illuminate/auth' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/broadcasting' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/bus' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/cache' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/collections' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/concurrency' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/conditionable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/config' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/console' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/container' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/contracts' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/cookie' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/database' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/encryption' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/events' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/filesystem' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/hashing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/http' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/json-schema' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/log' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/macroable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/mail' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/notifications' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/pagination' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/pipeline' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/process' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/queue' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/redis' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/reflection' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/routing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/session' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/support' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/testing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/translation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/validation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'illuminate/view' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.0.0',
          ),
        ),
        'jean85/pretty-package-versions' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '4d7aa5dab42e2a76d99559706022885de0e18e1a',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../jean85/pretty-package-versions',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'kodova/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'larastan/larastan' => 
        array (
          'pretty_version' => 'v3.9.3',
          'version' => '3.9.3.0',
          'reference' => '64a52bcc5347c89fdf131cb59f96ebfbc8d1ad65',
          'type' => 'phpstan-extension',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../larastan/larastan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/framework' => 
        array (
          'pretty_version' => 'v13.0.0',
          'version' => '13.0.0.0',
          'reference' => '3e33f431a05365d008742ff8001b92641086d5f8',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../laravel/framework',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/pail' => 
        array (
          'pretty_version' => 'v1.2.6',
          'version' => '1.2.6.0',
          'reference' => 'aa71a01c309e7f66bc2ec4fb1a59291b82eb4abf',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../laravel/pail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pint' => 
        array (
          'pretty_version' => 'v1.29.0',
          'version' => '1.29.0.0',
          'reference' => 'bdec963f53172c5e36330f3a400604c69bf02d39',
          'type' => 'project',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../laravel/pint',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/prompts' => 
        array (
          'pretty_version' => 'v0.3.15',
          'version' => '0.3.15.0',
          'reference' => '4bb8107ec97651fd3f17f897d6489dbc4d8fb999',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../laravel/prompts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/sanctum' => 
        array (
          'pretty_version' => 'v4.3.1',
          'version' => '4.3.1.0',
          'reference' => 'e3b85d6e36ad00e5db2d1dcc27c81ffdf15cbf76',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../laravel/sanctum',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/serializable-closure' => 
        array (
          'pretty_version' => 'v2.0.10',
          'version' => '2.0.10.0',
          'reference' => '870fc81d2f879903dfc5b60bf8a0f94a1609e669',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../laravel/serializable-closure',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/tinker' => 
        array (
          'pretty_version' => 'v3.0.0',
          'version' => '3.0.0.0',
          'reference' => 'cc74081282ba2e3dae1f0068ccb330370d24634e',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../laravel/tinker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/commonmark' => 
        array (
          'pretty_version' => '2.8.1',
          'version' => '2.8.1.0',
          'reference' => '84b1ca48347efdbe775426f108622a42735a6579',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../league/commonmark',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/config' => 
        array (
          'pretty_version' => 'v1.2.0',
          'version' => '1.2.0.0',
          'reference' => '754b3604fb2984c71f4af4a9cbe7b57f346ec1f3',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../league/config',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem' => 
        array (
          'pretty_version' => '3.32.0',
          'version' => '3.32.0.0',
          'reference' => '254b1595b16b22dbddaaef9ed6ca9fdac4956725',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../league/flysystem',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem-local' => 
        array (
          'pretty_version' => '3.31.0',
          'version' => '3.31.0.0',
          'reference' => '2f669db18a4c20c755c2bb7d3a7b0b2340488079',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../league/flysystem-local',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/mime-type-detection' => 
        array (
          'pretty_version' => '1.16.0',
          'version' => '1.16.0.0',
          'reference' => '2d6702ff215bf922936ccc1ad31007edc76451b9',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../league/mime-type-detection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '08cf38e3924d4f56238125547b5720496fac8fd4',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../league/uri',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri-interfaces' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '85d5c77c5d6d3af6c54db4a78246364908f3c928',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../league/uri-interfaces',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mockery/mockery' => 
        array (
          'pretty_version' => '1.6.12',
          'version' => '1.6.12.0',
          'reference' => '1f4efdd7d3beafe9807b08156dfcb176d18f1699',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../mockery/mockery',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'monolog/monolog' => 
        array (
          'pretty_version' => '3.10.0',
          'version' => '3.10.0.0',
          'reference' => 'b321dd6749f0bf7189444158a3ce785cc16d69b0',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../monolog/monolog',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mtdowling/cron-expression' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '^1.0',
          ),
        ),
        'myclabs/deep-copy' => 
        array (
          'pretty_version' => '1.13.4',
          'version' => '1.13.4.0',
          'reference' => '07d290f0c47959fd5eed98c95ee5602db07e0b6a',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../myclabs/deep-copy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nesbot/carbon' => 
        array (
          'pretty_version' => '3.11.3',
          'version' => '3.11.3.0',
          'reference' => '6a7e652845bb018c668220c2a545aded8594fbbf',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../nesbot/carbon',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/schema' => 
        array (
          'pretty_version' => 'v1.3.5',
          'version' => '1.3.5.0',
          'reference' => 'f0ab1a3cda782dbc5da270d28545236aa80c4002',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../nette/schema',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/utils' => 
        array (
          'pretty_version' => 'v4.1.3',
          'version' => '4.1.3.0',
          'reference' => 'bb3ea637e3d131d72acc033cfc2746ee893349fe',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../nette/utils',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nikic/php-parser' => 
        array (
          'pretty_version' => 'v5.7.0',
          'version' => '5.7.0.0',
          'reference' => 'dca41cd15c2ac9d055ad70dbfd011130757d1f82',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../nikic/php-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nunomaduro/collision' => 
        array (
          'pretty_version' => 'v8.9.1',
          'version' => '8.9.1.0',
          'reference' => 'a1ed3fa530fd60bc515f9303e8520fcb7d4bd935',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../nunomaduro/collision',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nunomaduro/termwind' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => '712a31b768f5daea284c2169a7d227031001b9a8',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../nunomaduro/termwind',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'pestphp/pest' => 
        array (
          'pretty_version' => 'v4.4.2',
          'version' => '4.4.2.0',
          'reference' => '5d42e8fe3ae1d9fdf7c9f73ee88138fd30265701',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../pestphp/pest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin' => 
        array (
          'pretty_version' => 'v4.0.0',
          'version' => '4.0.0.0',
          'reference' => '9d4b93d7f73d3f9c3189bb22c220fef271cdf568',
          'type' => 'composer-plugin',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../pestphp/pest-plugin',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-arch' => 
        array (
          'pretty_version' => 'v4.0.0',
          'version' => '4.0.0.0',
          'reference' => '25bb17e37920ccc35cbbcda3b00d596aadf3e58d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../pestphp/pest-plugin-arch',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-laravel' => 
        array (
          'pretty_version' => 'v4.1.0',
          'version' => '4.1.0.0',
          'reference' => '3057a36669ff11416cc0dc2b521b3aec58c488d0',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../pestphp/pest-plugin-laravel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-mutate' => 
        array (
          'pretty_version' => 'v4.0.1',
          'version' => '4.0.1.0',
          'reference' => 'd9b32b60b2385e1688a68cc227594738ec26d96c',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../pestphp/pest-plugin-mutate',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-profanity' => 
        array (
          'pretty_version' => 'v4.2.1',
          'version' => '4.2.1.0',
          'reference' => '343cfa6f3564b7e35df0ebb77b7fa97039f72b27',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../pestphp/pest-plugin-profanity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/manifest' => 
        array (
          'pretty_version' => '2.0.4',
          'version' => '2.0.4.0',
          'reference' => '54750ef60c58e43759730615a392c31c80e23176',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phar-io/manifest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/version' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'reference' => '4f7fd7836c6f332bb2933569e566a0d6c4cbed74',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phar-io/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-common' => 
        array (
          'pretty_version' => '2.2.0',
          'version' => '2.2.0.0',
          'reference' => '1d01c49d4ed62f25aa84a747ad35d5a16924662b',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpdocumentor/reflection-common',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-docblock' => 
        array (
          'pretty_version' => '6.0.2',
          'version' => '6.0.2.0',
          'reference' => '897b5986ece6b4f9d8413fea345c7d49c757d6bf',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpdocumentor/reflection-docblock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/type-resolver' => 
        array (
          'pretty_version' => '2.0.0',
          'version' => '2.0.0.0',
          'reference' => '327a05bbee54120d4786a0dc67aad30226ad4cf9',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpdocumentor/type-resolver',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpoption/phpoption' => 
        array (
          'pretty_version' => '1.9.5',
          'version' => '1.9.5.0',
          'reference' => '75365b91986c2405cf5e1e012c5595cd487a98be',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpoption/phpoption',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpstan/phpdoc-parser' => 
        array (
          'pretty_version' => '2.3.2',
          'version' => '2.3.2.0',
          'reference' => 'a004701b11273a26cd7955a61d67a7f1e525a45a',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpstan/phpdoc-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpstan/phpstan' => 
        array (
          'pretty_version' => '2.1.42',
          'version' => '2.1.42.0',
          'reference' => '1279e1ce86ba768f0780c9d889852b4e02ff40d0',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpstan/phpstan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-code-coverage' => 
        array (
          'pretty_version' => '12.5.3',
          'version' => '12.5.3.0',
          'reference' => 'b015312f28dd75b75d3422ca37dff2cd1a565e8d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpunit/php-code-coverage',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-file-iterator' => 
        array (
          'pretty_version' => '6.0.1',
          'version' => '6.0.1.0',
          'reference' => '3d1cd096ef6bea4bf2762ba586e35dbd317cbfd5',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpunit/php-file-iterator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-invoker' => 
        array (
          'pretty_version' => '6.0.0',
          'version' => '6.0.0.0',
          'reference' => '12b54e689b07a25a9b41e57736dfab6ec9ae5406',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpunit/php-invoker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-text-template' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => 'e1367a453f0eda562eedb4f659e13aa900d66c53',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpunit/php-text-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-timer' => 
        array (
          'pretty_version' => '8.0.0',
          'version' => '8.0.0.0',
          'reference' => 'f258ce36aa457f3aa3339f9ed4c81fc66dc8c2cc',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpunit/php-timer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/phpunit' => 
        array (
          'pretty_version' => '12.5.12',
          'version' => '12.5.12.0',
          'reference' => '418e06b3b46b0d54bad749ff4907fc7dfb530199',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../phpunit/phpunit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'psr/clock' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'e41a24703d4560fd0acb709162f73b8adfc3aa0d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psr/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/clock-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/container' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psr/container',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/container-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.1 || 2.0',
          ),
        ),
        'psr/event-dispatcher' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psr/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-client' => 
        array (
          'pretty_version' => '1.0.3',
          'version' => '1.0.3.0',
          'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psr/http-client',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-client-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-factory' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'reference' => '2b4765fddfe3b508ac62f829e852b1501d3f6e8a',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psr/http-factory',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-factory-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-message' => 
        array (
          'pretty_version' => '2.0',
          'version' => '2.0.0.0',
          'reference' => '402d35bcb92c70c026d1a6a9883f06b2ead23d71',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psr/http-message',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-message-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/log' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psr/log',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/log-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0 || 2.0 || 3.0',
            1 => '1.0|2.0|3.0',
            2 => '3.0.0',
          ),
        ),
        'psr/simple-cache' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psr/simple-cache',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/simple-cache-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0 || 2.0 || 3.0',
          ),
        ),
        'psy/psysh' => 
        array (
          'pretty_version' => 'v0.12.21',
          'version' => '0.12.21.0',
          'reference' => '4821fab5b7cd8c49a673a9fd5754dc9162bb9e97',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../psy/psysh',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ralouphie/getallheaders' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '120b605dfeb996808c31b6477290a714d356e822',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../ralouphie/getallheaders',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/collection' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '344572933ad0181accbf4ba763e85a0306a8c5e2',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../ramsey/collection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/uuid' => 
        array (
          'pretty_version' => '4.9.2',
          'version' => '4.9.2.0',
          'reference' => '8429c78ca35a09f27565311b98101e2826affde0',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../ramsey/uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'rhumsaa/uuid' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '4.9.2',
          ),
        ),
        'sebastian/cli-parser' => 
        array (
          'pretty_version' => '4.2.0',
          'version' => '4.2.0.0',
          'reference' => '90f41072d220e5c40df6e8635f5dafba2d9d4d04',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/cli-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/comparator' => 
        array (
          'pretty_version' => '7.1.4',
          'version' => '7.1.4.0',
          'reference' => '6a7de5df2e094f9a80b40a522391a7e6022df5f6',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/comparator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/complexity' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => 'bad4316aba5303d0221f43f8cee37eb58d384bbb',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/complexity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/diff' => 
        array (
          'pretty_version' => '7.0.0',
          'version' => '7.0.0.0',
          'reference' => '7ab1ea946c012266ca32390913653d844ecd085f',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/diff',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/environment' => 
        array (
          'pretty_version' => '8.0.4',
          'version' => '8.0.4.0',
          'reference' => '7b8842c2d8e85d0c3a5831236bf5869af6ab2a11',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/environment',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/exporter' => 
        array (
          'pretty_version' => '7.0.2',
          'version' => '7.0.2.0',
          'reference' => '016951ae10980765e4e7aee491eb288c64e505b7',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/exporter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/global-state' => 
        array (
          'pretty_version' => '8.0.2',
          'version' => '8.0.2.0',
          'reference' => 'ef1377171613d09edd25b7816f05be8313f9115d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/global-state',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/lines-of-code' => 
        array (
          'pretty_version' => '4.0.0',
          'version' => '4.0.0.0',
          'reference' => '97ffee3bcfb5805568d6af7f0f893678fc076d2f',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/lines-of-code',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-enumerator' => 
        array (
          'pretty_version' => '7.0.0',
          'version' => '7.0.0.0',
          'reference' => '1effe8e9b8e068e9ae228e542d5d11b5d16db894',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/object-enumerator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-reflector' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => '4bfa827c969c98be1e527abd576533293c634f6a',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/object-reflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/recursion-context' => 
        array (
          'pretty_version' => '7.0.1',
          'version' => '7.0.1.0',
          'reference' => '0b01998a7d5b1f122911a66bebcb8d46f0c82d8c',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/recursion-context',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/type' => 
        array (
          'pretty_version' => '6.0.3',
          'version' => '6.0.3.0',
          'reference' => 'e549163b9760b8f71f191651d22acf32d56d6d4d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/version' => 
        array (
          'pretty_version' => '6.0.0',
          'version' => '6.0.0.0',
          'reference' => '3e6ccf7657d4f0a59200564b08cead899313b53c',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../sebastian/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'spatie/once' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'staabm/side-effects-detector' => 
        array (
          'pretty_version' => '1.0.5',
          'version' => '1.0.5.0',
          'reference' => 'd8334211a140ce329c13726d4a715adbddd0a163',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../staabm/side-effects-detector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'symfony/clock' => 
        array (
          'pretty_version' => 'v7.4.0',
          'version' => '7.4.0.0',
          'reference' => '9169f24776edde469914c1e7a1442a50f7a4e110',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/console' => 
        array (
          'pretty_version' => 'v7.4.7',
          'version' => '7.4.7.0',
          'reference' => 'e1e6770440fb9c9b0cf725f81d1361ad1835329d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/console',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/css-selector' => 
        array (
          'pretty_version' => 'v7.4.6',
          'version' => '7.4.6.0',
          'reference' => '2e7c52c647b406e2107dd867db424a4dbac91864',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/css-selector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/deprecation-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '63afe740e99a13ba87ec199bb07bbdee937a5b62',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/deprecation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/error-handler' => 
        array (
          'pretty_version' => 'v7.4.4',
          'version' => '7.4.4.0',
          'reference' => '8da531f364ddfee53e36092a7eebbbd0b775f6b8',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/error-handler',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher' => 
        array (
          'pretty_version' => 'v7.4.4',
          'version' => '7.4.4.0',
          'reference' => 'dc2c0eba1af673e736bb851d747d266108aea746',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-contracts' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => '59eb412e93815df44f05f342958efa9f46b1e586',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/event-dispatcher-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.0|3.0',
          ),
        ),
        'symfony/finder' => 
        array (
          'pretty_version' => 'v7.4.6',
          'version' => '7.4.6.0',
          'reference' => '8655bf1076b7a3a346cb11413ffdabff50c7ffcf',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/finder',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-foundation' => 
        array (
          'pretty_version' => 'v7.4.7',
          'version' => '7.4.7.0',
          'reference' => 'f94b3e7b7dafd40e666f0c9ff2084133bae41e81',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/http-foundation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-kernel' => 
        array (
          'pretty_version' => 'v7.4.7',
          'version' => '7.4.7.0',
          'reference' => '3b3fcf386c809be990c922e10e4c620d6367cab1',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/http-kernel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mailer' => 
        array (
          'pretty_version' => 'v7.4.6',
          'version' => '7.4.6.0',
          'reference' => 'b02726f39a20bc65e30364f5c750c4ddbf1f58e9',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/mailer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mime' => 
        array (
          'pretty_version' => 'v7.4.7',
          'version' => '7.4.7.0',
          'reference' => 'da5ab4fde3f6c88ab06e96185b9922f48b677cd1',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/mime',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => 'a3cc8b044a6ea513310cbd48ef7333b384945638',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-ctype',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-grapheme' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '380872130d3a5dd3ace2f4010d95125fde5d5c70',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-intl-grapheme',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-idn' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '9614ac4d8061dc257ecc64cba1b140873dce8ad3',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-intl-idn',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-normalizer' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '3833d7255cc303546435cb650316bff708a1c75c',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-intl-normalizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '6d857f4d76bd4b343eac26d6b539585d2bc56493',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-mbstring',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php80' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '0cc9dd0f17f61d8131e7df6b84bd344899fe2608',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-php80',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php83' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '17f6f9a6b1735c0f163024d959f700cfbc5155e5',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-php83',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php84' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => 'd8ced4d875142b6a7426000426b8abc631d6b191',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-php84',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php85' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => 'd4e5fcd4ab3d998ab16c0db48e6cbb9a01993f91',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-php85',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-uuid' => 
        array (
          'pretty_version' => 'v1.33.0',
          'version' => '1.33.0.0',
          'reference' => '21533be36c24be3f4b1669c4725c7d1d2bab4ae2',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/polyfill-uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/process' => 
        array (
          'pretty_version' => 'v7.4.5',
          'version' => '7.4.5.0',
          'reference' => '608476f4604102976d687c483ac63a79ba18cc97',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/process',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/routing' => 
        array (
          'pretty_version' => 'v7.4.6',
          'version' => '7.4.6.0',
          'reference' => '238d749c56b804b31a9bf3e26519d93b65a60938',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/routing',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/service-contracts' => 
        array (
          'pretty_version' => 'v3.6.1',
          'version' => '3.6.1.0',
          'reference' => '45112560a3ba2d715666a509a0bc9521d10b6c43',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/service-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/string' => 
        array (
          'pretty_version' => 'v7.4.6',
          'version' => '7.4.6.0',
          'reference' => '9f209231affa85aa930a5e46e6eb03381424b30b',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/string',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation' => 
        array (
          'pretty_version' => 'v7.4.6',
          'version' => '7.4.6.0',
          'reference' => '1888cf064399868af3784b9e043240f1d89d25ce',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/translation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-contracts' => 
        array (
          'pretty_version' => 'v3.6.1',
          'version' => '3.6.1.0',
          'reference' => '65a8bc82080447fae78373aa10f8d13b38338977',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/translation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.3|3.0',
          ),
        ),
        'symfony/uid' => 
        array (
          'pretty_version' => 'v7.4.4',
          'version' => '7.4.4.0',
          'reference' => '7719ce8aba76be93dfe249192f1fbfa52c588e36',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/uid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/var-dumper' => 
        array (
          'pretty_version' => 'v7.4.6',
          'version' => '7.4.6.0',
          'reference' => '045321c440ac18347b136c63d2e9bf28a2dc0291',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../symfony/var-dumper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ta-tikoma/phpunit-architecture-test' => 
        array (
          'pretty_version' => '0.8.7',
          'version' => '0.8.7.0',
          'reference' => '1248f3f506ca9641d4f68cebcd538fa489754db8',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../ta-tikoma/phpunit-architecture-test',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'theseer/tokenizer' => 
        array (
          'pretty_version' => '2.0.1',
          'version' => '2.0.1.0',
          'reference' => '7989e43bf381af0eac72e4f0ca5bcbfa81658be4',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../theseer/tokenizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'tijsverkoyen/css-to-inline-styles' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => 'f0292ccf0ec75843d65027214426b6b163b48b41',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../tijsverkoyen/css-to-inline-styles',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'vlucas/phpdotenv' => 
        array (
          'pretty_version' => 'v5.6.3',
          'version' => '5.6.3.0',
          'reference' => '955e7815d677a3eaa7075231212f2110983adecc',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../vlucas/phpdotenv',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'voku/portable-ascii' => 
        array (
          'pretty_version' => '2.0.3',
          'version' => '2.0.3.0',
          'reference' => 'b1d923f88091c6bf09699efcd7c8a1b1bfd7351d',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../voku/portable-ascii',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'webmozart/assert' => 
        array (
          'pretty_version' => '2.1.6',
          'version' => '2.1.6.0',
          'reference' => 'ff31ad6efc62e66e518fbab1cde3453d389bcdc8',
          'type' => 'library',
          'install_path' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\composer/../webmozart/assert',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
      ),
    ),
  ),
  'executedFilesHashes' => 
  array (
    'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\larastan\\larastan\\bootstrap.php' => '5a3eacbf63b3e41659adfee92facededf8e020a932800f93c9a8b0e67f235805',
    'phar://D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\Attribute85.php' => 'cb8b31e82c61ce197871c9e8a6f122256751f2ab606dd2be90846d4fa5f8933e',
    'phar://D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionAttribute.php' => 'c0068e383717870a304781d462f7e2afe1c6f24e9133851852a2aca96b4fa26f',
    'phar://D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionIntersectionType.php' => '65fe0a8bc6fe285d8ddc8798ab5b9299920af70db5ad74596bc08df823e7c5d9',
    'phar://D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionUnionType.php' => '1e2fe940e4ba4e00d9ee6adb2af3ee1bf333e6f8afe61c61deb038886d293427',
  ),
  'phpExtensions' => 
  array (
    0 => 'Core',
    1 => 'PDO',
    2 => 'Phar',
    3 => 'Reflection',
    4 => 'SPL',
    5 => 'SimpleXML',
    6 => 'bcmath',
    7 => 'calendar',
    8 => 'ctype',
    9 => 'curl',
    10 => 'date',
    11 => 'dom',
    12 => 'fileinfo',
    13 => 'filter',
    14 => 'hash',
    15 => 'iconv',
    16 => 'json',
    17 => 'libxml',
    18 => 'mbstring',
    19 => 'mysqlnd',
    20 => 'openssl',
    21 => 'pcre',
    22 => 'pdo_mysql',
    23 => 'pdo_sqlite',
    24 => 'random',
    25 => 'readline',
    26 => 'session',
    27 => 'standard',
    28 => 'tokenizer',
    29 => 'xml',
    30 => 'xmlreader',
    31 => 'xmlwriter',
    32 => 'zlib',
  ),
  'stubFiles' => 
  array (
  ),
  'level' => '5',
),
	'projectExtensionFiles' => array (
),
	'errorsCallback' => static function (): array { return array (
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientSocioeconomicPolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$user_id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientSocioeconomicPolicy.php',
       'line' => 27,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientSocioeconomicPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 27,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php',
       'line' => 29,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 29,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'PHPDoc tag @return with type void|true is not subtype of native type bool.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php',
       'line' => 85,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 85,
       'nodeType' => 'PhpParser\\Node\\Stmt\\ClassMethod',
       'identifier' => 'return.phpDocType',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::postJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
       'line' => 8,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 8,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::postJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
       'line' => 57,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 57,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::postJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
       'line' => 62,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 62,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
       'line' => 68,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 68,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::postJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 11,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 11,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::withToken().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 31,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 31,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property PHPUnit\\Framework\\TestCase::$app.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 36,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 36,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::withToken().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 38,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 38,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::postJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 45,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 45,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::postJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 52,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 52,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::postJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 57,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 57,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 67,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 67,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    8 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::getJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'line' => 75,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 75,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::getJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'line' => 9,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 9,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::postJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'line' => 15,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 15,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::getJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'line' => 22,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 22,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'line' => 30,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 30,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::getJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'line' => 37,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 37,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'line' => 11,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 11,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'line' => 18,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 18,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::getJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'line' => 25,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 25,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'line' => 32,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 32,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'line' => 39,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 39,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'line' => 48,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 48,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 28,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 28,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 37,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 37,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 45,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 45,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 53,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 53,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 62,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 62,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 70,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 70,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 79,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 79,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 88,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 88,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    8 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 97,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 97,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    9 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::getJson().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 103,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 103,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    10 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method PHPUnit\\Framework\\TestCase::actingAs().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'line' => 111,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 111,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$name.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 14,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 14,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 16,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 16,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 29,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 29,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 29,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 29,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property App\\Models\\Patient::$full_name.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 41,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 41,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Cannot call method format() on string.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 73,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 73,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.nonObject',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$patient.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 82,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 82,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$patient.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 83,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 83,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    8 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$patient.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'line' => 84,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 84,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::can().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'line' => 39,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 39,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::can().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'line' => 96,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 96,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::can().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'line' => 123,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 123,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::can().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'line' => 157,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 157,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::can().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'line' => 187,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 187,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicFactoryTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicFactoryTest.php',
       'line' => 25,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicFactoryTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 25,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 16,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 16,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$patient_id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 17,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 17,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$marital_status.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 43,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 43,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$number_of_dependents.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 44,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 44,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$occupation.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 45,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 45,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$has_health_insurance.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 46,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 46,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    6 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$has_family_support.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 47,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 47,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    7 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$has_caregiver.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 48,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 48,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    8 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$additional_notes.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 49,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 49,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    9 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$marital_status.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 83,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 83,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    10 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$living_arrangement.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 84,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 84,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    11 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$employment_status.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 85,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 85,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    12 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$income_level.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 86,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 86,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    13 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$education_level.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 87,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 87,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    14 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$transportation_access.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 88,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 88,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    15 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$food_security_status.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 89,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 89,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    16 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$dietary_restrictions_cultural.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 90,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 90,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    17 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$additional_notes.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 91,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 91,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    18 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$has_health_insurance.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 100,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 100,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    19 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$has_family_support.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 107,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 107,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    20 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$has_caregiver.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 114,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 114,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    21 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$number_of_dependents.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 121,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 121,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    22 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$patient.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 130,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 130,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    23 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$patient.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 131,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 131,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    24 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$marital_status.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 141,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 141,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    25 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 167,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 167,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    26 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 192,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 192,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    27 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$marital_status.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 193,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 193,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    28 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$employment_status.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 194,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 194,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    29 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$additional_notes.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'line' => 195,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 195,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::can().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
       'line' => 26,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 26,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::can().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
       'line' => 39,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 39,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Call to an undefined method Illuminate\\Database\\Eloquent\\Model::can().',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
       'line' => 57,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 57,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsFactoryTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$role.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsFactoryTest.php',
       'line' => 19,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsFactoryTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 19,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php' => 
  array (
    0 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'line' => 19,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 19,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    1 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'line' => 19,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 19,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    2 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$role.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'line' => 35,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 35,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    3 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Cannot call method isToday() on string.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'line' => 48,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 48,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.nonObject',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    4 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Cannot call method toDateString() on string.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'line' => 49,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'traitFilePath' => NULL,
       'tip' => NULL,
       'nodeLine' => 49,
       'nodeType' => 'PhpParser\\Node\\Expr\\MethodCall',
       'identifier' => 'method.nonObject',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
    5 => 
    \PHPStan\Analyser\Error::__set_state(array(
       'message' => 'Access to an undefined property Illuminate\\Database\\Eloquent\\Model::$patient_id.',
       'file' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'line' => 60,
       'canBeIgnored' => true,
       'filePath' => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
       'traitFilePath' => NULL,
       'tip' => 'Learn more: <fg=cyan>https://phpstan.org/blog/solving-phpstan-access-to-undefined-property</>',
       'nodeLine' => 60,
       'nodeType' => 'PhpParser\\Node\\Expr\\PropertyFetch',
       'identifier' => 'property.notFound',
       'metadata' => 
      array (
      ),
       'fixedErrorDiff' => NULL,
    )),
  ),
); },
	'locallyIgnoredErrorsCallback' => static function (): array { return array (
); },
	'linesToIgnore' => array (
),
	'unmatchedLineIgnores' => array (
),
	'collectedDataCallback' => static function (): array { return array (
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\ApiController.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Traits\\ApiResponses',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\UserController.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Controllers\\Api\\UserController',
        1 => 'index',
        2 => 'App\\Http\\Controllers\\Api\\UserController',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Controllers\\Api\\UserController',
        1 => 'create',
        2 => 'App\\Http\\Controllers\\Api\\UserController',
      ),
      2 => 
      array (
        0 => 'App\\Http\\Controllers\\Api\\UserController',
        1 => 'store',
        2 => 'App\\Http\\Controllers\\Api\\UserController',
      ),
      3 => 
      array (
        0 => 'App\\Http\\Controllers\\Api\\UserController',
        1 => 'edit',
        2 => 'App\\Http\\Controllers\\Api\\UserController',
      ),
      4 => 
      array (
        0 => 'App\\Http\\Controllers\\Api\\UserController',
        1 => 'update',
        2 => 'App\\Http\\Controllers\\Api\\UserController',
      ),
      5 => 
      array (
        0 => 'App\\Http\\Controllers\\Api\\UserController',
        1 => 'destroy',
        2 => 'App\\Http\\Controllers\\Api\\UserController',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Middleware\\RoleMiddleware.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 39,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Traits\\ApiResponses',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\Api\\LoginRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\Api\\LoginRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\Api\\LoginRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\Api\\LoginRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\Api\\LoginRequest',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StorePatientRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StorePatientRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\StorePatientRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\StorePatientRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StorePatientRequest',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StoreSocioeconomicRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreSocioeconomicRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\StoreSocioeconomicRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\StoreSocioeconomicRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreSocioeconomicRequest',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StoreVisitRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\StoreVisitRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\StoreVisitRequest',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\UpdatePatientRequest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Http\\Requests\\UpdatePatientRequest',
        1 => 'authorize',
        2 => 'App\\Http\\Requests\\UpdatePatientRequest',
      ),
      1 => 
      array (
        0 => 'App\\Http\\Requests\\UpdatePatientRequest',
        1 => 'rules',
        2 => 'App\\Http\\Requests\\UpdatePatientRequest',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Patient.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\Patient',
        1 => 'casts',
        2 => 'App\\Models\\Patient',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\PatientSocioeconomic.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\PatientSocioeconomic',
        1 => 'casts',
        2 => 'App\\Models\\PatientSocioeconomic',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\User.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\User',
        1 => 'casts',
        2 => 'App\\Models\\User',
      ),
      1 => 
      array (
        0 => 'App\\Models\\User',
        1 => 'isAdmin',
        2 => 'App\\Models\\User',
      ),
      2 => 
      array (
        0 => 'App\\Models\\User',
        1 => 'isDoctor',
        2 => 'App\\Models\\User',
      ),
      3 => 
      array (
        0 => 'App\\Models\\User',
        1 => 'isPatient',
        2 => 'App\\Models\\User',
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'Illuminate\\Notifications\\Notifiable',
        2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
        3 => 'Laravel\\Sanctum\\HasApiTokens',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Visit.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\UserPolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\UserPolicy',
        1 => 'create',
        2 => 'App\\Policies\\UserPolicy',
      ),
      1 => 
      array (
        0 => 'App\\Policies\\UserPolicy',
        1 => 'update',
        2 => 'App\\Policies\\UserPolicy',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Traits\\ApiResponses.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitDeclarationCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Traits\\ApiResponses',
        1 => 7,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\api.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'get',
        2 => 7,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'group',
        2 => 11,
      ),
      2 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'get',
        2 => 15,
      ),
      3 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'get',
        2 => 18,
      ),
      4 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'get',
        2 => 22,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\web.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'welcome',
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Route',
        1 => 'get',
        2 => 5,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'test',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'it',
        1 => 18,
      ),
      2 => 
      array (
        0 => 'it',
        1 => 37,
      ),
      3 => 
      array (
        0 => 'it',
        1 => 56,
      ),
      4 => 
      array (
        0 => 'it',
        1 => 61,
      ),
      5 => 
      array (
        0 => 'it',
        1 => 66,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toMatchArray',
        2 => 30,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 48,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Traits\\ApiResponses',
      ),
      1 => 
      array (
        0 => 'App\\Traits\\ApiResponses',
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 9,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 27,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 42,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 51,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 56,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 64,
      ),
      7 => 
      array (
        0 => 'test',
        1 => 74,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 6,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 8,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 14,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 21,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 27,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 36,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 6,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 8,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 15,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 24,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 29,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 36,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 45,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'beforeEach',
        1 => 6,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 25,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 34,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 42,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 50,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 59,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 67,
      ),
      7 => 
      array (
        0 => 'test',
        1 => 76,
      ),
      8 => 
      array (
        0 => 'test',
        1 => 85,
      ),
      9 => 
      array (
        0 => 'test',
        1 => 94,
      ),
      10 => 
      array (
        0 => 'test',
        1 => 102,
      ),
      11 => 
      array (
        0 => 'test',
        1 => 108,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'test',
        1 => 8,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 24,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 34,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 47,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 56,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 68,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 77,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 13,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 28,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 41,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 52,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 60,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 63,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 72,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 82,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'test',
        1 => 8,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 13,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 20,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 28,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 35,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 41,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 47,
      ),
      7 => 
      array (
        0 => 'test',
        1 => 56,
      ),
      8 => 
      array (
        0 => 'test',
        1 => 61,
      ),
      9 => 
      array (
        0 => 'test',
        1 => 66,
      ),
      10 => 
      array (
        0 => 'test',
        1 => 74,
      ),
      11 => 
      array (
        0 => 'test',
        1 => 80,
      ),
      12 => 
      array (
        0 => 'test',
        1 => 86,
      ),
      13 => 
      array (
        0 => 'test',
        1 => 92,
      ),
      14 => 
      array (
        0 => 'test',
        1 => 102,
      ),
      15 => 
      array (
        0 => 'test',
        1 => 108,
      ),
      16 => 
      array (
        0 => 'test',
        1 => 113,
      ),
      17 => 
      array (
        0 => 'test',
        1 => 119,
      ),
      18 => 
      array (
        0 => 'test',
        1 => 128,
      ),
      19 => 
      array (
        0 => 'test',
        1 => 136,
      ),
      20 => 
      array (
        0 => 'test',
        1 => 144,
      ),
      21 => 
      array (
        0 => 'test',
        1 => 152,
      ),
      22 => 
      array (
        0 => 'test',
        1 => 162,
      ),
      23 => 
      array (
        0 => 'test',
        1 => 169,
      ),
      24 => 
      array (
        0 => 'test',
        1 => 176,
      ),
      25 => 
      array (
        0 => 'test',
        1 => 183,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 11,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 16,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 23,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 32,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 39,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 45,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 51,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 59,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 64,
      ),
      9 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 69,
      ),
      10 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 78,
      ),
      11 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 84,
      ),
      12 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 90,
      ),
      13 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 96,
      ),
      14 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 106,
      ),
      15 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 112,
      ),
      16 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 117,
      ),
      17 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 123,
      ),
      18 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 133,
      ),
      19 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 141,
      ),
      20 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 149,
      ),
      21 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 157,
      ),
      22 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 166,
      ),
      23 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 173,
      ),
      24 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 180,
      ),
      25 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 187,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicFactoryTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 10,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 17,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 22,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 29,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 52,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 58,
      ),
      7 => 
      array (
        0 => 'test',
        1 => 64,
      ),
      8 => 
      array (
        0 => 'test',
        1 => 70,
      ),
      9 => 
      array (
        0 => 'test',
        1 => 76,
      ),
      10 => 
      array (
        0 => 'test',
        1 => 82,
      ),
      11 => 
      array (
        0 => 'test',
        1 => 88,
      ),
      12 => 
      array (
        0 => 'test',
        1 => 94,
      ),
      13 => 
      array (
        0 => 'test',
        1 => 100,
      ),
      14 => 
      array (
        0 => 'test',
        1 => 107,
      ),
      15 => 
      array (
        0 => 'test',
        1 => 112,
      ),
      16 => 
      array (
        0 => 'test',
        1 => 117,
      ),
      17 => 
      array (
        0 => 'test',
        1 => 123,
      ),
      18 => 
      array (
        0 => 'test',
        1 => 129,
      ),
      19 => 
      array (
        0 => 'test',
        1 => 136,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 12,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 19,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 25,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 32,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 49,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 55,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 61,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 67,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 73,
      ),
      9 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 79,
      ),
      10 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 85,
      ),
      11 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 91,
      ),
      12 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 97,
      ),
      13 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 103,
      ),
      14 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 109,
      ),
      15 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 114,
      ),
      16 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeIn',
        2 => 119,
      ),
      17 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 126,
      ),
      18 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 131,
      ),
      19 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 138,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 11,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 20,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 52,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 58,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 96,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 103,
      ),
      7 => 
      array (
        0 => 'test',
        1 => 110,
      ),
      8 => 
      array (
        0 => 'test',
        1 => 117,
      ),
      9 => 
      array (
        0 => 'test',
        1 => 126,
      ),
      10 => 
      array (
        0 => 'test',
        1 => 134,
      ),
      11 => 
      array (
        0 => 'test',
        1 => 144,
      ),
      12 => 
      array (
        0 => 'test',
        1 => 152,
      ),
      13 => 
      array (
        0 => 'test',
        1 => 161,
      ),
      14 => 
      array (
        0 => 'test',
        1 => 172,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 16,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 43,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toThrow',
        2 => 53,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 83,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 100,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 107,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 114,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 121,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 130,
      ),
      9 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 140,
      ),
      10 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 147,
      ),
      11 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toThrow',
        2 => 156,
      ),
      12 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 167,
      ),
      13 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 192,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicPolicyTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 8,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 11,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 16,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 21,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 27,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 34,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 41,
      ),
      7 => 
      array (
        0 => 'test',
        1 => 48,
      ),
      8 => 
      array (
        0 => 'test',
        1 => 56,
      ),
      9 => 
      array (
        0 => 'test',
        1 => 61,
      ),
      10 => 
      array (
        0 => 'test',
        1 => 66,
      ),
      11 => 
      array (
        0 => 'test',
        1 => 72,
      ),
      12 => 
      array (
        0 => 'test',
        1 => 79,
      ),
      13 => 
      array (
        0 => 'test',
        1 => 86,
      ),
      14 => 
      array (
        0 => 'test',
        1 => 94,
      ),
      15 => 
      array (
        0 => 'test',
        1 => 101,
      ),
      16 => 
      array (
        0 => 'test',
        1 => 108,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 13,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 18,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 23,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 31,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 38,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 45,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 52,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 58,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 63,
      ),
      9 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 68,
      ),
      10 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 76,
      ),
      11 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 83,
      ),
      12 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 90,
      ),
      13 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 98,
      ),
      14 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 105,
      ),
      15 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 112,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\SocioeconomicMigrationTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'uses',
        1 => 5,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 7,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 11,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 15,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 19,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 23,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 27,
      ),
      7 => 
      array (
        0 => 'test',
        1 => 31,
      ),
      8 => 
      array (
        0 => 'test',
        1 => 35,
      ),
      9 => 
      array (
        0 => 'test',
        1 => 39,
      ),
      10 => 
      array (
        0 => 'test',
        1 => 43,
      ),
      11 => 
      array (
        0 => 'test',
        1 => 47,
      ),
      12 => 
      array (
        0 => 'test',
        1 => 51,
      ),
      13 => 
      array (
        0 => 'test',
        1 => 55,
      ),
      14 => 
      array (
        0 => 'test',
        1 => 59,
      ),
      15 => 
      array (
        0 => 'test',
        1 => 63,
      ),
      16 => 
      array (
        0 => 'test',
        1 => 67,
      ),
      17 => 
      array (
        0 => 'test',
        1 => 71,
      ),
      18 => 
      array (
        0 => 'test',
        1 => 75,
      ),
      19 => 
      array (
        0 => 'test',
        1 => 79,
      ),
      20 => 
      array (
        0 => 'test',
        1 => 83,
      ),
      21 => 
      array (
        0 => 'test',
        1 => 87,
      ),
      22 => 
      array (
        0 => 'test',
        1 => 91,
      ),
      23 => 
      array (
        0 => 'test',
        1 => 99,
      ),
      24 => 
      array (
        0 => 'test',
        1 => 107,
      ),
      25 => 
      array (
        0 => 'test',
        1 => 115,
      ),
      26 => 
      array (
        0 => 'test',
        1 => 123,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 8,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 12,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 16,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 20,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 24,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 28,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 32,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 36,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 40,
      ),
      9 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 44,
      ),
      10 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 48,
      ),
      11 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 52,
      ),
      12 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 56,
      ),
      13 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 60,
      ),
      14 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 64,
      ),
      15 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 68,
      ),
      16 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 72,
      ),
      17 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 76,
      ),
      18 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 80,
      ),
      19 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 84,
      ),
      20 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 88,
      ),
      21 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 95,
      ),
      22 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 103,
      ),
      23 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 111,
      ),
      24 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 119,
      ),
      25 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeEmpty',
        2 => 136,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'test',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 14,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 21,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 34,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 42,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 48,
      ),
      6 => 
      array (
        0 => 'test',
        1 => 54,
      ),
      7 => 
      array (
        0 => 'test',
        1 => 62,
      ),
      8 => 
      array (
        0 => 'test',
        1 => 68,
      ),
      9 => 
      array (
        0 => 'test',
        1 => 74,
      ),
      10 => 
      array (
        0 => 'test',
        1 => 80,
      ),
      11 => 
      array (
        0 => 'test',
        1 => 88,
      ),
      12 => 
      array (
        0 => 'test',
        1 => 94,
      ),
      13 => 
      array (
        0 => 'test',
        1 => 100,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 11,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 18,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 26,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 39,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 45,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 51,
      ),
      6 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 57,
      ),
      7 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 65,
      ),
      8 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 71,
      ),
      9 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 77,
      ),
      10 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 84,
      ),
      11 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 91,
      ),
      12 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 97,
      ),
      13 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeFalse',
        2 => 104,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsFactoryTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'test',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 16,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 22,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 28,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeNull',
        2 => 10,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 19,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeInstanceOf',
        2 => 25,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBe',
        2 => 32,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsMigrationTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'test',
        1 => 3,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 8,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 16,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 4,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 11,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeTrue',
        2 => 18,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'test',
        1 => 7,
      ),
      1 => 
      array (
        0 => 'test',
        1 => 24,
      ),
      2 => 
      array (
        0 => 'test',
        1 => 31,
      ),
      3 => 
      array (
        0 => 'test',
        1 => 38,
      ),
      4 => 
      array (
        0 => 'test',
        1 => 44,
      ),
      5 => 
      array (
        0 => 'test',
        1 => 53,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeInstanceOf',
        2 => 17,
      ),
      1 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toBeInstanceOf',
        2 => 27,
      ),
      2 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toEqual',
        2 => 34,
      ),
      3 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toEqual',
        2 => 41,
      ),
      4 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toEqual',
        2 => 47,
      ),
      5 => 
      array (
        0 => 
        array (
          0 => 'Pest\\Mixins\\Expectation',
        ),
        1 => 'toEqual',
        2 => 58,
      ),
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Pest.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\FunctionWithoutImpurePointsCollector' => 
    array (
      0 => 'something',
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureMethodCallCollector' => 
    array (
      0 => 
      array (
        0 => 
        array (
          0 => 'Pest\\PendingCalls\\UsesCall',
        ),
        1 => 'in',
        2 => 14,
      ),
    ),
  ),
); },
	'dependencies' => array (
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\ApiController.php' => 
  array (
    'fileHash' => '3c43facd995c5f187d6621b8ed865e890683b6f1dd9c08add72ee2f24278099d',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\AuthController.php',
      1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\UserController.php',
      2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\api.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\AuthController.php' => 
  array (
    'fileHash' => '4502b237b454dae2f02e36ff40d98f7203f28e224f128cba5f471779dccaaf10',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\api.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\UserController.php' => 
  array (
    'fileHash' => '1087bcaede6d9fe840d51e8ff9f20f059bf444fa4344ad13e09442b1b19f0535',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\api.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Controller.php' => 
  array (
    'fileHash' => '25d1c1ef8e6cc8a376553faacfba2b07d9dfaee9bdbb84f14f77517580e9deb1',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\ApiController.php',
      1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\AuthController.php',
      2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\UserController.php',
      3 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\api.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Middleware\\RoleMiddleware.php' => 
  array (
    'fileHash' => '39ab336b87ec2da448218c1028a908bd4c1b618abea4e5f5bd0cf09e825f300e',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\Api\\LoginRequest.php' => 
  array (
    'fileHash' => '085ad9dfcd2f4aeb81c76fb1e386e8c26584b6661073740e88d003bfd138cf1a',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\AuthController.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StorePatientRequest.php' => 
  array (
    'fileHash' => 'b79ea0df8d4fb3de9552d59aa49c10840928da301f210cb442fb869f4ec2b172',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StoreSocioeconomicRequest.php' => 
  array (
    'fileHash' => 'e54f829998cf97ff63561edb9431bd379b37b857097e0dbc918ba3d3b7f1dc24',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StoreVisitRequest.php' => 
  array (
    'fileHash' => '061a4c11d78a74660c32d786fe2a8fa89552abbdf5ac48062f8275a637b60814',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\UpdatePatientRequest.php' => 
  array (
    'fileHash' => '937d9f5d58ac72a2ac8e8f63c4e3eca7d1cf610bf056fb0df2efb362f867df8b',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Patient.php' => 
  array (
    'fileHash' => 'a8f5edc5369234564003d965a321b3bd6ab61c0879e68cc1579a0478679aadc9',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\PatientSocioeconomic.php',
      1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\User.php',
      2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Visit.php',
      3 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientPolicy.php',
      4 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
      5 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
      6 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicFactoryTest.php',
      7 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
      8 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicPolicyTest.php',
      9 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
      10 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsFactoryTest.php',
      11 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\PatientSocioeconomic.php' => 
  array (
    'fileHash' => 'e05bbeee1a19cb47a0210b2eb365058b737702ebe9a31209d02275d878348e36',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Patient.php',
      1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientSocioeconomicPolicy.php',
      2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicFactoryTest.php',
      3 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php',
      4 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicPolicyTest.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\User.php' => 
  array (
    'fileHash' => 'c4e5fd775762172ec4dbb834a5dbc039c391dc21e0ac4e028bf1470f514383af',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\AuthController.php',
      1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\UserController.php',
      2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Middleware\\RoleMiddleware.php',
      3 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StoreVisitRequest.php',
      4 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Patient.php',
      5 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Visit.php',
      6 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientPolicy.php',
      7 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientSocioeconomicPolicy.php',
      8 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\UserPolicy.php',
      9 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php',
      10 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
      11 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php',
      12 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php',
      13 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php',
      14 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php',
      15 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php',
      16 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php',
      17 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicPolicyTest.php',
      18 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
      19 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Visit.php' => 
  array (
    'fileHash' => '3e7add6483992694989106a92e09a988502045e583a24623ecdc16abcbe20b8c',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StoreVisitRequest.php',
      1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Patient.php',
      2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php',
      3 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php',
      4 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsFactoryTest.php',
      5 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientPolicy.php' => 
  array (
    'fileHash' => '00f9855213e0e257d7875ae55da500c4b2374c4ae655cfc22d77d5684f9278e7',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientSocioeconomicPolicy.php' => 
  array (
    'fileHash' => 'dc8a4b66c93eb845b41e3ae00aa27d719aac28e2c896bed4a47e2f954f1850cd',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\UserPolicy.php' => 
  array (
    'fileHash' => 'e64dc6292725bea611494434444ec2bb6e818b376d08bf278dcfe94b64e39bbe',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php' => 
  array (
    'fileHash' => 'ef53221c800fe50a088c4de32386f77e3eb4bb680f60134d7ed34e3899e11d43',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Providers\\AppServiceProvider.php' => 
  array (
    'fileHash' => 'caf306ef6a25a547bbb7edd5508a39b51c365083eb6dd58378cf2242018e94c7',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Traits\\ApiResponses.php' => 
  array (
    'fileHash' => 'bd1a196f084e39d39a945f94348233b4faac057851737d58f5c1a6fe7d8cdffd',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\ApiController.php',
      1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\AuthController.php',
      2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\UserController.php',
      3 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Middleware\\RoleMiddleware.php',
      4 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\api.php',
      5 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
    ),
    'usedTraitDependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\ApiController.php',
      1 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\AuthController.php',
      2 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\UserController.php',
      3 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Middleware\\RoleMiddleware.php',
      4 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php',
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\helpers.php' => 
  array (
    'fileHash' => 'f6a3b0e69463e962c857c9bcd8529702235f9bbf235a48ffc6cbf1b43fe6d94a',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\api.php' => 
  array (
    'fileHash' => 'f71ab38a0248913df721f54955dbbb6405c3675bca8ddf02b375e6050364a431',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\console.php' => 
  array (
    'fileHash' => '9adccc33e7dd400683e434774077c7fdb2f299c5712cedf16a43fdf56f2850fa',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\routes\\web.php' => 
  array (
    'fileHash' => '248c7eeeb43bb61ee4fb6603e52b44843c64f72a841d671bcd41871aab23f970',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ApiRoutesTest.php' => 
  array (
    'fileHash' => '0e296e9bf24adbfb2cbee585ae8808b658f96e5572217da387b36cef7247983e',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\AuthTest.php' => 
  array (
    'fileHash' => 'e98e18e624ad3091ea1b29e6caaa198cd9d716566d9572dd06e98fbe6f09c13c',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\ResponseContractTest.php' => 
  array (
    'fileHash' => '59dffada82760c689640a956368f6c13a2910705c6992b56b1bc0c88005692ee',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Api\\RoleAccessTest.php' => 
  array (
    'fileHash' => '7d01b74758e86a92638426d64db01aba18c164514238647dd820c7576fce7ec3',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Middleware\\RoleMiddlewareTest.php' => 
  array (
    'fileHash' => '147b390e1638ea9efb78d95fc3046e3c25458ecc3e70153853e28ac62dfb75b6',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientModelTest.php' => 
  array (
    'fileHash' => '8415840bd1384fbf2e896cf856050f4caf2bf95a7863a5b30085cb146865d739',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\PatientPolicyTest.php' => 
  array (
    'fileHash' => '08f148a3d99f8916c39796219eaf3863691916b4a4e160d03caef5304c94d7b2',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicFactoryTest.php' => 
  array (
    'fileHash' => '95c135b38dd51956ae8604165e53f03160d78af9f6731a5f6d83190d59ff312e',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicModelTest.php' => 
  array (
    'fileHash' => '81b33852087b1c55920480e21740d3af74a9aeb4fd918dcdf9edf955345f2658',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\PatientSocioeconomicPolicyTest.php' => 
  array (
    'fileHash' => 'ad39c72d34f63547c2753033ab1d1eb1f2fb27474353724a82df06f90007fae4',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\Socioeconomic\\SocioeconomicMigrationTest.php' => 
  array (
    'fileHash' => '3a153f6ba7eaaa87de6d18839c0bcc9bd712a8ec06b4b66cfad529b0b6c6e162',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitPolicyTest.php' => 
  array (
    'fileHash' => 'ff69b658a5af70f8702688542829e0e61fb5018fcbd912fdac0814daf050587c',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsFactoryTest.php' => 
  array (
    'fileHash' => '93f266390449b0962e19a4ee105aeb1c9d2d0f351e38dec23dc3ab43b512154d',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsMigrationTest.php' => 
  array (
    'fileHash' => '140631606716f9b4557c9937f0679701f189464ea1cae5f7a80cc296739855a2',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Feature\\visits\\VisitsModelTest.php' => 
  array (
    'fileHash' => 'd3fbc9d2901f2b4aefb850a44497d238ac3e7d133bcb7356c112a8a16df30fa3',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Pest.php' => 
  array (
    'fileHash' => 'fc6be82d1fdbcd0f3c6216063cbed092de47caf5bd77606b76e45a86363c7917',
    'dependentFiles' => 
    array (
    ),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\TestCase.php' => 
  array (
    'fileHash' => 'c7c209f5579c42647c1f7b2a79ac828e8b54ac62a98442045a6e94fa2c0d0ebc',
    'dependentFiles' => 
    array (
      0 => 'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Pest.php',
    ),
  ),
),
	'exportedNodesCallback' => static function (): array { return array (
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\ApiController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Api\\ApiController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Traits\\ApiResponses',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\AuthController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Api\\AuthController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Api\\ApiController',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'login',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'App\\Http\\Requests\\Api\\LoginRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'logout',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Api\\UserController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Api\\UserController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Api\\ApiController',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'index',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Display a listing of the resource.
     */',
             'namespace' => 'App\\Http\\Controllers\\Api',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'user' => 'App\\Models\\User',
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Show the form for creating a new resource.
     */',
             'namespace' => 'App\\Http\\Controllers\\Api',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'user' => 'App\\Models\\User',
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Store a newly created resource in storage.
     */',
             'namespace' => 'App\\Http\\Controllers\\Api',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'user' => 'App\\Models\\User',
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'show',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Display the specified resource.
     */',
             'namespace' => 'App\\Http\\Controllers\\Api',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'user' => 'App\\Models\\User',
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'edit',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Show the form for editing the specified resource.
     */',
             'namespace' => 'App\\Http\\Controllers\\Api',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'user' => 'App\\Models\\User',
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Update the specified resource in storage.
     */',
             'namespace' => 'App\\Http\\Controllers\\Api',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'user' => 'App\\Models\\User',
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'destroy',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Remove the specified resource from storage.
     */',
             'namespace' => 'App\\Http\\Controllers\\Api',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'user' => 'App\\Models\\User',
              'request' => 'Illuminate\\Http\\Request',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Controllers\\Controller.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Controller',
       'phpDoc' => NULL,
       'abstract' => true,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Middleware\\RoleMiddleware.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Middleware\\RoleMiddleware',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Traits\\ApiResponses',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */',
             'namespace' => 'App\\Http\\Middleware',
             'uses' => 
            array (
              'apiresponses' => 'App\\Traits\\ApiResponses',
              'closure' => 'Closure',
              'request' => 'Illuminate\\Http\\Request',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
              'log' => 'Illuminate\\Support\\Facades\\Log',
              'response' => 'Symfony\\Component\\HttpFoundation\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Symfony\\Component\\HttpFoundation\\Response',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'next',
               'type' => 'Closure',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'roles',
               'type' => 'string',
               'byRef' => false,
               'variadic' => true,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\Api\\LoginRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\Api\\LoginRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests\\Api',
             'uses' => 
            array (
              'validationrule' => 'Illuminate\\Contracts\\Validation\\ValidationRule',
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests\\Api',
             'uses' => 
            array (
              'validationrule' => 'Illuminate\\Contracts\\Validation\\ValidationRule',
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StorePatientRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StorePatientRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StoreSocioeconomicRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreSocioeconomicRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     * Authorization is handled in the Livewire component.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\StoreVisitRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\StoreVisitRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Http\\Requests\\UpdatePatientRequest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Requests\\UpdatePatientRequest',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Http\\FormRequest',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authorize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine if the user is authorized to make this request.
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rules',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \\Illuminate\\Contracts\\Validation\\ValidationRule|array<mixed>|string>
     */',
             'namespace' => 'App\\Http\\Requests',
             'uses' => 
            array (
              'formrequest' => 'Illuminate\\Foundation\\Http\\FormRequest',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Patient.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Patient',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'user',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fullName',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Casts\\Attribute',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'socioeconomic',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'visits',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\PatientSocioeconomic.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\PatientSocioeconomic',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'table',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'patient',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\User.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\User',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Auth\\User',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        1 => 'Illuminate\\Notifications\\Notifiable',
        2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
        3 => 'Laravel\\Sanctum\\HasApiTokens',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'hidden',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'initials',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the user\'s initials
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isAdmin',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Check if user is admin
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isDoctor',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Check if user is doctor
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isPatient',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Check if user is patient
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'softdeletes' => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'str' => 'Illuminate\\Support\\Str',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'patient',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toSimpleData',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
           'args' => 
          array (
            0 => '[\'name\', \'email\', \'password\']',
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
           'args' => 
          array (
            0 => '[\'password\', \'remember_token\']',
          ),
        )),
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Models\\Visit.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Visit',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'patient',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'doctor',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\PatientPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'viewAny',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can view any models.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patient' => 'App\\Models\\Patient',
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can view the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patient' => 'App\\Models\\Patient',
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'patient',
               'type' => 'App\\Models\\Patient',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can create models.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patient' => 'App\\Models\\Patient',
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can update the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patient' => 'App\\Models\\Patient',
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'patient',
               'type' => 'App\\Models\\Patient',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can delete the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patient' => 'App\\Models\\Patient',
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'patient',
               'type' => 'App\\Models\\Patient',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'restore',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can restore the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patient' => 'App\\Models\\Patient',
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'patient',
               'type' => 'App\\Models\\Patient',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'forceDelete',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can permanently delete the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patient' => 'App\\Models\\Patient',
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'patient',
               'type' => 'App\\Models\\Patient',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\PatientSocioeconomicPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\PatientSocioeconomicPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'viewAny',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can view any models.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patientsocioeconomic' => 'App\\Models\\PatientSocioeconomic',
              'user' => 'App\\Models\\User',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can view the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patientsocioeconomic' => 'App\\Models\\PatientSocioeconomic',
              'user' => 'App\\Models\\User',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'socioeconomic',
               'type' => 'App\\Models\\PatientSocioeconomic',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can create models.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'patientsocioeconomic' => 'App\\Models\\PatientSocioeconomic',
              'user' => 'App\\Models\\User',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'socioeconomic',
               'type' => 'App\\Models\\PatientSocioeconomic',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'socioeconomic',
               'type' => 'App\\Models\\PatientSocioeconomic',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\UserPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\UserPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can create models.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can update the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can delete the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'response' => 'Illuminate\\Auth\\Access\\Response',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'userToDelete',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Policies\\VisitPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\VisitPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'viewAny',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can view any models.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'visit' => 'App\\Models\\Visit',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can view the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'visit' => 'App\\Models\\Visit',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'visit',
               'type' => 'App\\Models\\Visit',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can create models.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'visit' => 'App\\Models\\Visit',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can update the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'visit' => 'App\\Models\\Visit',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'visit',
               'type' => 'App\\Models\\Visit',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can delete the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'visit' => 'App\\Models\\Visit',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'visit',
               'type' => 'App\\Models\\Visit',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'restore',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can restore the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'visit' => 'App\\Models\\Visit',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'visit',
               'type' => 'App\\Models\\Visit',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'forceDelete',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Determine whether the user can permanently delete the model.
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'visit' => 'App\\Models\\Visit',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'visit',
               'type' => 'App\\Models\\Visit',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'adminOrDoctor',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param User $user
     * @return true|void
     */',
             'namespace' => 'App\\Policies',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'visit' => 'App\\Models\\Visit',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Providers\\AppServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\AppServiceProvider',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Support\\ServiceProvider',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Register any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'boot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bootstrap any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\Traits\\ApiResponses.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedTraitNode::__set_state(array(
       'name' => 'App\\Traits\\ApiResponses',
       'phpDoc' => NULL,
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'success',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'statusCode',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'ok',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'created',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'noContent',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'error',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'message',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'statusCode',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\app\\helpers.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedFunctionNode::__set_state(array(
       'name' => 'format_enum_label',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
     * Convert a snake_case enum value to a human-readable label, or return \'N/A\' when null/empty.
     */',
         'namespace' => NULL,
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'byRef' => false,
       'returnType' => 'string',
       'parameters' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
           'name' => 'value',
           'type' => '?string',
           'byRef' => false,
           'variadic' => false,
           'hasDefault' => false,
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\Pest.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedFunctionNode::__set_state(array(
       'name' => 'something',
       'phpDoc' => NULL,
       'byRef' => false,
       'returnType' => NULL,
       'parameters' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'D:\\_Learn\\_PhpstormProjects\\nutri-ledger\\backend\\tests\\TestCase.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Tests\\TestCase',
       'phpDoc' => NULL,
       'abstract' => true,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Testing\\TestCase',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
); },
];
