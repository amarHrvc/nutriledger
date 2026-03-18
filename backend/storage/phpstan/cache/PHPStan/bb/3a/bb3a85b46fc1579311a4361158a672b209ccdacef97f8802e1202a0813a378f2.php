<?php declare(strict_types = 1);

// osfsl-D:/_Learn/_PhpstormProjects/nutri-ledger/backend/vendor/composer/../laravel/framework/src/Illuminate/Database/DatabaseManager.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Database\DatabaseManager
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-0d99978319baeed500c4de012a2a810415bfe3ebe6efdcb2b8e4fd37115e9033-8.3.21-6.65.0.9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Database\\DatabaseManager',
        'filename' => 'D:/_Learn/_PhpstormProjects/nutri-ledger/backend/vendor/composer/../laravel/framework/src/Illuminate/Database/DatabaseManager.php',
      ),
    ),
    'namespace' => 'Illuminate\\Database',
    'name' => 'Illuminate\\Database\\DatabaseManager',
    'shortName' => 'DatabaseManager',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @mixin \\Illuminate\\Database\\Connection
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 495,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Database\\ConnectionResolverInterface',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\Macroable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'app' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'name' => 'app',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The application instance.
 *
 * @var \\Illuminate\\Contracts\\Foundation\\Application
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 19,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'factory' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'name' => 'factory',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The database connection factory instance.
 *
 * @var \\Illuminate\\Database\\Connectors\\ConnectionFactory
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'connections' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'name' => 'connections',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 46,
            'endLine' => 46,
            'startTokenPos' => 113,
            'startFilePos' => 1046,
            'endTokenPos' => 114,
            'endFilePos' => 1047,
          ),
        ),
        'docComment' => '/**
 * The active connection instances.
 *
 * @var array<string, \\Illuminate\\Database\\Connection>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 46,
        'endLine' => 46,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'dynamicConnectionConfigurations' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'name' => 'dynamicConnectionConfigurations',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 53,
            'endLine' => 53,
            'startTokenPos' => 125,
            'startFilePos' => 1229,
            'endTokenPos' => 126,
            'endFilePos' => 1230,
          ),
        ),
        'docComment' => '/**
 * The dynamically configured (DB::build) connection configurations.
 *
 * @var array<string, array>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 53,
        'endLine' => 53,
        'startColumn' => 5,
        'endColumn' => 52,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'extensions' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'name' => 'extensions',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 60,
            'endLine' => 60,
            'startTokenPos' => 137,
            'startFilePos' => 1361,
            'endTokenPos' => 138,
            'endFilePos' => 1362,
          ),
        ),
        'docComment' => '/**
 * The custom connection resolvers.
 *
 * @var array<string, callable>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 60,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 31,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'reconnector' => 
      array (
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'name' => 'reconnector',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The callback to be executed to reconnect to a database.
 *
 * @var callable
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 27,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'app' => 
          array (
            'name' => 'app',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 33,
            'endColumn' => 36,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'factory' => 
          array (
            'name' => 'factory',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Connectors\\ConnectionFactory',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 39,
            'endColumn' => 64,
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
 * Create a new database manager instance.
 *
 * @param  \\Illuminate\\Contracts\\Foundation\\Application  $app
 * @param  \\Illuminate\\Database\\Connectors\\ConnectionFactory  $factory
 */',
        'startLine' => 75,
        'endLine' => 85,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'connection' => 
      array (
        'name' => 'connection',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 93,
                'endLine' => 93,
                'startTokenPos' => 238,
                'startFilePos' => 2251,
                'endTokenPos' => 238,
                'endFilePos' => 2254,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 93,
            'endLine' => 93,
            'startColumn' => 32,
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
 * Get a database connection instance.
 *
 * @param  \\UnitEnum|string|null  $name
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 93,
        'endLine' => 109,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'build' => 
      array (
        'name' => 'build',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 117,
            'endLine' => 117,
            'startColumn' => 27,
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
 * Build a database connection instance from the given configuration.
 *
 * @param  array  $config
 * @return \\Illuminate\\Database\\ConnectionInterface
 */',
        'startLine' => 117,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'calculateDynamicConnectionName' => 
      array (
        'name' => 'calculateDynamicConnectionName',
        'parameters' => 
        array (
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 132,
            'endLine' => 132,
            'startColumn' => 59,
            'endColumn' => 71,
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
 * Calculate the dynamic connection name for an on-demand connection based on its configuration.
 *
 * @param  array  $config
 * @return string
 */',
        'startLine' => 132,
        'endLine' => 137,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'connectUsing' => 
      array (
        'name' => 'connectUsing',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 149,
            'endLine' => 149,
            'startColumn' => 34,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'config' => 
          array (
            'name' => 'config',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 149,
            'endLine' => 149,
            'startColumn' => 48,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'force' => 
          array (
            'name' => 'force',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 149,
                'endLine' => 149,
                'startTokenPos' => 528,
                'startFilePos' => 4228,
                'endTokenPos' => 528,
                'endFilePos' => 4232,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'bool',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 149,
            'endLine' => 149,
            'startColumn' => 63,
            'endColumn' => 81,
            'parameterIndex' => 2,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get a database connection instance from the given configuration.
 *
 * @param  \\UnitEnum|string  $name
 * @param  array  $config
 * @param  bool  $force
 * @return \\Illuminate\\Database\\ConnectionInterface
 *
 * @throws \\RuntimeException
 */',
        'startLine' => 149,
        'endLine' => 166,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'parseConnectionName' => 
      array (
        'name' => 'parseConnectionName',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 174,
            'endLine' => 174,
            'startColumn' => 44,
            'endColumn' => 48,
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
 * Parse the connection into an array of the name and read / write type.
 *
 * @param  string  $name
 * @return array
 */',
        'startLine' => 174,
        'endLine' => 179,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'makeConnection' => 
      array (
        'name' => 'makeConnection',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 187,
            'endLine' => 187,
            'startColumn' => 39,
            'endColumn' => 43,
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
 * Make the database connection instance.
 *
 * @param  string  $name
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 187,
        'endLine' => 206,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'configuration' => 
      array (
        'name' => 'configuration',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 216,
            'endLine' => 216,
            'startColumn' => 38,
            'endColumn' => 42,
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
 * Get the configuration for a connection.
 *
 * @param  string  $name
 * @return array
 *
 * @throws \\InvalidArgumentException
 */',
        'startLine' => 216,
        'endLine' => 228,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'configure' => 
      array (
        'name' => 'configure',
        'parameters' => 
        array (
          'connection' => 
          array (
            'name' => 'connection',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Connection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 237,
            'endLine' => 237,
            'startColumn' => 34,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 237,
            'endLine' => 237,
            'startColumn' => 58,
            'endColumn' => 62,
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
 * Prepare the database connection instance.
 *
 * @param  \\Illuminate\\Database\\Connection  $connection
 * @param  string  $type
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 237,
        'endLine' => 258,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'dispatchConnectionEstablishedEvent' => 
      array (
        'name' => 'dispatchConnectionEstablishedEvent',
        'parameters' => 
        array (
          'connection' => 
          array (
            'name' => 'connection',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Connection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 266,
            'endLine' => 266,
            'startColumn' => 59,
            'endColumn' => 80,
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
 * Dispatch the ConnectionEstablished event if the event dispatcher is available.
 *
 * @param  \\Illuminate\\Database\\Connection  $connection
 * @return void
 */',
        'startLine' => 266,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'setPdoForType' => 
      array (
        'name' => 'setPdoForType',
        'parameters' => 
        array (
          'connection' => 
          array (
            'name' => 'connection',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Database\\Connection',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 284,
            'endLine' => 284,
            'startColumn' => 38,
            'endColumn' => 59,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'type' => 
          array (
            'name' => 'type',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 284,
                'endLine' => 284,
                'startTokenPos' => 1146,
                'startFilePos' => 8850,
                'endTokenPos' => 1146,
                'endFilePos' => 8853,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 284,
            'endLine' => 284,
            'startColumn' => 62,
            'endColumn' => 73,
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
 * Prepare the read / write mode for database connection instance.
 *
 * @param  \\Illuminate\\Database\\Connection  $connection
 * @param  string|null  $type
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 284,
        'endLine' => 293,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'purge' => 
      array (
        'name' => 'purge',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 301,
                'endLine' => 301,
                'startTokenPos' => 1222,
                'startFilePos' => 9291,
                'endTokenPos' => 1222,
                'endFilePos' => 9294,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 301,
            'endLine' => 301,
            'startColumn' => 27,
            'endColumn' => 38,
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
 * Disconnect from the given database and remove from local cache.
 *
 * @param  \\UnitEnum|string|null  $name
 * @return void
 */',
        'startLine' => 301,
        'endLine' => 306,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'disconnect' => 
      array (
        'name' => 'disconnect',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 314,
                'endLine' => 314,
                'startTokenPos' => 1276,
                'startFilePos' => 9609,
                'endTokenPos' => 1276,
                'endFilePos' => 9612,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 314,
            'endLine' => 314,
            'startColumn' => 32,
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
 * Disconnect from the given database.
 *
 * @param  \\UnitEnum|string|null  $name
 * @return void
 */',
        'startLine' => 314,
        'endLine' => 319,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'reconnect' => 
      array (
        'name' => 'reconnect',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 327,
                'endLine' => 327,
                'startTokenPos' => 1341,
                'startFilePos' => 9984,
                'endTokenPos' => 1341,
                'endFilePos' => 9987,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 327,
            'endLine' => 327,
            'startColumn' => 31,
            'endColumn' => 42,
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
 * Reconnect to the given database.
 *
 * @param  \\UnitEnum|string|null  $name
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 327,
        'endLine' => 338,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'usingConnection' => 
      array (
        'name' => 'usingConnection',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 347,
            'endLine' => 347,
            'startColumn' => 37,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 347,
            'endLine' => 347,
            'startColumn' => 44,
            'endColumn' => 61,
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
 * Set the default database connection for the callback execution.
 *
 * @param  \\UnitEnum|string  $name
 * @param  callable  $callback
 * @return mixed
 */',
        'startLine' => 347,
        'endLine' => 358,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'refreshPdoConnections' => 
      array (
        'name' => 'refreshPdoConnections',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 366,
            'endLine' => 366,
            'startColumn' => 46,
            'endColumn' => 50,
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
 * Refresh the PDO connections on a given connection.
 *
 * @param  string  $name
 * @return \\Illuminate\\Database\\Connection
 */',
        'startLine' => 366,
        'endLine' => 377,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'getDefaultConnection' => 
      array (
        'name' => 'getDefaultConnection',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the default connection name.
 *
 * @return string
 */',
        'startLine' => 384,
        'endLine' => 387,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'setDefaultConnection' => 
      array (
        'name' => 'setDefaultConnection',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 395,
            'endLine' => 395,
            'startColumn' => 42,
            'endColumn' => 46,
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
 * Set the default connection name.
 *
 * @param  string  $name
 * @return void
 */',
        'startLine' => 395,
        'endLine' => 398,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'supportedDrivers' => 
      array (
        'name' => 'supportedDrivers',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the supported drivers.
 *
 * @return string[]
 */',
        'startLine' => 405,
        'endLine' => 408,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'availableDrivers' => 
      array (
        'name' => 'availableDrivers',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get all of the drivers that are actually available.
 *
 * @return string[]
 */',
        'startLine' => 415,
        'endLine' => 421,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'extend' => 
      array (
        'name' => 'extend',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 430,
            'endLine' => 430,
            'startColumn' => 28,
            'endColumn' => 32,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'resolver' => 
          array (
            'name' => 'resolver',
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
            'startLine' => 430,
            'endLine' => 430,
            'startColumn' => 35,
            'endColumn' => 52,
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
 * Register an extension connection resolver.
 *
 * @param  string  $name
 * @param  callable  $resolver
 * @return void
 */',
        'startLine' => 430,
        'endLine' => 433,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'forgetExtension' => 
      array (
        'name' => 'forgetExtension',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 441,
            'endLine' => 441,
            'startColumn' => 37,
            'endColumn' => 41,
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
 * Remove an extension connection resolver.
 *
 * @param  string  $name
 * @return void
 */',
        'startLine' => 441,
        'endLine' => 444,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'getConnections' => 
      array (
        'name' => 'getConnections',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Return all of the created connections.
 *
 * @return array<string, \\Illuminate\\Database\\Connection>
 */',
        'startLine' => 451,
        'endLine' => 454,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'setReconnector' => 
      array (
        'name' => 'setReconnector',
        'parameters' => 
        array (
          'reconnector' => 
          array (
            'name' => 'reconnector',
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
            'startLine' => 462,
            'endLine' => 462,
            'startColumn' => 36,
            'endColumn' => 56,
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
 * Set the database reconnector callback.
 *
 * @param  callable  $reconnector
 * @return void
 */',
        'startLine' => 462,
        'endLine' => 465,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      'setApplication' => 
      array (
        'name' => 'setApplication',
        'parameters' => 
        array (
          'app' => 
          array (
            'name' => 'app',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 473,
            'endLine' => 473,
            'startColumn' => 36,
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
 * Set the application instance used by the manager.
 *
 * @param  \\Illuminate\\Contracts\\Foundation\\Application  $app
 * @return $this
 */',
        'startLine' => 473,
        'endLine' => 478,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
      '__call' => 
      array (
        'name' => '__call',
        'parameters' => 
        array (
          'method' => 
          array (
            'name' => 'method',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 487,
            'endLine' => 487,
            'startColumn' => 28,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'parameters' => 
          array (
            'name' => 'parameters',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 487,
            'endLine' => 487,
            'startColumn' => 37,
            'endColumn' => 47,
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
 * Dynamically pass methods to the default connection.
 *
 * @param  string  $method
 * @param  array  $parameters
 * @return mixed
 */',
        'startLine' => 487,
        'endLine' => 494,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Database',
        'declaringClassName' => 'Illuminate\\Database\\DatabaseManager',
        'implementingClassName' => 'Illuminate\\Database\\DatabaseManager',
        'currentClassName' => 'Illuminate\\Database\\DatabaseManager',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
        'Illuminate\\Support\\Traits\\Macroable' => 
        array (
          0 => 
          array (
            'alias' => 'macroCall',
            'method' => '__call',
            'hash' => 'illuminate\\support\\traits\\macroable::__call',
          ),
        ),
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
        'illuminate\\support\\traits\\macroable::__call' => 'Illuminate\\Support\\Traits\\Macroable::__call',
      ),
    ),
  ),
));