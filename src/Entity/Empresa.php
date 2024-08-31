<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
class Empresa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $razaoSocial = null;

    #[ORM\Column(length: 14)]
    private ?string $cnpj = null;

    /**
     * @var Collection<int, Socio>
     */
    #[ORM\OneToMany(targetEntity: Socio::class, mappedBy: 'empresa', orphanRemoval: true)]
    private Collection $socio;

    public function __construct()
    {
        $this->socio = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRazaoSocial(): ?string
    {
        return $this->razaoSocial;
    }

    public function setRazaoSocial(?string $razaoSocial): static
    {
        $this->razaoSocial = $razaoSocial;

        return $this;
    }

    public function getCnpj(): ?string
    {
        return $this->cnpj;
    }

    public function setCnpj(string $cnpj): static
    {
        $this->cnpj = $cnpj;

        return $this;
    }
    public function getSocio(): Collection
    {
        return $this->socio;
    }

    public function addSocio(Socio $socio): static
    {
        if (!$this->socio->contains($socio)) {
            $this->socio->add($socio);
            $socio->setEmpresa($this);
        }

        return $this;
    }

    public function removeSocio(Socio $socio): static
    {
        if ($this->socio->removeElement($socio)) {
            if ($socio->getEmpresa() === $this) {
                $socio->setEmpresa(null);
            }
        }

        return $this;
    }
}
