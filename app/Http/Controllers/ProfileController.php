<?php


namespace Filesharing\Http\Controllers;

use Doctrine\ORM\EntityManager;
use Filesharing\Entity\User;
use Filesharing\Services\AuthService;
use Filesharing\Services\CsrfService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\RecursiveValidator;


class ProfileController
{
    protected $auth;
    protected $validator;
    protected $view;
    protected $em;
    protected $csrf;

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

    public function profileShow(Request $request, Response $response, $args)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['name' => $args['name']]);

        $response = $this->csrf->setCookieToken($response, $request->getCookieParam('token'));
        $token = $this->csrf->getToken();
        if ($request->getParsedBody()) {
            $uploadedFile = $request->getUploadedFiles()['file'];

            $lastPath = $user->getAvatarPath();

            $path = "uploads/avatars/" . bin2hex(random_bytes(10)) . $uploadedFile->getClientFilename();


            $errors = $this->validator->validate($user);

            $errors->addAll($this->validator->validate($uploadedFile->getClientFilename(), new NotBlank()));

            $errors->addAll($this->validator->validate($uploadedFile->getClientMediaType(), new Regex(
                ['pattern' => '#image\\/png|jpg|jpeg#i',
                    'match' => true
                ]
            )));

            $errors->addAll($this->csrf->checkToken($request));


            if (0 == count($errors)) {
                if (file_exists($lastPath) && $lastPath != 'img/user.png') {

                    unlink($lastPath);
                }
                $user->setAvatarPath($path);
                $uploadedFile->moveTo($path);
                $this->em->persist($user);
                move_uploaded_file($uploadedFile->file, $path);
                $this->em->flush();

                return $response->withRedirect("/profile/" . strtolower($args['name']), 303);
            }
        }


        return $this->view->render($response, 'profile.twig', ["user" => $user, "token" => $token, "errors" => $errors ?? '']);
    }

}