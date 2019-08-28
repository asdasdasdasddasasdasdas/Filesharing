<?php


namespace Filesharing\Http\Controllers;

use Carbon\Carbon;
use Filesharing\Entity\Role;
use Filesharing\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\EqualTo;

class AuthController
{
    protected $auth;
    protected $validator;
    protected $view;
    protected $em;
    protected $csrf;

    /**
     * AuthController constructor.
     * @param $auth
     * @param $view
     * @param $em
     */
    public function __construct($container)
    {
        $this->csrf = $container->get('csrf');
        $this->auth = $container->get('auth');
        $this->validator = $container->get('validator');
        $this->em = $container->get('em');
        $this->view = $container->get('view');
    }

    public function signUp($request, $response, $args)
    {

        $this->csrf->setCookieToken($response, $request->getCookieParam('token'));
        $token = $this->csrf->getToken();
        if ($request->getParsedBody()) {

            $user = new User;

            $hash = bin2hex(random_bytes(40));

            $user->setName($request->getParsedBodyParam('name'));
            $user->setEmail($request->getParsedBodyParam('email'));
            $user->setPassword(md5($request->getParsedBodyParam('password')));
            $user->setCreatedAt(Carbon::now());
            $user->setHash($hash);
            $user->setAvatarPath('img/user.png');
            $errors = $this->validator->validate($user);
            if (0 == count($errors)) {
                $response = $this->auth->authUser($hash, $response);

                $this->em->persist($user);

                $this->em->flush();
                return $response->withRedirect("/profile/{$user->getName()}", 303);
            }
        }
        return $this->view->render($response, 'registration.twig', [
            'errors' => $errors ?? '',
            'token' => $token
        ]);
    }

    public function login($request, $response, $args)
    {

        $response = $this->csrf->setCookieToken($response, $request->getCookieParam('token'));
        $token = $this->csrf->getToken();
        if ($request->getParsedBody()) {

            $repository = $this->em->getRepository(User::class);
            $errors = $this->validator->validate($request->getParsedBodyParam('email'), [
                new NotBlank(["message" => "Email must be not blank"]),
                new Email()]);

            $errors->addAll($this->validator->validate(md5($request->getParsedBodyParam('password')), [
                new NotBlank(["message" => "Password must be not blank"])
            ]));
            $ptoken = is_null($request->getParsedBodyParam('token')) ? '' : $request->getParsedBodyParam('token');
            $ctoken = is_null($request->getCookieParam('token')) ? '' : $request->getCookieParam('token');

            $errors->addAll($this->validator->validate($ptoken, new EqualTo(['value' => $ctoken])));
            if (0 == count($errors)) {
                $user = $repository->findOneBy([
                    'email' => $request->getParsedBodyParam('email'),
                    'password' => $request->getParsedBodyParam('password')
                ]);
                d($user);
                if ($user) {
                    $response = $this->auth->authUser($user->getHash(), $response);
                    return $response->withRedirect("/profile/{$user->getName()}", 303);
                }
            }
        }
        return $this->view->render($response, 'login.twig', [
            'errors' => $errors ?? '',
            'token' => $token
        ]);
    }

    public function logout($request, $response, $args)
    {

        $response = $this->auth->logoutUser($request->getCookieParam('hash'), $response);
        return $response->withRedirect('/', 303);
    }
}