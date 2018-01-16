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

/* Session */

$app->register(new Silex\Provider\SessionServiceProvider());
if (null === $app["session"]->get("user"))
    $app["session"]->set("user", ["username" => "", "isConnected" => false, "isAdmin" => false]);

/*****************/
/* Define Routes */
/*****************/

/* HomePage */

$app->get('', 'DUT\\Controllers\\HomeController::displayHomePage')
    ->bind('home');

/* User */

$app->get('/login', 'DUT\\Controllers\\AuthController::displayLoginPage')
    ->bind('login');

$app->get('/loginError', 'DUT\\Controllers\\AuthController::displayLoginPageWithErrorMsg')
    ->bind('loginError');

$app->post('/login', 'DUT\\Controllers\\AuthController::login')
    ->bind('loginPost');

$app->get('/logout', 'DUT\\Controllers\\AuthController::logout')
    ->bind('logout');


$app->get("/subscribe", "DUT\\Controllers\\AuthController::displaySubscribePage")
    ->bind('subscribe');

$app->get("/subscribe/{errorMsg}", "DUT\\Controllers\\AuthController::displaySubscribePageErrorMsg")
    ->bind('subscribeError');

$app->post("/subscribe", "DUT\\Controllers\\AuthController::subscribe")
    ->bind('subscribePost');

/* Post */

$app->get("/posts", "DUT\\Controllers\\PostController::displayAllPosts")
    ->bind("allPostsList");

$app->get("/createPost", "DUT\\Controllers\\PostController::displayPostCreation")
    ->bind("createPost")->before('DUT\\Controllers\\AuthController::isAdmin');

$app->post("/createPost", "DUT\\Controllers\\PostController::savePost")
    ->bind("savePost")->before('DUT\\Controllers\\AuthController::isAdmin');

$app->get("/{idPost}", "DUT\\Controllers\\PostController::displayPost")
    ->bind("{idPost}")->before('DUT\\Controllers\\AuthController::isConnected');

$app->get("/edit/{idPost}", "DUT\\Controllers\\PostController::displayPostEdition")
    ->bind("edit/{idPost}")->before('DUT\\Controllers\\AuthController::isAdmin');

$app->get("/remove/{idPost}", "DUT\\Controllers\\PostController::removePost")
    ->bind("remove/{idPost}")->before('DUT\\Controllers\\AuthController::isAdmin');

/* Commentary */

$app->get("/{idPost}/editCommentary/{idCommentary}", "DUT\\Controllers\\CommentaryController::displayEditCommentaryPage")
    ->bind("editCommentary")->before('DUT\\Controllers\\AuthController::isOwnerOrAdmin');

$app->post("/addCommentary", "DUT\\Controllers\\CommentaryController::addCommentary")
    ->bind("addCommentary")->before('DUT\\Controllers\\AuthController::isConnected');

$app->post("/updateCommentary", "DUT\\Controllers\\CommentaryController::updateCommentary")
    ->bind("updateCommentary")->before('DUT\\Controllers\\AuthController::isConnected');

$app->get("/{idPost}/removeCommentary/{idCommentary}", "DUT\\Controllers\\CommentaryController::removeCommentary")
    ->bind("removeCommentary")->before('DUT\\Controllers\\AuthController::isOwnerOrAdmin');

$app["debug"] = true;
$app->run();
