<?php

use \Filesharing\Http\Controllers\ProfileController;
use Filesharing\Http\Controllers\HomeController;
use \Filesharing\Http\Controllers\AuthController;
use \Filesharing\Middleware\AuthMiddleware;

error_reporting(-1);
function d($n)
{
    echo '<pre class ="dev">';
    var_dump($n);
    echo '</pre>';
}


function dd($n)
{
    echo '<pre class ="dev">';
    var_dump($n);
    echo '</pre>';
    die();
}

$container = require_once __DIR__ . "/../config/bootstrap.php";

$app = new \Slim\App($container);

$app->group('', function () use ($app) {
    $app->get('/profile/{name}', ProfileController::class . ':profileShow');
    $app->post('/profile/{name}', ProfileController::class . ':profileShow');

})->add($container->get(AuthMiddleware::class));
$app->get('/', HomeController::class . ':home');
$app->post('/', HomeController::class . ':home');
$app->get('/login', AuthController::class . ':login');
$app->post('/login', AuthController::class . ':login');
$app->get('/logout', AuthController::class . ':logout');
$app->get('/signup', AuthController::class . ':signUp');
$app->post('/signup', AuthController::class . ':signUp');
$app->post('/show/{id}', HomeController::class . ':show');
$app->get('/show/{id}', HomeController::class . ':show');
$app->get('/download/{id}', HomeController::class . ':download');
$app->get('/show', HomeController::class . ':showFiles');


$app->run();
