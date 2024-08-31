<?php

namespace App\Service;

use App\Entity\Empresa;
use App\Repository\EmpresaRepository;
use Doctrine\ORM\EntityManagerInterface;

class EmpresaService
{
    private $em;
    private $empresaRepository;

    public function __construct(EntityManagerInterface $em, EmpresaRepository $empresaRepository)
    {
        $this->em = $em;
        $this->empresaRepository = $empresaRepository;
    }

    public function getAllEmpresas(): array
    {
        return $this->empresaRepository->findEmpresasWithSocios();
    }

    public function getEmpresaById(int $id): ?Empresa
    {
        return $this->empresaRepository->find($id);
    }

    public function createEmpresa(array $data): Empresa
    {
        $empresa = new Empresa();
        $empresa->setRazaoSocial($data['razaoSocial']); // Ajuste aqui
        $empresa->setCnpj($data['cnpj']);
        $this->em->persist($empresa);
        $this->em->flush();
        return $empresa;
    }


    public function updateEmpresa(Empresa $empresa, array $data): void
    {
        $empresa->setRazaoSocial($data['razaoSocial']);
        $empresa->setCnpj($data['cnpj']);
        $this->em->flush();
    }

    public function deleteEmpresa(Empresa $empresa): void
    {
        $this->em->remove($empresa);
        $this->em->flush();
    }
}


