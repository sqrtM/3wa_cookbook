<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Form\CommentType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookbookController extends AbstractController
{
    public function __construct(
        private readonly RecipeRepository $recipes,
        private readonly UserRepository $users
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

    #[Route('/cookbook/recipe/{recipeId}', name: 'app_recipe')]
    public function recipe(int $recipeId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = $this->recipes->findOneBy(['id' => $recipeId]);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAssociatedRecipe($recipe);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setAuthor($this->users->findOneBy(['id' => 1]));

            $entityManager->persist($comment);
            $entityManager->flush();

            // Redirect back to the recipe page after submitting the comment
            return $this->redirectToRoute('app_recipe', ['recipeId' => $recipe->getId()]);
        }

        return $this->render('cookbook/recipe.html.twig', [
            'recipe' => $recipe,
            'comment_form' => $form->createView(),
        ]);
    }

    #[Route('/cookbook/recipe/{recipeId}/edit', name: 'app_edit_recipe')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $recipeId): Response
    {
        $recipe = $this->recipes->findOneBy(['id' => $recipeId]);
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($recipe->getSteps() as $step) {
                $recipe->addStep($step);
            }

            $entityManager->persist($recipe);
            $entityManager->flush();

            return $this->redirectToRoute('app_recipe', ['recipeId' => $recipeId]);
        }

        return $this->render('cookbook/edit_recipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cookbook/recipe/{recipeId}/delete', name: 'app_delete_recipe')]
    public function delete(int $recipeId, EntityManagerInterface $entityManager): Response
    {
        $recipe = $this->recipes->findOneBy(['id' => $recipeId]);
        $entityManager->remove($recipe);
        $entityManager->flush();
        return $this->redirectToRoute('app_cookbook');
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

            return $this->redirectToRoute('app_recipe', ['recipeId' => $recipe->getId()]);
        }

        return $this->render('cookbook/new_recipe.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
