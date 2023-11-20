<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('steps', CollectionType::class, [
                'entry_type' => StepType::class,
                'allow_add' => true,
                'by_reference' => false,
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $recipe = $event->getData();
                $author = $this->userRepository->findOneBy(['id' => 1]);

                if ($author instanceof User) {
                    $recipe->setAuthor($author);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
