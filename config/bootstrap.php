<?php


use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use \Filesharing\Http\Controllers\ProfileController;
use Filesharing\Http\Controllers\HomeController;
use Filesharing\Http\Controllers\AuthController;
use Filesharing\Services\AuthService;
use Slim\Views\Twig;
use Symfony\Component\Validator\Validation;
use \Slim\Container;
use \Doctrine\ORM\EntityManager;
use \Doctrine\Bundle\DoctrineBundle\Registry;
use \Filesharing\ConstraintValidatorFactory;
use \Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use \Filesharing\Services\CsrfService;
use \Slim\Http\Request;
use Filesharing\Helper\Helper;
use \Filesharing\Middleware\AuthMiddleware;
use \Filesharing\Helper\FileSizeConverter;

$loader = require __DIR__ . '/../vendor/autoload.php';
$isDevMode = true;
$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(array('app/Entity'), $isDevMode);

$connection = require 'connection.php';

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];


$container = new Container($configuration);
$em = EntityManager::create($connection, $config);
$registry = new Registry($container, $connection, [EntityManager::class], '', '');
$factory = new ConstraintValidatorFactory();
$factory->addValidator('doctrine.orm.validator.unique', new UniqueEntityValidator($registry));

$container[FileSizeConverter::class] = function (Container $container) {
    return new FileSizeConverter();
};

$container[EntityManager::class] = function (Container $container) use ($em) {

    return $em;
};
$container[AuthService::class] = function (Container $container) {

    return new AuthService($container->get(EntityManager::class));
};
$container[Helper::class] = function ($container) {

    return new Helper();
};
$container[Validation::class] = function (Container $container) use ($factory) {
    $builder = Validation::createValidatorBuilder();
    $builder->setConstraintValidatorFactory($factory);
    $builder->enableAnnotationMapping();

    $validator = $builder->getValidator();


    return $validator;
};
$container[CsrfService::class] = function (Container $container) {

    return new CsrfService($container->get(Validation::class));
};


$container[Twig::class] = function (Container $container) {


    $view = new Twig('../app/Views', [
        'cache' => false
    ]);

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->get(AuthService::class)->isLoggedIn($container->request->getCookieParam('hash')),
        'user' => $container->get(AuthService::class)->getAuthUser($container->request->getCookieParam('hash')),

    ]);

    $view->getEnvironment()->addGlobal('converter',
        $container->get(FileSizeConverter::class)

    );

    return $view;
};

$container[AuthMiddleware::class] = function (Container $container) {
    return new AuthMiddleware($container->get(AuthService::class));

};
$container['notFoundHandler'] = function (Container $container) {
    return function (Request $request, $response) use ($container) {

        return $container->get(Twig::class)->render($response->withStatus(404), "404.twig", ['path' => $request->getRequestTarget()]);
    };
};
$container[HomeController::class] = function (Container $container) {
    return new HomeController(
        $container->get(CsrfService::class),
        $container->get(AuthService::class),
        $container->get(Validation::class),
        $container->get(EntityManager::class),
        $container->get(Twig::class),
        $container->get(Helper::class)
    );
};
$container[AuthController::class] = function (Container $container) {
    return new AuthController(
        $container->get(CsrfService::class),
        $container->get(AuthService::class),
        $container->get(Validation::class),
        $container->get(EntityManager::class),
        $container->get(Twig::class)
    );
};
$container[ProfileController::class] = function (Container $container) {
    return new ProfileController(
        $container->get(CsrfService::class),
        $container->get(AuthService::class),
        $container->get(Validation::class),
        $container->get(EntityManager::class),
        $container->get(Twig::class)
    );
};


/** @var ClassLoader $loader */


AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $container;