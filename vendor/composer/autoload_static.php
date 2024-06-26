<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc9a59339497a80d0bc899bceee07c6e8
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PauloLeo\\LaravelQJS\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PauloLeo\\LaravelQJS\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc9a59339497a80d0bc899bceee07c6e8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc9a59339497a80d0bc899bceee07c6e8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc9a59339497a80d0bc899bceee07c6e8::$classMap;

        }, null, ClassLoader::class);
    }
}
