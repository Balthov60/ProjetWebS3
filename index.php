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

$_SESSION["user"]["username"] = "Balthov60";
$_SESSION["user"]["isConnected"] = "true";
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

$app->get('/logout', 'DUT\\Controllers\\AuthController::logout')
    ->bind('logout');


$app->get('/login/{errorMsg}', 'DUT\\Controllers\\AuthController::displayLoginPageWithErrorMsg')
    ->bind('loginError');

$app->post('/login', 'DUT\\Controllers\\AuthController::login')
    ->bind('loginPost');

$app->get("/subscribe", "DUT\\Controllers\\AuthController::displaySubscribePage")
    ->bind('subscribe');

$app->get("/subscribe/{errorMsg}", "DUT\\Controllers\\AuthController::displaySubscribePageErrorMsg")
    ->bind('subscribeError');

$app->post("/subscribe", "DUT\\Controllers\\AuthController::subscribe")
    ->bind('subscribePost');

/* Post */

$app->get("/posts", "DUT\\Controllers\\PostController::displayAllPosts")
    ->bind("allPostsList");

$app->post("/posts", "DUT\\Controllers\\PostController::orderPostsBy")
    ->bind("orderPostsBy");

$app->get("/{idPost}", "DUT\\Controllers\\PostController::displayPost")
    ->bind("{idPost}");

$app->get("/edit/{idPost}", "DUT\\Controllers\\PostController::displayPostEdition")
    ->bind("edit/{idPost}");

$app->get("/remove/{idPost}", "DUT\\Controllers\\PostController::removePost")
    ->bind("remove/{idPost}");

$app->get("/{idPost}/editCommentary/{idCommentary}", "DUT\\Controllers\\CommentaryController::editCommentary")
    ->bind("editCommentary");

/* Commentary */

$app->post("/addCommentary", "DUT\\Controllers\\CommentaryController::addCommentary")
    ->bind("addCommentary");

$app->post("/updateCommentary", "DUT\\Controllers\\CommentaryController::updateCommentary")
    ->bind("updateCommentary");

$app->get("/{idPost}/removeCommentary/{idCommentary}", "DUT\\Controllers\\CommentaryController::removeCommentary")
    ->bind("removeCommentary");

$app["debug"] = true;
$app->run();
