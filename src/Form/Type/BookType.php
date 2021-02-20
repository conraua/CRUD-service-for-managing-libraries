<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Author;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('year', IntegerType::class, [
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('image', UrlType::class, [
                    'required' => false
                ])
            ->add('authors', EntityType::class, [
                    'class' => Author::class,
                    'choice_label' => 'name',
                    'multiple' => true
                ])
            ->add('save', SubmitType::class)
        ;
    }
}