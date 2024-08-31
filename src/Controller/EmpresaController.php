<?php

namespace App\Controller;

use App\Service\EmpresaService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;

class EmpresaController extends AbstractController
{
    private $empresaService;

    public function __construct(EmpresaService $empresaService)
    {
        $this->empresaService = $empresaService;
    }

    /**
     * @OA\Tag(name="Empresas")
     * @OA\Get(
     *     path="/api/empresas",
     *     summary="Lista todas as empresas",
     *     tags={"Empresas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de empresas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref=@OA\Schema(ref="#/components/schemas/Empresa"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Request body contendo valores inválidos ou campos obrigatórios não preenchidos."
     *     )
     * )
     */
    #[Route('/api/empresas', name: 'api_empresas', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $empresas = $this->empresaService->getAllEmpresas();
        $data = [];
        foreach ($empresas as $empresa) {
            $data[] = [
                'id' => $empresa->getId(),
                'nome' => $empresa->getRazaoSocial(),
                'cnpj' => $empresa->getCnpj(),
                'socios' => array_map(function($socio) {
                    return [
                        'id' => $socio->getId(),
                        'nome' => $socio->getNome(),
                        'cpf' => $socio->getCpf(),
                    ];
                }, $empresa->getSocio()->toArray())
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * @OA\Tag(name="Empresas")
     * @OA\Get(
     *     path="/api/empresas/{id}",
     *     summary="Detalha uma empresa",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes da empresa",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="cnpj", type="string"),
     *             @OA\Property(
     *                 property="socios",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="nome", type="string"),
     *                     @OA\Property(property="cpf", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa não encontrada"
     *     )
     * )
     */
    #[Route('/api/empresas/{id}', name: 'api_empresa_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $empresa = $this->empresaService->getEmpresaById($id);
        if (!$empresa) {
            return new JsonResponse(['error' => 'Empresa não encontrada'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse([
            'id' => $empresa->getId(),
            'nome' => $empresa->getRazaoSocial(),
            'cnpj' => $empresa->getCnpj(),
            'socios' => array_map(function($socio) {
                return [
                    'id' => $socio->getId(),
                    'nome' => $socio->getNome(),
                    'cpf' => $socio->getCpf(),
                ];
            }, $empresa->getSocio()->toArray())
        ]);
    }

    /**
     * @OA\Tag(name="Empresas")
     * @OA\Post(
     *     path="/api/empresas",
     *     summary="Cria uma nova empresa",
     *     tags={"Empresas"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"nome", "cnpj"},
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="cnpj", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Empresa criada",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na criação da empresa"
     *     )
     * )
     */
    #[Route('/api/empresas', name: 'api_empresa_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Verificação das chaves corretas
        // Certifique-se de que os nomes das chaves correspondem ao que você espera
        if (empty($data['razaoSocial']) || empty($data['cnpj'])) {
            return new JsonResponse(['error' => 'Dados inválidos'], Response::HTTP_BAD_REQUEST);
        }

        $empresa = $this->empresaService->createEmpresa($data);
        return new JsonResponse(['id' => $empresa->getId()], Response::HTTP_CREATED);
    }

    /**
     * @OA\Tag(name="Empresas")
     * @OA\Put(
     *     path="/api/empresas/{id}",
     *     summary="Atualiza uma empresa existente",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="nome", type="string"),
     *             @OA\Property(property="cnpj", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa atualizada"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa não encontrada"
     *     )
     * )
     */
    #[Route('/api/empresas/{id}', name: 'api_empresa_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['nome']) || empty($data['cnpj'])) {
            return new JsonResponse(['error' => 'Dados inválidos'], Response::HTTP_BAD_REQUEST);
        }
        $empresa = $this->empresaService->getEmpresaById($id);
        if (!$empresa) {
            return new JsonResponse(['error' => 'Empresa não encontrada'], Response::HTTP_NOT_FOUND);
        }
        $this->empresaService->updateEmpresa($empresa, $data);
        return new JsonResponse(['status' => 'Empresa atualizada']);
    }

    /**
     * @OA\Tag(name="Empresas")
     * @OA\Delete(
     *     path="/api/empresas/{id}",
     *     summary="Remove uma empresa",
     *     tags={"Empresas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Empresa removida"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empresa não encontrada"
     *     )
     * )
     */
    #[Route('/api/empresas/{id}', name: 'api_empresa_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $empresa = $this->empresaService->getEmpresaById($id);
        if (!$empresa) {
            return new JsonResponse(['error' => 'Empresa não encontrada'], Response::HTTP_NOT_FOUND);
        }
        $this->empresaService->deleteEmpresa($empresa);
        return new JsonResponse(['status' => 'Empresa removida']);
    }
}
