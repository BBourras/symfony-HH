<?php

namespace App\Twig\Runtime;

use App\Repository\PostRepository;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\Inflector\FrenchInflector;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly PostRepository $postRepository)
    {
        // Inject dependencies if needed
    }

    public function doPluralize(int $quantity, string $singular, string $plural):string
    {
        $singularOrPlural = $quantity === 1 ? $singular : $plural;
        return $quantity . ' '. $singularOrPlural;  
    }

    public function doTotalPosts():int
    {
        return $this->postRepository->count([]);
    }

    public function doLatestPosts(int $maxResults = 5):array
    {
        return $this->postRepository->findBy([], ['publishedAt' => 'DESC'], $maxResults);
    }

    public function doMostCommentedPosts(int $maxResults = 5):array
    {
        return $this->postRepository->findMostCommented($maxResults);
    }
}