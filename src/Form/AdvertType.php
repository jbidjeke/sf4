<?php
// src/Form/Type/AdvertType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Advert;
use App\Entity\Category;
use App\Form\ImageType;

class AdvertType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('price', MoneyType::class, [
          'divisor' => 100,
          'label' => 'Prix',
      ])
      ->add('categories', EntityType::class, [
          // query choices from this entity
          'class' => Category::class,
      
          // use the Category.name property as the visible option string
          'choice_label' => 'Name',
      
          // used to render a select box, check boxes or radios
          'multiple' => true,
          'expanded' => false,
      ])
      ->add('image', ImageType::class, [
                'label' => 'Information facultative',
            ]);
      

    // On ajoute une fonction qui va ecouter l'evenement PRE_SET_DATA
    /*$builder->addEventListener(
      FormEvents::PRE_SET_DATA,
      function(FormEvent $event) {
        // Recuperer notre objet Advert sous-jacent
        $advert = $event->getData();

        if (null === $advert) {
          return;
        }

        if (!$advert->getPublished() || null === $advert->getId()) {
          $event->getForm()->add('published', 'checkbox', array('required' => false));
        } else {
          $event->getForm()->remove('published');
        }
      }
    );*/
  }

   /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }

}