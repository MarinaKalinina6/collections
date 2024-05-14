<?php

declare(strict_types=1);

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class CollectionsController extends AbstractController
{
#[Route('/', name: 'main')]
    public function main(Security $security,):Response
    {
        $currentUser = $security->getUser();
        if($currentUser === null){
            $currentUsername = '';
        } else{
            $currentUsername = $currentUser->getUserIdentifier();
        }

        return $this->render('main.html.twig', [
            'currentUsername' => $currentUsername,
        ]);
    }
}
