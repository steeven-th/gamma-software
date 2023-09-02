<?php

namespace App\Controller;

use App\Entity\MusicBands;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManageDatasController extends AbstractController {

    public function __construct(private readonly EntityManagerInterface $em) {
    }

    #[Route('/manage/datas', name: 'app_manage_datas')]
    public function index(): Response {
        return $this->render('manage_datas/index.html.twig');
    }

    // Route ajax permettant de récupérer le fichier envoyé
    #[Route('/import/file/upload', name: 'app_import_file_upload')]
    public function uploadFile(Request $request): JsonResponse {

        // Récupération des données du fichier
        $file = $request->files->get('file');

        $spreadsheet = IOFactory::load($file);
        $sheet = $spreadsheet->getActiveSheet();
        $sheetData = $sheet->toArray(null, true, true, true);

        // Traitement des données pour enregistrement en BDD
        foreach ($sheetData as $key => $datas) {
            if ($key > 1) {

                $musicBands = new MusicBands();
                $musicBands->setName($datas['A']);
                $musicBands->setOrigin($datas['B']);
                $musicBands->setCity($datas['C']);
                $musicBands->setStartYear(new \DateTime($datas['D']));
                $musicBands->setEndYear(new \DateTime($datas['E']));
                $musicBands->setFounders($datas['F']);
                $musicBands->setMembers($datas['G']);
                $musicBands->setMusicalStyle($datas['H']);
                $musicBands->setDescription($datas['I']);

                $this->em->persist($musicBands);
            }
        }

        try {
            $this->em->flush();

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Fichier importé avec succès en base de données'
                ]
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Erreur lors de l\'importation du fichier en base de données'
                ]
            );
        }
    }

    #[Route('/import/database/drop', name: 'app_databse_drop')]
    public function dropDataBase(): JsonResponse {

        $allMusicBands = $this->em->getRepository(MusicBands::class)->findAll();
        foreach ($allMusicBands as $musicBand) {
            $this->em->remove($musicBand);
        }

        try {
            $this->em->flush();

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Base de données vidée avec succès'
                ]
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Erreur lors de la vidage de la base de données'
                ]
            );
        }
    }

    #[Route('/import/data/delete', name: 'app_delete_data')]
    public function deleteData(Request $request): JsonResponse {

        $musicBand = $this->em->getRepository(MusicBands::class)->find($request->get('id'));
        $this->em->remove($musicBand);

        try {
            $this->em->flush();

            return new JsonResponse(
                [
                    'success' => true,
                    'message' => 'Donnée supprimée avec succès'
                ]
            );
        } catch (\Exception $e) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => 'Erreur lors de la suppression de la donnée'
                ]
            );
        }
    }

    #[Route('/load/datas', name: 'app_load_datas')]
    public function loadDatas(Request $request): JsonResponse {

        $step = $request->get('step');
        $offset = $request->get('offset');

        $musicBands = $this->em->getRepository(MusicBands::class)->getMusicBandsByStep($step, $offset);

        return new JsonResponse(
            [
                'success'    => true,
                'musicBands' => $musicBands
            ]
        );
    }
}
