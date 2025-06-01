<?php

namespace App\Controller\Admin;

use App\Entity\AdministrativeUnit;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UnitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdministrativeUnit::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('name'),
            TextField::new('code'),
            AssociationField::new('children')
                ->autocomplete(),
            AssociationField::new('path')
                ->autocomplete(),
            AssociationField::new('parent')
                ->autocomplete(),
        ];
    }
}
