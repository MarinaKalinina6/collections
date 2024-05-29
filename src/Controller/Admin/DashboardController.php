<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Entity\ItemCollection\CollectionCategory;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
         return $this->render('Admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Collections');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('User', 'fa fa user', User::class);

        yield MenuItem::section('Collections');
        yield MenuItem::linkToCrud('Collection', 'fa fa album-collection', ItemCollection::class);
        yield MenuItem::linkToCrud('Category', 'fa fa sitemap', CollectionCategory::class);
        yield MenuItem::linkToCrud('Items', 'fa fa sitemap', Item::class);

    }
}
