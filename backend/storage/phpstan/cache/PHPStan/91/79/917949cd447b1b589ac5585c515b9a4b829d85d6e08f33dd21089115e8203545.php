<?php declare(strict_types = 1);

// osfsl-D:/_Learn/_PhpstormProjects/nutri-ledger/backend/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasTimestamps.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\Eloquent\Concerns\HasTimestamps
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-2bf3bb3341f668f3ce5a6ad4f8f61d19b71b1cd223a85b5241e80a99ac2b4493-8.3.21-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'filename' => 'D:/_Learn/_PhpstormProjects/nutri-ledger/backend/vendor/composer/../laravel/framework/src/Illuminate/Database/Eloquent/Concerns/HasTimestamps.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
    'name' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
    'shortName' => 'HasTimestamps',
    'isInterface' => false,
    'isTrait' => true,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 253,
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
      'timestamps' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'name' => 'timestamps',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 17,
            'endLine' => 17,
            'startTokenPos' => 41,
            'startFilePos' => 370,
            'endTokenPos' => 41,
            'endFilePos' => 373,
          ),
        ),
        'docComment' => '/**
 * Indicates if the model should be timestamped.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 17,
        'endLine' => 17,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'ignoreTimestampsOn' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'name' => 'ignoreTimestampsOn',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 54,
            'startFilePos' => 538,
            'endTokenPos' => 55,
            'endFilePos' => 539,
          ),
        ),
        'docComment' => '/**
 * The list of models classes that have timestamps temporarily disabled.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 46,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'initializeHasTimestamps' => 
      array (
        'name' => 'initializeHasTimestamps',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Attributes\\Initialize',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'docComment' => '/**
 * Initialize the HasTimestamps trait.
 *
 * @return void
 */',
        'startLine' => 31,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'touch' => 
      array (
        'name' => 'touch',
        'parameters' => 
        array (
          'attribute' => 
          array (
            'name' => 'attribute',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 47,
                'endLine' => 47,
                'startTokenPos' => 148,
                'startFilePos' => 1104,
                'endTokenPos' => 148,
                'endFilePos' => 1107,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 47,
            'endLine' => 47,
            'startColumn' => 27,
            'endColumn' => 43,
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
 * Update the model\'s update timestamp.
 *
 * @param  array|string|null  $attribute
 * @return bool
 */',
        'startLine' => 47,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'touchQuietly' => 
      array (
        'name' => 'touchQuietly',
        'parameters' => 
        array (
          'attribute' => 
          array (
            'name' => 'attribute',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 74,
                'endLine' => 74,
                'startTokenPos' => 264,
                'startFilePos' => 1703,
                'endTokenPos' => 264,
                'endFilePos' => 1706,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 74,
            'endLine' => 74,
            'startColumn' => 34,
            'endColumn' => 50,
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
 * Update the model\'s update timestamp without raising any events.
 *
 * @param  array|string|null  $attribute
 * @return bool
 */',
        'startLine' => 74,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'updateTimestamps' => 
      array (
        'name' => 'updateTimestamps',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Update the creation and update timestamps.
 *
 * @return $this
 */',
        'startLine' => 84,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'setCreatedAt' => 
      array (
        'name' => 'setCreatedAt',
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
            'startLine' => 109,
            'endLine' => 109,
            'startColumn' => 34,
            'endColumn' => 39,
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
 * Set the value of the "created at" attribute.
 *
 * @param  mixed  $value
 * @return $this
 */',
        'startLine' => 109,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'setUpdatedAt' => 
      array (
        'name' => 'setUpdatedAt',
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
            'startLine' => 122,
            'endLine' => 122,
            'startColumn' => 34,
            'endColumn' => 39,
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
 * Set the value of the "updated at" attribute.
 *
 * @param  mixed  $value
 * @return $this
 */',
        'startLine' => 122,
        'endLine' => 127,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'freshTimestamp' => 
      array (
        'name' => 'freshTimestamp',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a fresh timestamp for the model.
 *
 * @return \\Illuminate\\Support\\Carbon
 */',
        'startLine' => 134,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'freshTimestampString' => 
      array (
        'name' => 'freshTimestampString',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a fresh timestamp for the model.
 *
 * @return string
 */',
        'startLine' => 144,
        'endLine' => 147,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'usesTimestamps' => 
      array (
        'name' => 'usesTimestamps',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the model uses timestamps.
 *
 * @return bool
 */',
        'startLine' => 154,
        'endLine' => 157,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'getCreatedAtColumn' => 
      array (
        'name' => 'getCreatedAtColumn',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the "created at" column.
 *
 * @return string|null
 */',
        'startLine' => 164,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'getUpdatedAtColumn' => 
      array (
        'name' => 'getUpdatedAtColumn',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the name of the "updated at" column.
 *
 * @return string|null
 */',
        'startLine' => 174,
        'endLine' => 177,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'getQualifiedCreatedAtColumn' => 
      array (
        'name' => 'getQualifiedCreatedAtColumn',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified "created at" column.
 *
 * @return string|null
 */',
        'startLine' => 184,
        'endLine' => 189,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'getQualifiedUpdatedAtColumn' => 
      array (
        'name' => 'getQualifiedUpdatedAtColumn',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the fully-qualified "updated at" column.
 *
 * @return string|null
 */',
        'startLine' => 196,
        'endLine' => 201,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'withoutTimestamps' => 
      array (
        'name' => 'withoutTimestamps',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 208,
            'endLine' => 208,
            'startColumn' => 46,
            'endColumn' => 63,
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
 * Disable timestamps for the current class during the given callback scope.
 *
 * @return mixed
 */',
        'startLine' => 208,
        'endLine' => 211,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'withoutTimestampsOn' => 
      array (
        'name' => 'withoutTimestampsOn',
        'parameters' => 
        array (
          'models' => 
          array (
            'name' => 'models',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 48,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 220,
            'endLine' => 220,
            'startColumn' => 57,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Disable timestamps for the given model classes during the given callback scope.
 *
 * @param  array  $models
 * @param  callable  $callback
 * @return mixed
 */',
        'startLine' => 220,
        'endLine' => 233,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'aliasName' => NULL,
      ),
      'isIgnoringTimestamps' => 
      array (
        'name' => 'isIgnoringTimestamps',
        'parameters' => 
        array (
          'class' => 
          array (
            'name' => 'class',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 241,
                'endLine' => 241,
                'startTokenPos' => 869,
                'startFilePos' => 5627,
                'endTokenPos' => 869,
                'endFilePos' => 5630,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 241,
            'endLine' => 241,
            'startColumn' => 49,
            'endColumn' => 61,
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
 * Determine if the given model is ignoring timestamps / touches.
 *
 * @param  string|null  $class
 * @return bool
 */',
        'startLine' => 241,
        'endLine' => 252,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database\\Eloquent\\Concerns',
        'declaringClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'implementingClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
        'currentClassName' => 'Illuminate\\Database\\Eloquent\\Concerns\\HasTimestamps',
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