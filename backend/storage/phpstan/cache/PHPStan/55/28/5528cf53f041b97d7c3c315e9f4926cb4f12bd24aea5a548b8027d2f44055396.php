<?php declare(strict_types = 1);

// osfsl-D:/_Learn/_PhpstormProjects/nutri-ledger/backend/vendor/composer/../laravel/framework/src/Illuminate/Foundation/Testing/Concerns/InteractsWithTime.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Foundation\Testing\Concerns\InteractsWithTime
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-d126c91dd2b8a0cc1d8231741340c74e3017b193043c9d0b1f4d1aa860c41d56-8.3.21-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'filename' => 'D:/_Learn/_PhpstormProjects/nutri-ledger/backend/vendor/composer/../laravel/framework/src/Illuminate/Foundation/Testing/Concerns/InteractsWithTime.php',
      ),
    ),
    'namespace' => 'Illuminate\\Foundation\\Testing\\Concerns',
    'name' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
    'shortName' => 'InteractsWithTime',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 8,
    'endLine' => 80,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'freezeTime' => 
      array (
        'name' => 'freezeTime',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 18,
                'endLine' => 18,
                'startTokenPos' => 35,
                'startFilePos' => 414,
                'endTokenPos' => 35,
                'endFilePos' => 417,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 18,
            'endLine' => 18,
            'startColumn' => 32,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @template TReturn of mixed
 *
 * Freeze time.
 *
 * @param  (callable(): TReturn)|null  $callback
 * @return ($callback is null ? \\Illuminate\\Support\\Carbon : TReturn)
 */',
        'startLine' => 18,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Testing\\Concerns',
        'declaringClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'implementingClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'currentClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'aliasName' => NULL,
      ),
      'freezeSecond' => 
      array (
        'name' => 'freezeSecond',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 33,
                'endLine' => 33,
                'startTokenPos' => 93,
                'startFilePos' => 849,
                'endTokenPos' => 93,
                'endFilePos' => 852,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 33,
            'endLine' => 33,
            'startColumn' => 34,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @template TReturn of mixed
 *
 * Freeze time at the beginning of the current second.
 *
 * @param  (callable(): TReturn)|null  $callback
 * @return ($callback is null ? \\Illuminate\\Support\\Carbon : TReturn)
 */',
        'startLine' => 33,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Testing\\Concerns',
        'declaringClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'implementingClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'currentClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'aliasName' => NULL,
      ),
      'travel' => 
      array (
        'name' => 'travel',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 46,
            'endLine' => 46,
            'startColumn' => 28,
            'endColumn' => 33,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Begin travelling to another time.
 *
 * @param  int  $value
 * @return \\Illuminate\\Foundation\\Testing\\Wormhole
 */',
        'startLine' => 46,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Testing\\Concerns',
        'declaringClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'implementingClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'currentClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'aliasName' => NULL,
      ),
      'travelTo' => 
      array (
        'name' => 'travelTo',
        'parameters' => 
        array (
          'date' => 
          array (
            'name' => 'date',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 30,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 60,
                'endLine' => 60,
                'startTokenPos' => 183,
                'startFilePos' => 1579,
                'endTokenPos' => 183,
                'endFilePos' => 1582,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 37,
            'endColumn' => 52,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @template TReturn of mixed
 *
 * Travel to another time.
 *
 * @param  \\DateTimeInterface|\\Closure|\\Illuminate\\Support\\Carbon|string|bool|null  $date
 * @param  (callable(): TReturn)|null  $callback
 * @return ($callback is null ? void : TReturn)
 */',
        'startLine' => 60,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Testing\\Concerns',
        'declaringClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'implementingClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'currentClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'aliasName' => NULL,
      ),
      'travelBack' => 
      array (
        'name' => 'travelBack',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Travel back to the current time.
 *
 * @return \\DateTimeInterface
 */',
        'startLine' => 76,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Foundation\\Testing\\Concerns',
        'declaringClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'implementingClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'currentClassName' => 'Illuminate\\Foundation\\Testing\\Concerns\\InteractsWithTime',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));