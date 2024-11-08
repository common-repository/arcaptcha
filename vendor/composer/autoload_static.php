<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2e8f1f07daae247a05c1cd494f6f63fd
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'ARCaptcha\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ARCaptcha\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'ARCaptcha\\CF7\\CF7' => __DIR__ . '/../..' . '/src/php/CF7/CF7.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2e8f1f07daae247a05c1cd494f6f63fd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2e8f1f07daae247a05c1cd494f6f63fd::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2e8f1f07daae247a05c1cd494f6f63fd::$classMap;

        }, null, ClassLoader::class);
    }
}
