<?php


namespace Filesharing\Http\Controllers;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Filesharing\Entity\User;
use Filesharing\Services\AuthService;
use Filesharing\Services\CsrfService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Validator\RecursiveValidator;

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
    public function __construct(
        CsrfService $csrf,
        AuthService $auth,
        RecursiveValidator $validator,
        EntityManager $em,
        Twig $view
    )
    {
        $this->csrf = $csrf;
        $this->auth = $auth;
        $this->validator = $validator;
        $this->em = $em;
        $this->view = $view;
    }

    public function signUp(Request $request, Response $response, $args)
    {

        $this->csrf->setCookieToken($response, $request->getCookieParam('token'));
        $token = $this->csrf->getToken();
        if ($request->getParsedBody()) {

            $user = new User;

            $hash = bin2hex(random_bytes(40));

            $user->setName($request->getParsedBodyParam('name'));
            $user->setEmail($request->getParsedBodyParam('email'));
            $user->setPassword($request->getParsedBodyParam('password'));
            $user->setCreatedAt(Carbon::now());
            $user->setHash($hash);
            $user->setAvatarPath('img/user.png');
            $errors = $this->validator->validate($user);
            $errors->addAll($this->csrf->checkToken($request));
            if (0 == count($errors)) {
                $user->setPassword(md5($request->getParsedBodyParam('password')));
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

    public function login(Request $request, Response $response, $args)
    {

        $response = $this->csrf->setCookieToken($response, $request->getCookieParam('token'));
        $token = $this->csrf->getToken();
        if ($request->getParsedBody()) {

            $repository = $this->em->getRepository(User::class);
            $errors = $this->validator->validate($request->getParsedBodyParam('email'), [
                new NotBlank(["message" => "Email must be not blank"]),
                new Email()]);

            $errors->addAll($this->validator->validate($request->getParsedBodyParam('password'), [
                new NotBlank(["message" => "Password must be not blank"])
            ]));
            $errors->addAll($this->csrf->checkToken($request));
            if (0 == count($errors)) {
                $user = $repository->findOneBy([
                    'email' => $request->getParsedBodyParam('email'),
                    'password' => md5($request->getParsedBodyParam('password'))
                ]);

                $errors->addAll($this->validator->validate($user, new NotNull(["message" => "
Email or password is incorrect
"])));
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

    public function logout(Request $request, Response $response, $args)
    {
        $response = $this->auth->logoutUser($request->getCookieParam('hash'), $response);
        return $response->withRedirect('/', 303);
    }
}