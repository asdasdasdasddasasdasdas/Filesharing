<?php


namespace Filesharing\Services;

use Doctrine\ORM\EntityManager;
use Filesharing\Entity\User;

class AuthService
{
    protected $em;

    /**
     * AuthService constructor.
     * @param $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    public function authUser($hash, $response)
    {
        $time = time();
        return $response->withHeader("set-cookie", "hash=$hash;max-age=$time+60*60*24*30*12*10");
    }

    public function isLoggedIn($hash)
    {

        $repository = $this->em->getRepository(User::class);
        $user = $repository->findBy(['hash' => $hash]);
        if ($user) {
            return true;
        } else {
            return false;
        }
    }

    public function getAuthUser($hash)
    {

        $repository = $this->em->getRepository(User::class);
        $user = $repository->findOneBy(['hash' => $hash]);

        return $user ?? null;
    }

    public function logoutUser($hash, $response)
    {
        return $response->withHeader("set-cookie", "hash=$hash;max-age=0");
    }
}