<?php

namespace App\Controller\API;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiVilleController extends AbstractController
{
    /**
     * @Route("/api/recupVille/{id}", name="api_recupVille", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function recupApiVille(VilleRepository $villeRepository, int $id) : JsonResponse{
        $ville = $villeRepository->find($id);
        $lieux = $ville->getLieus();
        return $this->json($lieux, Response::HTTP_OK, [], ['groups'=>'listeLieux']);
    }
}