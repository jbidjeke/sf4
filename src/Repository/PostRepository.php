<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findLatest(int $page = 1): Pagerfanta
    {
        $query = $this->getEntityManager()
            ->createQuery('
                SELECT p, a, t
                FROM App:Post p
                JOIN p.author a
                LEFT JOIN p.tags t
                WHERE p.publishedAt <= :now
                ORDER BY p.publishedAt DESC
            ')
            ->setParameter('now', new \DateTime())
        ;
 

        return $this->createPaginator($query, $page);
    }
    
    public function findLatestApi(): array
    {
        $query = $this->getEntityManager()
        ->createQuery('
                SELECT p, a, t
                FROM App:Post p
                JOIN p.author a
                LEFT JOIN p.tags t
                WHERE p.publishedAt <= :now
                ORDER BY p.publishedAt DESC
            ')
         ->setParameter('now', new \DateTime());

         return $query->getResult();
    }
    
    /**
     * Recupère les données dans la base en fonction de la lat, lng, category et eventuellement une expression
     * @param string $category
     * @param float $lat
     * @param float $lng
     * @param int $distance
     * @param string $q
     * @return array
     */
    public function findBySearchQueryApi(string $category = "", float $lat = 0, float $lng = 0, int $distance = -1, string $rawQuery = ""): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === count($searchTerms)) {
            return [];
        }

        $formule="(6366*acos(cos(radians($lat))*cos(radians(`lat`))*cos(radians(`lng`) -radians($lng))+sin(radians($lat))*sin(radians(`lat`))))";
        $sql = "\n"
            . "SELECT `p`.id, `p`.title, `p`.summary, `p`.content, `p`.published_at, `u`.full_name, `u`.email, `a`.price, `g`.lat, `g`.lng, `i`.url \n"
            . "FROM `symfony_demo_post` `p`\n"
            . "LEFT JOIN `symfony_demo_user` `u`\n"
            . "ON `p`.`author_id` = `u`.`id`\n"
            . "LEFT JOIN `advert` `a`\n"
            . "ON `p`.`advert_id` = `a`.`id`\n"
            . "LEFT JOIN `image` `i`\n"
            . "ON `a`.`image_id` = `i`.`id`\n"
            . "LEFT JOIN `geolocate` `g`\n"
            . "ON `a`.`geolocate_id` = `g`.`id`\n"
            . "LEFT JOIN `advert_category` `ac`\n"
            . "ON `a`.`id` = `ac`.`advert_id`\n"
            . "LEFT JOIN `category` `c`\n"
            . "ON `c`.`id` = `ac`.`category_id`\n";
        
            $sql .="WHERE 1\n";
            
            //$sql .="AND `lat` != null AND `lng` != null\n";
        
            if (!empty($category))
                $sql .="AND `c`.`name` = :category\n";
            
            if ($distance >= 0)
                $sql .="AND $formule <= :distance  \n";
            
            
        
        foreach ($searchTerms as $key => $term) {
            $sql .="AND (`p`.`title` LIKE '%$term%' OR `p`.`content` LIKE '%$term%') \n";
        }
       
        $sql .= "ORDER BY `p`.`published_at` DESC";
              
        try {
            $stmt = $this->getEntityManager()
            ->getConnection()
            ->prepare($sql);
            
            if (!empty($category))
                $stmt->bindValue('category', $category);
            
            if ($distance >= 0)
                $stmt->bindValue('distance', $distance);
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return [];
        }
    }

    private function createPaginator(Query $query, int $page): Pagerfanta
    {
        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(Post::NUM_ITEMS);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @return Post[]
     */
    public function findBySearchQuery(string $rawQuery, int $limit = Post::NUM_ITEMS): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('p');

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('p.title LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.$term.'%')
            ;
        }

        return $queryBuilder
            ->orderBy('p.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Removes all non-alphanumeric characters except whitespaces.
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return preg_replace('/[^[:alnum:] ]/', '', trim(preg_replace('/[[:space:]]+/', ' ', $query)));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', mb_strtolower($searchQuery)));

        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }
}
