<?php

namespace Filesharing\Http\Controllers;

use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Filesharing\Entity\Comment;
use Filesharing\Entity\File;
use Filesharing\Entity\User;
use Filesharing\Services\AuthService;
use Filesharing\Services\CsrfService;
use Filesharing\Helper\Helper;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Validator\RecursiveValidator;


class HomeController
{
    /**
     * @var
     */
    protected $validator;
    /**
     * @Doctrine\ORM\EntityManager
     */
    protected $em;
    /**
     * @var
     */
    protected $view;
    /**
     * @var
     */
    protected $helper;

    protected $csrf;


    public function __construct(
        CsrfService $csrf,
        AuthService $auth,
        RecursiveValidator $validator,
        EntityManager $em,
        Twig $view,
        Helper $helper
    )
    {

        $this->csrf = $csrf;
        $this->auth = $auth;
        $this->validator = $validator;
        $this->em = $em;
        $this->view = $view;
        $this->helper = $helper;

    }

    public function download(Request $request, Response $response, $args)
    {
        $file = $this->em->getRepository(File::class)->findOneBy(['id' => $args['id']]);
        $response = $response->withHeader("Content-Type", $file->getType())
            ->withHeader('Content-Description', 'File Transfer')
            ->withHeader('Expires', '0')
            ->withHeader("Content-Disposition", "attachment; filename={$file->getName()}")
            ->withHeader("Content-length", "{$file->getSize()}");
        readfile($file->getPublicPath());
        return $response;
    }

    public function home(Request $request, Response $response, $args)
    {

        $response = $this->csrf->setCookieToken($response, $request->getCookieParam('token'));
        $token = $this->csrf->getToken();
        if ($request->getUploadedFiles()) {
            $uploadedFiles = $request->getUploadedFiles()['file'];
            foreach ($uploadedFiles as $uploadedFile) {

                $slug = bin2hex(random_bytes(10));

                $path = 'uploads/' . $slug . $uploadedFile->getClientFilename();

                $file = new File();

                $imgPath = $this->helper->getImagePath($uploadedFile->getClientMediaType());

                if ($imgPath) {
                    $file->setImagePath($imgPath);
                } else {
                    $file->setImagePath('/' . $path);
                }

                $file->setName($uploadedFile->getClientFilename());
                $file->setSize($uploadedFile->getSize());
                $file->setDescription($request->getParsedBodyParam('description'));
                $file->setPublicPath($path);
                $file->setType($uploadedFile->getClientMediaType());
                $file->setCreatedAt(Carbon::now());
                $file->setUser($this->auth->getAuthUser($request->getCookieParam('hash')));
                $errors = $this->validator->validate($file);

                $errors->addAll($this->csrf->checkToken($request));


                if (0 == count($errors)) {

                    $uploadedFile->moveTo($path);
                    $this->em->persist($file);
                    move_uploaded_file($uploadedFile->file, $path);

                } else {
                    return $this->view->render($response, 'home.twig', ['errors' => $errors ?? '', 'token' => $token]);
                }
            }
            $this->em->flush();
            return $response->withRedirect('/show', 303);
        }
        return $this->view->render($response, 'home.twig', ['errors' => $errors ?? '', 'token' => $this->csrf->getToken()]);

    }


    public function showFiles(Request $request, Response $response, $args)
    {
        $currentPage = is_null($request->getQueryParam("page")) || $request->getQueryParam("page") < 1 ? 1 : intval($request->getQueryParam('page'));
        $paginator = new \Filesharing\Pagination\Paginator($this->em);
        $paginator->paginate($request, $currentPage, 16);

        return $this->view->render($response, 'show.twig', [
            'paginator' => $paginator,
            'helper' => $this->helper

        ]);
    }


    public function show(Request $request, Response $response, $args)
    {


        $file = $this->em->getRepository(File::class)->findOneBy(['id' => $args['id']]);
        $criteria = Criteria::create();
        $exp = Criteria::expr();
        $criteria->where($exp->eq('parent', null));

        if ($request->getParsedBody()) {

            if ($request->getParsedBodyParam('comment-answer')) {

                $comment = $this->em->getRepository(Comment::class)->findOneBy(['id' => $request->getParsedBodyParam('comment-id')]);

                $answer = new Comment();
                $answer->setCreatedAt(Carbon::now());
                $answer->setText($request->getParsedBodyParam('comment-answer'));
                $answer->setUser($this->auth->getAuthUser($request->getCookieParam('hash')));
                $errors = $this->validator->validate($answer);
                if (0 == count($errors)) {
                    $answer->setParent($comment);
                    $comment->setFile($file);
                    $file->setComments($comment);
                    $this->em->persist($comment);
                    $this->em->persist($answer);
                    $this->em->persist($file);

                    $this->em->flush();
                    return $response->withRedirect("/show/{$args['id']}", 303);
                }
            }


            $comment = new Comment();
            $comment->setCreatedAt(Carbon::now());

            $comment->setText($request->getParsedBodyParam('text'));
            $errors = $this->validator->validate($comment);
            if (0 == count($errors)) {

                $comment->setUser($this->auth->getAuthUser($request->getCookieParam('hash')));
                $comment->setFile($file);
                $file->setComments($comment);
                $this->em->persist($file);
                $this->em->persist($comment);
                $this->em->flush();
                return $response->withRedirect("/show/{$args['id']}", 303);
            }

        }


        if (!$file) {
            return $response->withRedirect('/show', 303);
        }
        return $this->view->render($response, 'file.twig', [
            "file" => $file,
            'helper' => $this->helper,
            'comments' => $file->getComments()->matching($criteria),
            'errors' => $errors ?? ''
        ]);
    }
}