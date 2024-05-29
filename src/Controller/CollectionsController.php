<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ItemCollection;
use App\Form\Collection;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToReadFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CollectionsController extends AbstractController
{
    #[Route('/collections', name: 'view_collections')]
    public function view(
        EntityManagerInterface $entityManager,
    ): Response {
        $currentUser = $this->getUser()->getUserIdentifier();
        $collections = $entityManager->getRepository(ItemCollection::class)->findBy(
            ['userId' => $currentUser],
            ['id' => 'DESC'],
        );

        $collectionItem = [];
        foreach ($collections as $collection) {
            $collectionItem[] = [
                'collectionId' => $collection->getId(),
                'name' => $collection->getName(),
                'description' => $collection->getDescription(),
                'category' => $collection->getCategory()->getName(),
                'image' => $collection->getImageUrl() !== null
                    ? sprintf('collection_image/%s', $collection->getImageUrl())
                    : null,
            ];
        }

        return $this->render('Collections/view.html.twig', [
            'collection' => $collectionItem,
        ]);
    }

    #[Route('/files', name: 'view_asset')]
    public function viewAsset(
        FilesystemOperator $imagesStorage,
        Request $request,
    ): Response {
        $path = $request->query->get('path');

        try {
            return new Response($imagesStorage->read($path), 200, ['Content-Type' => 'image/jpeg']);
        } catch (UnableToReadFile) {
            throw $this->createNotFoundException();
        }
    }

    /**
     * @throws FilesystemException
     */
    #[Route('/collection/create', name: 'create_collection', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function create(
        Request $request,
        SluggerInterface $slugger,
        FilesystemOperator $imagesStorage,
        EntityManagerInterface $entityManager,
    ): Response {
        $collection = new ItemCollection();
        $form = $this->createForm(Collection::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $currentUser = $this->getUser()->getUserIdentifier();
            $collection->setUserId($currentUser);

            /** @var UploadedFile $image */
            return $this->checkData($form, $slugger, $collection, $imagesStorage, $entityManager);
        }

        return $this->render('Collections/create.html.twig', [
            'heading' => 'Please, create collection',
            'form' => $form,
            'title' => 'Create collection',
        ]);
    }

    /**
     * @throws FilesystemException
     */
    #[Route('/collection/{id}/update', name: 'update_collection', methods: [Request::METHOD_POST, Request::METHOD_GET])]
    public function update(
        Request $request,
        SluggerInterface $slugger,
        FilesystemOperator $imagesStorage,
        EntityManagerInterface $entityManager,
        ItemCollection $collection,
    ): Response {
        $form = $this->createForm(Collection::class, $collection);
        $form->handleRequest($request);
        $currentImage = $collection->getImageUrl();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            return $this->checkData($form, $slugger, $collection, $imagesStorage, $entityManager);
        }

        return $this->render('Collections/create.html.twig', [
            'heading' => 'Update collection',
            'form' => $form,
            'collection' => $collection,
            'image' => $currentImage,
            'title' => 'Update collection',
        ]);
    }

    #[Route('collection/{id}/delete', name: 'delete_collection')]
    public function delete(EntityManagerInterface $entityManager, ItemCollection $collection): Response
    {
        $id = $collection->getId();
        $deleteCollection = $entityManager->getRepository(ItemCollection::class)->find($id);

        $entityManager->remove($deleteCollection);

        $entityManager->flush();

        return $this->redirectToRoute('view_collections');
    }

    /**
     * @param FormInterface $form
     * @param SluggerInterface $slugger
     * @param ItemCollection $collection
     * @param FilesystemOperator $imagesStorage
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws FilesystemException
     */
    public function checkData(
        FormInterface $form,
        SluggerInterface $slugger,
        ItemCollection $collection,
        FilesystemOperator $imagesStorage,
        EntityManagerInterface $entityManager,
    ): Response {
        $image = $form->get('image')->getData();
        if ($image !== null) {
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
            $collection->setImageUrl($newFilename);

            $imageContent = file_get_contents($image->getRealPath());


            $imagesStorage->write(
                sprintf('collection_image/%s', $newFilename),
                $imageContent,
            );
        }

        $entityManager->persist($collection);

        $entityManager->flush();

        return $this->redirectToRoute('view_collections');
    }
}
