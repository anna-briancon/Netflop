<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class UpdateFormType extends AbstractType
{
    /*
    * Formulaire de mise à jour de commentaire
    */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // name : le nom de l'utilisateur
        $builder
            ->add('name', null, [
                'label' => false,
            ])
                 
            // Permet de changer de mot de passe
            ->add('password', RepeatedType::class, [
                'mapped' => false,
                'required' => false,
                'label' => false,
                'type' => PasswordType::class,
                'options' => ['attr' => ['autocomplete' => 'new-password']],
                'first_options'  => ['label' => false],
                'second_options' => ['label' => false],
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                        // Taille maximale donnée par symfony pour des questions de sécurité
                        'max' => 4096,
                    ]),
                ],
            ])
            // country : le pays de l'utilisateur
            ->add('country', null, [
                'label' => false,
            ])
            // agreeTerms : vérifie si l'utilisateur accepte les termes d'inscription
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter les modifications.',
                    ]),
                ],
            ])
            // photo : la photo de profil de l'utilisateur
            ->add('photo', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => "Merci d'insérer une image valide",
                    ])
                ],
            ])
            // submit : le bouton d'envoie
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier',
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'photo' => null,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $entity = $form->getData();

        if ($entity != null && $entity->getPhoto()) {
            $view->vars['photo'] = $entity->getPhoto();
        }
    }
}
