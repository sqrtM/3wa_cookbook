<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookbookController extends AbstractController
{
    public function __construct(
        private readonly RecipeRepository $recipes
    ) {
    }

    #[Route('/cookbook', name: 'app_cookbook')]
    public function index(): Response
    {
        $recipes = $this->recipes->findAll();
        return $this->render('cookbook/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    #[Route('/cookbook/{recipeId}', name: 'app_recipe')]
    public function recipe(Request $request, int $recipeId): Response
    {
        $recipe = $this->recipes->findOneBy(['id' => $recipeId]);
        return $this->render('cookbook/recipe.html.twig', [
            'recipe' => $recipe,
        ]);
    }


    #[Route('/cookbook/new', name: 'app_new_recipe')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($recipe->getSteps() as $step) {
                $recipe->addStep($step);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute('app_cookbook');
        }

        return $this->render('cookbook/new_recipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
