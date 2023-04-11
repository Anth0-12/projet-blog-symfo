<?php

namespace App\Twig;

use App\Controller\Admin\ArticleCrudController;
use App\Controller\Admin\CategoryCrudController;
use App\Controller\Admin\PageCrudController;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Menu;
use App\Entity\Page;
use Doctrine\Common\Collections\Collection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    const ADMIN_NAMESPACE = 'App\Controller\Admin';

    public function __construct(
        private RouterInterface $router,
        private AdminUrlGenerator $adminUrlGenerator
    )
    {
        
    }


    public function getFilters(): array
    {
        return [
            new TwigFilter('menuLink', [$this, 'menuLink']),
        ];
    }


    public function getFunctions(): array
    {
        return [
            new TwigFunction('ea_index', [$this, 'getAdminUrl']),
        ];
    }


    public function getAdminUrl(string $controller): string
    {
        return $this->adminUrlGenerator
            ->setController(self::ADMIN_NAMESPACE . DIRECTORY_SEPARATOR . $controller)
            ->generateUrl();
    }

    public function menuLink(Menu $menu): string
    {
        $article = $menu->getArticle();
        $category = $menu->getCategory();
        $page = $menu->getPage();
        
        $url = $menu->getLink() ?: '#';

        if ($url !== '#') {
            return $url;
        }

        if ($article) {
            $name = 'article_show';
            $slug = $article->getSlug();
        }

        if ($category) {
            $name = 'category_show';
            $slug = $category->getSlug();
        }

        if ($page) {
            $name = 'page_show';
            $slug = $page->getSlug();
        }


        if (!isset($name, $slug)) {
            return $url;
        }

        return $this->router->generate($name, [
            'slug' => $slug
        ]);
    }
}