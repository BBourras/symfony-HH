<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [AppExtensionRuntime::class, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pluralize', [AppExtensionRuntime::class, 'doPluralize']),
            new TwigFunction('total_posts', [AppExtensionRuntime::class, 'doTotalPosts']),
            new TwigFunction('latest_posts', [AppExtensionRuntime::class, 'doLatestPosts']),
            new TwigFunction('most_commented_posts', [AppExtensionRuntime::class, 'doMostCommentedPosts']),
        ];
    }
}