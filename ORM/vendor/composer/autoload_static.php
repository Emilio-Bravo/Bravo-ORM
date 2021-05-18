<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc0f2a37ca4e253441be05f72c4717e0e
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Bravo\\ORM\\ENV\\' => 14,
            'Bravo\\ORM\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Bravo\\ORM\\ENV\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/env',
        ),
        'Bravo\\ORM\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Bravo\\ORM\\BravoORM' => __DIR__ . '/../..' . '/src/ORM/BravoORM.php',
        'Bravo\\ORM\\DB' => __DIR__ . '/../..' . '/src/Database/DB.php',
        'Bravo\\ORM\\DataHandler' => __DIR__ . '/../..' . '/src/helpers/DataHandler.php',
        'Bravo\\ORM\\DataNotFoundException' => __DIR__ . '/../..' . '/src/exceptions/dataNotFoundException.php',
        'Bravo\\ORM\\ENV\\DatabseEnv' => __DIR__ . '/../..' . '/src/env/DatabaseEnv.php',
        'Bravo\\ORM\\ExceptionInterface' => __DIR__ . '/../..' . '/src/interface/ExceptionInterface.php',
        'Bravo\\ORM\\Query' => __DIR__ . '/../..' . '/src/Database/query.php',
        'Bravo\\ORM\\QueryHandler' => __DIR__ . '/../..' . '/src/Database/queryHanlder.php',
        'Bravo\\ORM\\QueryInterface' => __DIR__ . '/../..' . '/src/interface/queryInterface.php',
        'Bravo\\ORM\\inputSanitizer' => __DIR__ . '/../..' . '/src/helpers/inputSanitizer.php',
        'Bravo\\ORM\\noConnectionException' => __DIR__ . '/../..' . '/src/exceptions/noConnectionException.php',
        'Bravo\\ORM\\statementException' => __DIR__ . '/../..' . '/src/exceptions/statementException.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc0f2a37ca4e253441be05f72c4717e0e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc0f2a37ca4e253441be05f72c4717e0e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc0f2a37ca4e253441be05f72c4717e0e::$classMap;

        }, null, ClassLoader::class);
    }
}
