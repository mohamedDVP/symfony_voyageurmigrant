<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('content', TextareaType::class, [
            'label' => false, // pas besoin de "Votre commentaire"
            'attr' => [
                'placeholder' => 'Écrivez votre commentaire ici...',
                'rows' => 4,
                'class' => 'form-control mb-2',
            ],
        ])
        ->add('parent', HiddenType::class, [
            'mapped' => false,
            'required' => false,
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Publier',
            'attr' => [
                'class' => 'btn btn-primary mt-2'
            ]
        ])
    ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
