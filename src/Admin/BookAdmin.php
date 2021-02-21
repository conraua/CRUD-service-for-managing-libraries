<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Author;

final class BookAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Book information')
            ->add('name', TextType::class)
            ->add('year', IntegerType::class)
            ->add('description', TextareaType::class)
            ->add('image', UrlType::class, [
                'required' => false
            ])
            ->end()
            ->with('Authors')
            ->add('authors', ModelType::class, [
                'class' => Author::class,
                'property' => 'name',
                'multiple' => true
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('authors', null, [], EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'name'
            ])
            ->add('year')
            ->add('description');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('authors', null, [
                'associated_property' => 'name'
            ])
            ->add('year')
            ->add('description')
            ->add('image');
    }
}