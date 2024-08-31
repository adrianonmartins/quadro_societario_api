<?php
namespace App\Controller;

use App\Service\SocioService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Sócios", description="Operações relacionadas aos sócios")
 */
class SocioController extends AbstractController
{
    private $socioService;

    public function __construct(SocioService $socioService)
    {
        $this->socioService = $socioService;
    }

    /**
     * @OA\Get(
     *     path="/api/socios",
     *     summary="Lista todos os sócios",
     *     tags={"Sócios"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de sócios",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@OA\Schema(ref="#/components/schemas/Socio"))
     *         )
     *     )
     * )
     */
    #[Route('/api/socios', name: 'api_socios', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $socios = $this->socioService->getAllSocios();
        $data = [];
        foreach ($socios as $socio) {
            $data[] = [
                'id' => $socio->getId(),
                'nome' => $socio->getNome(),
                'cpf' => $socio->getCpf(),
                'empresa' => $socio->getEmpresa()->getId(), // Exibindo apenas o ID da empresa
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * @OA\Get(
     *     path="/api/socios/{id}",
     *     summary="Exibe um sócio específico",
     *     tags={"Sócios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do sócio",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do sócio",
     *         @OA\JsonContent(ref=@OA\Schema(ref="#/components/schemas/Socio"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sócio não encontrado"
     *     )
     * )
     */
    #[Route('/api/socios/{id}', name: 'api_socio_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $socio = $this->socioService->getSocioById($id);
        if (!$socio) {
            return new JsonResponse(['error' => 'Sócio não encontrado'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse([
            'id' => $socio->getId(),
            'nome' => $socio->getNome(),
            'cpf' => $socio->getCpf(),
            'empresa' => $socio->getEmpresa()->getId(), // Exibindo apenas o ID da empresa
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/socios",
     *     summary="Cria um novo sócio",
     *     tags={"Sócios"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="cpf", type="string"),
     *             @OA\Property(property="empresa", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Sócio criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", format="int64")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Request body contendo valores inválidos ou campos obrigatórios não preenchidos."
     *     )
     * )
     */
    #[Route('/api/socios', name: 'api_socio_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $socio = $this->socioService->createSocio($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['id' => $socio->getId()], Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/socios/{id}",
     *     summary="Atualiza um sócio existente",
     *     tags={"Sócios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do sócio",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="cpf", type="string"),
     *             @OA\Property(property="empresa", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sócio atualizado com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Request body contendo valores inválidos ou campos obrigatórios não preenchidos."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sócio não encontrado"
     *     )
     * )
     */
    #[Route('/api/socios/{id}', name: 'api_socio_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $socio = $this->socioService->getSocioById($id);
        if (!$socio) {
            return new JsonResponse(['error' => 'Sócio não encontrado'], Response::HTTP_NOT_FOUND);
        }
        try {
            $this->socioService->updateSocio($socio, $data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse(['status' => 'Sócio atualizado']);
    }

    /**
     * @OA\Delete(
     *     path="/api/socios/{id}",
     *     summary="Remove um sócio existente",
     *     tags={"Sócios"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do sócio",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sócio removido com sucesso"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Sócio não encontrado"
     *     )
     * )
     */
    #[Route('/api/socios/{id}', name: 'api_socio_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $socio = $this->socioService->getSocioById($id);
        if (!$socio) {
            return new JsonResponse(['error' => 'Sócio não encontrado'], Response::HTTP_NOT_FOUND);
        }
        $this->socioService->deleteSocio($socio);
        return new JsonResponse(['status' => 'Sócio removido']);
    }
}