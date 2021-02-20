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
use Doctrine\ORM\EntityRepository;
use App\Entity\Author;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('yearFrom', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'min' => 0
                ]
            ])
            ->add('yearTo', IntegerType::class, [
                'required' => false,
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
                    'query_builder' => function (EntityRepository $entityRepository) {
                        $queryBuilder = $entityRepository->createQueryBuilder('author');
                        return $queryBuilder
                            ->innerJoin('author.books', 'book')
                            ->where($queryBuilder->expr()->isNotNull('book'))
                            ->orderBy('author.name', 'ASC');
                    },
                    'multiple' => true,
                    'required' => false
                ])
            ->add('submit', SubmitType::class)
        ;
    }
}