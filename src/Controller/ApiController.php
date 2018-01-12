<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Controller used to manage api contents in the public part of the site.
 *
 * @Route("/api")
 *
 * @author Jean eric BDIEJEK <jbidjeke@yahoo.fr>
 */
class ApiController extends Controller
{

    /**
     * Lists latest Posts.
     * 
     * @Route("/", defaults={"page": "1"})
     * @Method("GET")
     * @return JsonResponse
     */
    public function index(RegistryInterface $doctrine, int $page, string $category = "", float $lat = 0, float $lng = 0, int $km = 0, string $q = ""): JsonResponse
    {
        $posts = [];
        $latestPosts = $doctrine->getRepository(Post::class)->findLatestApi();
        foreach ($latestPosts as $post) {
            array_push($posts, $this->prepareForJson($post));    
        }
        
        return new JsonResponse($latestPosts);
    }
    
    
    /**
     * List one Post.
     *
     * @Route("/{id}", requirements={"id": "\d+"})
     * @Method("GET")
     * @return JsonResponse
     */
    public function show(Post $post): JsonResponse
    {
        // This security check can also be performed
        // using an annotation: @Security("is_granted('show', post)")
        //$this->denyAccessUnlessGranted('show', $post, 'Posts can only be shown to their authors.');
   
        return new JsonResponse($this->prepareForJson($post));
    }
    
    /**
     * Lists Search Posts.
     *
     * This controller responds to two different routes with the same URL:
     * @Route("/{lat}/{lng}/{q}", defaults={"page": "1"})
     * @Route("/{lat}/{lng}/{distance}/{q}", defaults={"page": "1"})
     * @Route("/{category}/{lat}/{lng}/{distance}/{q}", defaults={"page": "1"})
     * @Method("GET")
     * @return JsonResponse
     */
    public function search(RegistryInterface $doctrine, int $page, string $category = "", float $lat = 0, float $lng = 0, int $distance = -1, string $q = ""): JsonResponse
    {
        $posts = [];
        $latestPosts = $doctrine->getRepository(Post::class)->findBySearchQueryApi($category, $lat, $lng, $distance, $q);
        /*foreach ($latestPosts as $post) {
            array_push($posts, ['title' => $post->getTitle()]);
        }*/
        
        $jsonResponse = new JsonResponse($latestPosts);
        $jsonResponse->headers->set('Access-Control-Allow-Origin', '*');
        return $jsonResponse;
    }
    
    /**
     * Prepare for json format.
     * @param  Post $post 
     * @return array
     */
    protected function prepareForJson(Post $post): array
    {
        $price = null;
        $url = null;
        $lat = null;
        $lng = null;
        if ($post->getAdvert() != null){
            $price = $post->getAdvert()->getPrice();
            $url = $post->getAdvert()->getImage()->getUrl();
            $lat = $post->getAdvert()->getGeolocate()->getLat();
            $lng = $post->getAdvert()->getGeolocate()->getLng();
        }
        return ['id' => $post->getId(), 'title' => $post->getTitle(), 'summary' => $post->getSummary(), 'content' => $post->getContent(), 'published_at' => $post->getPublishedAt(), 'full_name' => $post->getAuthor()->getFullName(), 'email' => $post->getAuthor()->getEmail(), 'price' => $price, 'lat' => $lat, 'lng' => $lng, 'url' => $url];
        
    }
    
    
    
}
