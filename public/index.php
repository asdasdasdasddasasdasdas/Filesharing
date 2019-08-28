<?php

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
    $app->get('/profile/{name}', 'ProfileController:profileShow');
    $app->post('/profile/{name}', 'ProfileController:profileShow');

})->add($container->AuthMiddleware);
$app->get('/', 'HomeController:home');
$app->post('/', 'HomeController:home');
$app->get('/login', 'AuthController:login');
$app->post('/login', 'AuthController:login');
$app->get('/logout', 'AuthController:logout');
$app->get('/signup', 'AuthController:signUp');
$app->post('/signup', 'AuthController:signUp');
$app->post('/show/{id}', 'HomeController:show');
$app->get('/show/{id}', 'HomeController:show');
$app->get('/download/{id}', 'HomeController:download');
$app->get('/show', 'HomeController:showFiles');


$app->run();
