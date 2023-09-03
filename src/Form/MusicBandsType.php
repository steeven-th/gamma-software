<?php

namespace App\Form;

use App\Entity\MusicBands;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MusicBandsType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom du groupe",
                "attr"  => [
                    "placeholder" => "Nom du groupe"
                ]
            ])
            ->add('origin', TextType::class, [
                "label" => "Origine du groupe",
                "attr"  => [
                    "placeholder" => "Origine du groupe"
                ]
            ])
            ->add('city', TextType::class, [
                "label" => "Ville du groupe",
                "attr"  => [
                    "placeholder" => "Ville du groupe"
                ]
            ])
            ->add('startYear', DateType::class, [
                "label" => "Année de début",
                "attr"  => [
                    "placeholder" => "Année de début"
                ]
            ])
            ->add('endYear', DateType::class, [
                "label"    => "Année de fin",
                "attr"     => [
                    "placeholder" => "Année de fin"
                ],
                "required" => false
            ])
            ->add('founders', TextType::class, [
                "label"    => "Fondateurs",
                "attr"     => [
                    "placeholder" => "Fondateurs"
                ],
                "required" => false
            ])
            ->add('members', IntegerType::class, [
                "label"    => "Membres",
                "attr"     => [
                    "placeholder" => "Membres"
                ],
                "required" => false
            ])
            ->add('musicalStyle', TextType::class, [
                "label"    => "Style musical",
                "attr"     => [
                    "placeholder" => "Style musical"
                ],
                "required" => false
            ])
            ->add('description', TextareaType::class, [
                "label"    => "Description",
                "attr"     => [
                    "placeholder" => "Description"
                ],
                "required" => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults(
            [
                'data_class' => MusicBands::class,
            ]
        );
    }
}
