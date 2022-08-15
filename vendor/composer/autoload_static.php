<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit23e20e9a167be111e2f40bd5ec1d22aa
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Bc_Featuredproducts\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Bc_Featuredproducts\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit23e20e9a167be111e2f40bd5ec1d22aa::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit23e20e9a167be111e2f40bd5ec1d22aa::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit23e20e9a167be111e2f40bd5ec1d22aa::$classMap;

        }, null, ClassLoader::class);
    }
}
