<?php

require __DIR__ . '/vendor/autoload.php';

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

/*********************/
/* Init Session Data */
/*********************/

$_SESSION["user"]["username"] = "balthov60";
$_SESSION["user"]["isConnected"] = false;
$_SESSION["user"]["isAdmin"] = "true";


/*****************/
/* Define Routes */
/*****************/

/* HomePage */

$app->get('', 'DUT\\Controllers\\HomeController::displayHomePage')
    ->bind('home');

/* User */

$app->get('/login', 'DUT\\Controllers\\AuthController::displayLoginPage')
    ->bind('login');

$app->get("/subscribe", "DUT\\Controllers\\AuthController::displaySubscribePage")
    ->bind('subscribe');

/* Post */

$app->get("/{idPost}", "DUT\\Controllers\\PostController::displayPost")
    ->bind("{idPost}");

$app->get("/edit/{idPost]", "DUT\\Controllers\\PostController::displayPostEdition")
    ->bind("edit/{idPost}")
    ->before("DUT\\Controllers\\AuthController::isAdmin");

/* Commentary */

$app->post("/addCommentary", "DUT\\Controllers\\CommentaryController::addCommentary")
    ->bind("addCommentary");

$app->post("/removeCommentary", "DUT\\Controllers\\CommentaryController::removeCommentary")
    ->bind("removeCommentary");

$app["debug"] = true;
$app->run();
