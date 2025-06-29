<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6814c97bf23b8132fcd74696dcf52bd1
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'G' => 
        array (
            'Garden\\Cli\\' => 11,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
            'Command\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Garden\\Cli\\' => 
        array (
            0 => __DIR__ . '/..' . '/vanilla/garden-cli/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
        'Command\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Command',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6814c97bf23b8132fcd74696dcf52bd1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6814c97bf23b8132fcd74696dcf52bd1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6814c97bf23b8132fcd74696dcf52bd1::$classMap;

        }, null, ClassLoader::class);
    }
}
