<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainPageController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function view(EntityManagerInterface $entityManager,): Response
    {
        $receivedCollections = $entityManager->getRepository(ItemCollection::class)->findAll();
        $collections = [];
        foreach ($receivedCollections as $collection) {
            $collections[] = [
                'id' => $collection->getId(),
                'name' => $collection->getName(),
                'description' => $collection->getDescription(),
                'category' => $collection->getCategory()->getName(),
                'count_item' => $collection->getItem()->count(),
            ];
        }
        usort($collections, function ($a, $b) {
            return ($b['count_item'] - $a['count_item']);
        });

        $biggestCollection = array_slice($collections, 0, 5);

        $receivedItems = $entityManager->getRepository(Item::class)->findBy([], ['id' => 'DESC']);
        $itemByTag = [];
        foreach ($receivedItems as $item) {
            foreach ($item->getTags() as $tag) {
                $itemByTag[] =
                    $tag->getName();
            }
        }

        $itemsCount = array_slice($receivedItems, 0, 6);

        $items = [];
        foreach ($itemsCount as $item) {
            $items[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
                'collection_name' => $item->getItemCollection()->getName(),
                $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $item->getUserId()]),
                'username' => $user->getUsername(),
            ];
        }

        $tags = array_count_values($itemByTag);

        return $this->render('main.html.twig', [
            'collections' => $biggestCollection,
            'items' => $items,
            'tags' => $tags,
        ]);
    }

    #[Route('/tag/{name}', name: 'search_tag')]
    public function searchTag(
        Item\ItemTag $currentTag,
        EntityManagerInterface $entityManager,
    ): Response {
        $items = $entityManager->getRepository(Item::class)->findAll();

        $itemByTag = [];
        foreach ($items as $item) {
            $tags = $item->getTags();
            foreach ($tags as $tag) {
                if ($currentTag->getId() === $tag->getId()) {
                    $itemByTag[] = [
                        'id' => $item->getId(),
                        'name' => $item->getName(),
                        'collection_name' => $item->getItemCollection()->getName(),
                        'tag' => $tag->getName(),
                    ];
                }
            }
        }

        return $this->render('Main/search_tag.html.twig', [
            'items' => $itemByTag,
            'name_tag' => $currentTag->getName(),
        ]);
    }

    #[Route('view/item/{id}', name: 'view_item')]
    public function viewItem(Item $item, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => $item->getUserId()]);

        return $this->render('Main/view_item.html.twig', [
            'name' => $item->getName(),
            'collection_name' => $item->getItemCollection()->getName(),
            'category' => $item->getItemCollection()->getCategory()->getName(),
            'date' => $item->getAddedAt()->format("F j, Y, g:i a"),
            'author' => $user->getUsername(),
        ]);
    }
}