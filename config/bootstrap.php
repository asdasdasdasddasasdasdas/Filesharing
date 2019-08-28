<?php


use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Filesharing\Http\Controllers\AuthController;
use Filesharing\Http\Controllers\HomeController;
use Filesharing\Services\AuthService;
use Slim\Views\Twig;
use Symfony\Component\Validator\Validation;

$loader = require __DIR__.'/../vendor/autoload.php';
$isDevMode = true;
$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array('app/Entity'), $isDevMode);

$connection = array(
    "dbname" => "",
    "user" => "",
    "password" => "",
    "host" => "",
    "driver" => "");

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];


$container = new \Slim\Container($configuration);
$em = \Doctrine\ORM\EntityManager::create($connection, $config);
$registry = new \Doctrine\Bundle\DoctrineBundle\Registry($container, $connection, ['em'], '', '');
$factory = new \Filesharing\ConstraintValidatorFactory();
$factory->addValidator('doctrine.orm.validator.unique', new \Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator($registry));


$container['em'] = function ($container) use ($em) {

    return $em;
};
$container['auth'] = function ($container) {

    return new AuthService($container->em);
};
$container['types'] = function ($container) {

    return new \Filesharing\Services\Helper();
};
$container['validator'] = function ($container) use ($factory) {
    $builder = Validation::createValidatorBuilder();
    $builder->setConstraintValidatorFactory($factory);
    $builder->enableAnnotationMapping();

    $validator = $builder->getValidator();


    return $validator;
};
$container['csrf'] = function ($container) {

    return new \Filesharing\Services\CsrfService($container->validator);
};


$container['view'] = function ($container) {


    $view = new Twig('../app/Views', [
        'cache' => false
    ]);

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->isLoggedIn($container->request->getCookieParam('hash')),
        'user' => $container->auth->getAuthUser($container->request->getCookieParam('hash')),
    ]);

    return $view;
};

$container['AuthMiddleware'] = function ($container) {
    return new \Filesharing\Middleware\AuthMiddleware($container->auth);

};
$container['notFoundHandler'] = function ($container) {
    return function (\Slim\Http\Request $request, $response) use ($container) {

        return $container->view->render($response->withStatus(404), "404.twig", ['path' => $request->getRequestTarget()]);
    };
};
$container['HomeController'] = function ($container) {
    return new HomeController($container, $container->em);
};
$container['AuthController'] = function ($container) {
    return new AuthController($container);
};
$container['ProfileController'] = function ($container) {
    return new \Filesharing\Http\Controllers\ProfileController($container);
};


/** @var ClassLoader $loader */


AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $container;
