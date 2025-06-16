<?php

namespace App\Controller\Admin;

use App\Entity\Act;
use App\Entity\Challenge;
use App\Entity\Contact;
use App\Entity\Comment;
use App\Entity\Like;
use App\Entity\User;
use App\Entity\Location;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Relais Harmonie Backend');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Acts', 'fas fa-list', Act::class);
        yield MenuItem::linkToCrud('Challenges', 'fas fa-list', Challenge::class);
        yield MenuItem::linkToCrud('Contacts', 'fas fa-list', Contact::class);
        yield MenuItem::linkToCrud('Comments', 'fas fa-list', Comment::class);
        yield MenuItem::linkToCrud('Likes', 'fas fa-list', Like::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Locations', 'fas fa-list', Location::class);
    }
}
