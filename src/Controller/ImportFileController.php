<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportFileController extends AbstractController
{
    #[Route('/import/file', name: 'app_import_file')]
    public function index(): Response
    {
        return $this->render('import_file/index.html.twig', [
            'controller_name' => 'ImportFileController',
        ]);
    }
}
