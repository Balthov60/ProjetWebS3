<?php

require __DIR__ . '../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

/*************************/
/* Init Silex Components */
/*************************/

$app = new Silex\Application();

/* Entity Manager */

$app['connection'] = [ 'driver' => 'pdo_mysql', 'host' => 'localhost',
    'user' => 'root', 'password' => '', 'dbname' => 'blog-projet-web'];
$app['doctrine_config'] = Setup::createYAMLMetadataConfiguration([__DIR__ . '/config'], true);
$app['em'] = function ($app) {
    return EntityManager::create($app['connection'], $app['doctrine_config']);
};

/* URL Generator */

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/* Twig */

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/src/Views',
]);


/*****************/
/* Define Routes */
/*****************/

/* HomePage */

$app->get("/", "")->bind("");

/* User */

$app->get("/login", "PWB\\Controllers\\AuthController::")->bind('login');
$app->get("/login", "PWB\\Controllers\\AuthController::")->bind('subscribe');

/* Post */

$app->get("/{idPost]", "")->bind("");
$app->get("/edit/{idPost]", "")->bind("");

$app->run();
