#!/usr/bin/env php
<?php

// Fix warinigs
// error_reporting(error_reporting() ^ E_DEPRECATED);
date_default_timezone_set('Europe/Moscow');

require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

/**
 * Recursively populated $GLOBALS['files']
 *
 * @param string $path The path to glob through.
 *
 * @return void
 * @uses   $GLOBALS['files']
 */
function readDirectory($path)
{
    foreach (glob($path . '/*') as $file) {
        if (!is_dir($file)) {
            $GLOBALS['files'][] = $file;
        } else {
            readDirectory($file);
        }
    }   
}

$version = file_get_contents(dirname(__FILE__) . '/../VERSION.txt');

$api_version     = $version;
$api_state       = 'alpha';

$release_version = $version;
$release_state   = 'alpha';
$release_notes   = "This is an alpha release, see README.markdown for examples.";

$summary     = "A PHP API wrapper for Redis.";

$description = "Rediska (radish on russian) - PHP client for Redis.

Redis is an advanced fast key-value database written in C. It can be used like memcached, in front of a traditional 
database, or on its own thanks to the fact that the in-memory datasets are not volatile but instead persisted on disk. 
One of the cool features is that you can store not only strings, but lists and sets with atomic operations to push/pop 
elements.";

$package = new PEAR_PackageFileManager2();

$package->setOptions(
    array(
        'filelistgenerator'       => 'file',
        'outputdirectory'         => dirname(dirname(__FILE__)),
        'simpleoutput'            => true,
        'baseinstalldir'          => '/',
        'packagedirectory'        => dirname(__FILE__) . '/../',
        'dir_roles'               => array(
            'benchmarks'          => 'doc',
            'examples'            => 'doc',
            'library'             => 'php',
            'library/Rediska'     => 'php',
            'tests'               => 'test',
        ),
        'exceptions'              => array(
            'CHANGELOG.txt'       => 'doc',
            'README.markdown'     => 'doc',
            'VERSION.txt'         => 'doc',
        ),
        'ignore'                  => array(
            'coverage/*',
            'package.php',
            'package.xml',
            'scripts/create_package.php',
            '.git',
            '.gitignore',
            'tests/config.ini',
            '*.tgz'
        )
    )
);

$package->setPackage('Rediska');
$package->setSummary($summary);
$package->setDescription($description);
$package->setChannel('pear.geometria-lab.net');
$package->setPackageType('php');
$package->setLicense(
    'New BSD License',
    'http://www.opensource.org/licenses/bsd-license.php'
);

$package->setNotes($release_notes);
$package->setReleaseVersion($release_version);
$package->setReleaseStability($release_state);
$package->setAPIVersion($api_version);
$package->setAPIStability($api_state);

$maintainers = array(
    array(
        'name'  => 'Ivan Shumkov',
        'user'  => 'shumkov',
        'email' => 'ivan@shumkov.ru',
        'role'  => 'lead',
    ),
    array(
        'name'  => 'Maxim Ivanov',
        'user'  => 'ivanov',
        'email' => 'maximiv@gmail.com',
        'role'  => 'lead',
    ),
    array(
        'name'  => 'Till Klampaeckel',
        'user'  => 'till',
        'email' => 'till@php.net',
        'role'  => 'developer',
    ),
);

foreach ($maintainers as $_m) {  
    $package->addMaintainer(
        $_m['role'],
        $_m['user'],
        $_m['name'],
        $_m['email']
    );
}

$files = array(); // classes and tests
readDirectory(dirname(__FILE__) . '/../library');
readDirectory(dirname(__FILE__) . '/../tests');

foreach ($files as $file) {

    $package->addReplacement(
        $file,
        'package-info',
        '@name@',
        'name'
    );

    $package->addReplacement(
        $file,
        'package-info',
        '@package_version@',
        'version'
    );
}

$files = array();
readDirectory(dirname(__FILE__) . '/library');

$base = dirname(__FILE__) . '/';
foreach ($files as $file) {
    $file = str_replace($base, '', $file);
    $package->addInstallAs($file, str_replace('library/', '', $file));
}


$package->setPhpDep('5.2.1');

$package->setPearInstallerDep('1.7.0');
$package->generateContents();
$package->addRelease();

if (   isset($_GET['make'])
    || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')
) {
    $package->writePackageFile();
} else {
    $package->debugPackageFile();
}
