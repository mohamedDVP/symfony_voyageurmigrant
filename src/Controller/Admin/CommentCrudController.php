<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CommentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Comment::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // Champs visibles uniquement dans la liste (INDEX)
        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IdField::new('id'),
                TextField::new('content')->setLabel('Contenu'),
                AssociationField::new('author'),
                AssociationField::new('post'),
                DateTimeField::new('createdAt')->setLabel('Posté le'),
                ChoiceField::new('status')
                    ->setChoices([
                        'En attente' => 'pending',
                        'Approuvé'   => 'approved',
                        'Refusé'     => 'rejected',
                    ]),
            ];
        }

        // Champs pour NEW / EDIT / DETAIL
        return [
            TextField::new('content')->setLabel('Contenu'),
            AssociationField::new('author'),
            AssociationField::new('post'),
            DateTimeField::new('createdAt')->setLabel('Posté le')->onlyOnDetail(),
            ChoiceField::new('status')
                ->setChoices([
                    'En attente' => 'pending',
                    'Approuvé'   => 'approved',
                    'Refusé'     => 'rejected',
                ]),
        ];
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_MODERATOR'); // pour Post et Comment
    }
}
