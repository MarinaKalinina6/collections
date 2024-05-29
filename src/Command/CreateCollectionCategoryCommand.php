<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ItemCollection\CollectionCategory;
use App\Repository\CollectionCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-category')]
class CreateCollectionCategoryCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CollectionCategoryRepository $collectionCategoryRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (count($this->collectionCategoryRepository) > 0) {
            $output->writeln('Skipped. Categories already created.');

            return Command::SUCCESS;
        }

        $collectionCategory = [
            'Book',
            'Movies',
            'Games',
            'Music',
            'Money',
            'Coins',
            'Posters',
            'Statuettes',
            'Cars',
            'Motorcycles',
            'Other',
        ];

        foreach ($collectionCategory as $collectionCategoryName) {
            $category = new CollectionCategory();
            $category->setName($collectionCategoryName);

            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        $output->writeln(sprintf('Successfully added %d categories.', count($collectionCategory)));

        return Command::SUCCESS;
    }
}
