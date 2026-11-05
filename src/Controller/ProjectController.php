<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProjectController extends AbstractController
{
    private const PROJECTS = [
        'convertisseur' => [
            'title' => 'Convertisseur',
            'description' => 'Un convertisseur d\'unités simple pour passer des kilomètres aux miles, des degrés Celsius aux Fahrenheit, et plus encore.',
        ],
        'carnet-de-notes' => [
            'title' => 'Carnet de notes',
            'description' => 'Une application pour écrire, classer et retrouver rapidement ses notes personnelles.',
        ],
        'mini-blog' => [
            'title' => 'Mini-blog',
            'description' => 'Un petit blog pour publier des articles courts et les afficher du plus récent au plus ancien.',
        ],
    ];

    #[Route('/projets', name: 'app_project_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => self::PROJECTS,
        ]);
    }

    #[Route('/projets/{slug}', name: 'app_project_show', requirements: ['slug' => '[a-z0-9-]+'], methods: ['GET'])]
    public function show(string $slug): Response
    {
        $project = self::PROJECTS[$slug] ?? throw $this->createNotFoundException();

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }
}
