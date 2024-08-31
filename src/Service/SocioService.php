<?php

namespace App\Service;

use App\Entity\Socio;
use App\Entity\Empresa;
use Doctrine\ORM\EntityManagerInterface;

class SocioService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getAllSocios(): array
    {
        return $this->em->getRepository(Socio::class)->findAll();
    }

    public function getSocioById(int $id): ?Socio
    {
        return $this->em->getRepository(Socio::class)->find($id);
    }

    public function createSocio(array $data): Socio
    {
        $empresa = $this->em->getRepository(Empresa::class)->find($data['empresa_id']);
        if (!$empresa) {
            throw new \Exception('Empresa não encontrada');
        }
        $socio = new Socio();
        $socio->setNome($data['nome']);
        $socio->setCpf($data['cpf']);
        $socio->setEmpresa($empresa);
        $this->em->persist($socio);
        $this->em->flush();
        return $socio;
    }

    public function updateSocio(Socio $socio, array $data): void
    {
        $empresa = $this->em->getRepository(Empresa::class)->find($data['empresa_id']);
        if (!$empresa) {
            throw new \Exception('Empresa não encontrada');
        }
        $socio->setNome($data['nome']);
        $socio->setCpf($data['cpf']);
        $socio->setEmpresa($empresa);
        $this->em->flush();
    }

    public function deleteSocio(Socio $socio): void
    {
        $this->em->remove($socio);
        $this->em->flush();
    }
}
