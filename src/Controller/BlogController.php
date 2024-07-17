<?php

namespace App\Controller;

use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController{

    // To pass a variable
    #[Route('/blog/{page?0}', name: 'blog_list', requirements: ['page' => '\d+'])]
    public function list_page(int $page): Response
    {
        $textTest = 'Page: '.$page;
        return $this->render(
            'lucky/blog.html.twig',
            ['text_test' => $textTest],
        );
    }

    #[Route('/blog/{slug}', name: 'blog_show')]
    public function show(string $slug): Response
    {
        
        $textTest = 'Blog de: ' . strtolower($slug);
        return $this->render(
            'lucky/blog.html.twig',
            ['text_test' => $textTest],
        );
    }
    
}