<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Item;
use App\Entity\ItemCollection;
use App\Form\ItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemsController extends AbstractController
{
    #[Route('/collection/{id}/items', name: 'view_items')]
    public function view(Request $request, string $id, EntityManagerInterface $entityManager): Response
    {
        $sort = $request->query->get('sort');
        if ($sort === null) {
            $sort = 'DESC';
        }

        $collection = $entityManager->getRepository(ItemCollection::class)->findOneBy(['id' => $id]);
        $nameCollection = $collection->getName();
        $idCollection = $collection->getId();
        $items = $entityManager->getRepository(Item::class)->findBy(['itemCollection' => $idCollection],
            ['id' => $sort]);

        $attributes = $collection->getCustomItemAttributes()->getValues();
        $attributesName = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getType()->name === 'String' || $attribute->getType()->name === 'Date') {
                $attributesName[] = [
                    $attribute->getName(),
                ];
            }
        }
        $valueAttributes = [];
        foreach ($items as $item) {
            $value = $item->getValueAttributes()->getValues();
            foreach ($value as $values) {
                if ($values->getType()->name === 'String' || $values->getType()->name === 'Date') {
                    $valueAttributes[] = [
                        $values->getValue(),
                    ];
                }
            }
        }

        return $this->render('Items/view.html.twig', [
            'name_collection' => $nameCollection,
            'id_collection' => $idCollection,
            'items' => $items,
            'attributes' => $attributesName,
            'value_attributes' => $valueAttributes,
        ]);
    }

    #[Route('item/create', name: 'item_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        try {
            $id = $request->get('id');
        } catch (\Throwable) {
            throw $this->createNotFoundException();
        }

        $collection = $entityManager->getRepository(ItemCollection::class)->find($id);
        if ($collection === null) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(ItemType::class, options: [
            'collection' => $collection,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Item $item */
            $item = $form->getData();

            $item->setItemCollection($collection);
            $currentUser = $this->getUser()->getUserIdentifier();
            $item->setUserId($currentUser);

            $entityManager->persist($item);

            $entityManager->flush();

            return $this->redirectToRoute('view_items', ['id' => $id]);
        }

        return $this->render('Items/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Create Item',
            'heading' => 'Create Item',
            'idCollection' => $id,
        ]);
    }

    #[Route('item/{id}/edit', name: 'item_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, Item $item): Response
    {
        $collection = $item->getItemCollection();

        $form = $this->createForm(ItemType::class, $item, [
            'collection' => $collection,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Item $item */
            $item = $form->getData();

           // $item->setItemCollection($collection);
            $currentUser = $this->getUser()->getUserIdentifier();
            $item->setUserId($currentUser);

            $entityManager->persist($item);

            $entityManager->flush();

            return $this->redirectToRoute('view_items', ['id' => $collection->getId()]);
        }

        return $this->render('Items/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Update Item',
            'heading' => 'Update Item',
            'idCollection' => $collection->getId(),
        ]);
    }

    #[Route('item/{id}/delete', name: 'item_delete')]
    public function delete(EntityManagerInterface $entityManager, Item $item): Response
    {
        $itemCollection = $entityManager->getRepository(Item::class)->findOneBy(['id' => $item->getId()]);

        $entityManager->remove($itemCollection);

        $entityManager->flush();

        return $this->redirectToRoute('view_items', ['id' => $itemCollection->getItemCollection()->getId()]);
    }
}

