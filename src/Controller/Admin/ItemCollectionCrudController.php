<?php

namespace App\Controller\Admin;

use App\Entity\ItemCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ItemCollectionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ItemCollection::class;
    }

}
