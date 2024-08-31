<?php

namespace App\Repository;

use App\Entity\Empresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmpresaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    public function findEmpresasWithSocios(): array
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.socio', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();
    }
}

