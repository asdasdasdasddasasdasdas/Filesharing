<?php


namespace Filesharing\Pagination;


use Filesharing\Entity\File;

class Paginator
{
    private $em;

    private $pages;

    private $currentPage;

    private $paginator;

    private $link;


    /**
     * Paginator constructor.
     * @param $em
     */
    public function __construct($em)
    {
        $this->em = $em;
    }

    public function paginate($request, $currentPage = 1, $limit = 32)
    {


        $repository = $this->em->getRepository(File::class);


        $c = $repository->createQueryBuilder('p')
            ->select('count(p.type)')
            ->where('p.type LIKE :sort')
            ->andWhere('p.name LIKE :search')
            ->setParameter('sort', '%' . $request->getQueryParam('sort') . '%')
            ->setParameter('search', '%' . $request->getQueryParam('search') . '%')
            ->getQuery()->getSingleScalarResult();

        $pages = intval(ceil($c / $limit));

        if ($currentPage > $pages || $currentPage > $pages) {
            $currentPage = 1;
        }
        $query = $repository->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.created_at', 'desc')
            ->where('p.type LIKE :sort')
            ->andWhere('p.name LIKE :search')
            ->setParameter('sort', '%' . $request->getQueryParam('sort') . '%')
            ->setParameter('search', '%' . $request->getQueryParam('search') . '%')
            ->getQuery();
        $query = $query->setFirstResult(($currentPage - 1) * $limit)->setMaxResults($limit);



        $this->currentPage = $currentPage;
        $this->pages = $pages;

        $this->link = http_build_query([
            "search" => strlen($request->getQueryParam('search')) > 0 ? $request->getQueryParam('search') : null,
            "sort" => strlen($request->getQueryParam('sort')) > 0 ? $request->getQueryParam('sort') : null
        ]);

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query, true);
        $this->paginator = $paginator;


    }

    /**
     * @return mixed
     */
    public function getPages()
    {
        return $this->pages;
    }


    public function hasNextPage()
    {
        return $this->currentPage >= $this->pages ? false : true;
    }

    public function hasPreviousPage()
    {
        return $this->currentPage <= 1 ? false : true;
    }

    public function hasPages()
    {
        return $this->pages <= 1 ? false : true;
    }

    /**
     * @return mixed
     */
    public function getCurrentPage()
    {

        return $this->currentPage;
    }

    /**
     * @return mixed
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return strlen($this->link) > 0 ? '&' . $this->link : '';
    }
}