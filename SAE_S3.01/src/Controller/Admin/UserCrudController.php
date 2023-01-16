<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Boolean;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {

        if($this->isGranted('IS_IMPERSONATOR') && $this->isGranted('ROLE_SUPER_ADMIN')) {
            return [
            IdField::new('id')
                ->onlyOnIndex()
                ->setSortable(true), 
            TextField::new('name'),
            EmailField::new('email')
                ->setRequired(true)
                ->setFormTypeOptions(['disabled' => true])
                ->setSortable(true),
            BooleanField::new('isBot')
                ->setFormTypeOptions(['disabled' => true])
                ->setSortable(true),
            BooleanField::new('isAdmin')
                ->setRequired(true)
                ->setFormTypeOptions(['disabled' => true])
                ->setSortable(false),
            BooleanField::new('isSuperAdmin')
                ->setRequired(true)
                ->setFormTypeOptions(['disabled' => true]),
            CollectionField::new('roles'),
            ];
        } 
        else {
            return [
                IdField::new('id')
                    ->onlyOnIndex(),
                TextField::new('name'),
                EmailField::new('email')
                    ->setRequired(true)
                    ->setFormTypeOptions(['disabled' => true]),
                BooleanField::new('isBot')
                    ->setFormTypeOptions(['disabled' => true])
                    ->setSortable(true),
                BooleanField::new('isAdmin')
                    ->setRequired(true)
                    ->setPermission('ROLE_SUPER_ADMIN'),
                BooleanField::new('isSuperAdmin')
                    ->setRequired(true)
                    ->setFormTypeOptions(['disabled' => true]),
                CollectionField::new('roles')
                    ->setFormTypeOptions(['disabled' => true])
                    ->setPermission('ROLE_SUPER_ADMIN'),
            ];
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        $impersonate = Action::new('impersonate', 'Incarner', 'fas fa-user-secret')
        ->linkToUrl(function (User $entity) {
            return 'series/?_switch_user='.$entity->getEmail();
        });


    return parent::configureActions($actions)
        ->add(Crud::PAGE_INDEX, $impersonate)
        ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action
                ->setLabel('Supprimer')
                ->setIcon('fa fa-trash')
                ->displayIf(fn (User $user) => $this->isGranted('ROLE_SUPER_ADMIN'));
        })
        ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action
                ->setLabel('Modifier')
                ->setIcon('fa fa-edit');
        })
        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action
                ->setLabel('Ajouter')
                ->setIcon('fa fa-plus')
                ->displayIf(fn (User $user) => $this->isGranted('ROLE_SUPER_ADMIN'))
                ->linkToRoute('app_admin_user_new');
        })
        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action
                ->setLabel('Nombre de faux comptes')
                ->setIcon('fa fa-user-secret')
                ->linkToRoute('app_admin_user_count_fake_accounts');
        });
    }
    
}