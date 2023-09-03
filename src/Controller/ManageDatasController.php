<?php

namespace App\Controller;

use App\Entity\MusicBands;
use App\Form\MusicBandsType;
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

    // Route permettant de récupérer le fichier envoyé
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

    // Route permettant de vider la base de données
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

    // Route permettant de supprimer une donnée de la base de données
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

    // Route permettant de récupérer les données de la base de données par pallier
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

    // Route permettant d'afficher un modal pour éditer une donnée
    #[Route('/edit/data', name: 'app_edit_data')]
    public function editData(Request $request): Response {

        $musicBand = $request->get('id') == 0 ? new MusicBands() : $this->em->getRepository(MusicBands::class)->find($request->get('id'));

        if ($musicBand) {
            $form = $this->createForm(MusicBandsType::class, $musicBand);

            $renderModal = $this->renderView('manage_datas/edit_data.html.twig', [
                'form'      => $form->createView(),
                'musicBand' => $musicBand
            ]);

            return new JsonResponse(
                [
                    'status'        => true,
                    'modal'         => html_entity_decode($renderModal),
                    'musicBandId'   => $musicBand->getId(),
                    'musicBandName' => $musicBand->getName(),
                ]
            );
        }

        return new JsonResponse(
            [
                'status' => false,
            ]
        );
    }

    // Route permettant de mettre à jour une donnée
    #[Route('/update/data', name: 'app_update_data')]
    public function updateData(Request $request): JsonResponse {

        $musicBand = (int) substr($request->get('id'),
                                  7) == 0 ? new MusicBands() : $this->em->getRepository(MusicBands::class)->find(substr($request->get('id'),
                                                                                                                        7));

        if ($musicBand) {
            $form = $this->createForm(MusicBandsType::class, $musicBand);
            $form->handleRequest($request);

            //Récupération des données du formulaire
            $formDatas = $request->get('form');

            $musicBand->setName($formDatas[0]['value']);
            $musicBand->setOrigin($formDatas[1]['value']);
            $musicBand->setCity($formDatas[2]['value']);
            $musicBand->setStartYear(new \DateTime($formDatas[3]['value'] . '-01-01'));
            $musicBand->setEndYear($formDatas[4]['value'] != '' ? new \DateTime($formDatas[4]['value'] . '-01-01') : null);
            $musicBand->setFounders($formDatas[5]['value'] != '' ? $formDatas[5]['value'] : null);
            $musicBand->setMembers($formDatas[6]['value'] != '' ? (int) $formDatas[6]['value'] : null);
            $musicBand->setMusicalStyle($formDatas[7]['value'] != '' ? $formDatas[7]['value'] : null);
            $musicBand->setDescription($formDatas[8]['value'] != '' ? $formDatas[8]['value'] : null);

            try {
                $this->em->persist($musicBand);
                $this->em->flush();

                return new JsonResponse(
                    [
                        'status' => true,
                    ]
                );
            } catch (\Exception $e) {
                return new JsonResponse(
                    [
                        'status' => false,
                    ]
                );
            }
        }

        return new JsonResponse(
            [
                'status' => false,
            ]
        );
    }
}
