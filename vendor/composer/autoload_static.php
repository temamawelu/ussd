<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit79d421761354fcdebc2cc88f733d2ec3
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stilinski\\Ussd\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stilinski\\Ussd\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit79d421761354fcdebc2cc88f733d2ec3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit79d421761354fcdebc2cc88f733d2ec3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit79d421761354fcdebc2cc88f733d2ec3::$classMap;

        }, null, ClassLoader::class);
    }
}
