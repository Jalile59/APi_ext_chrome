<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IdUserRepository")
 */
class IdUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $idapplication;

    /**
     * @ORM\Column(type="datetime")
     */
    private $InstalledDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Input", mappedBy="idUser")
     */
    private $link;

    public function __construct()
    {
        $this->link = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdapplication(): ?string
    {
        return $this->idapplication;
    }

    public function setIdapplication(string $idapplication): self
    {
        $this->idapplication = $idapplication;

        return $this;
    }

    public function getInstalledDate(): ?\DateTimeInterface
    {
        return $this->InstalledDate;
    }

    public function setInstalledDate(\DateTimeInterface $InstalledDate): self
    {
        $this->InstalledDate = $InstalledDate;

        return $this;
    }

    /**
     * @return Collection|Input[]
     */
    public function getLink(): Collection
    {
        return $this->link;
    }

    public function addLink(Input $link): self
    {
        if (!$this->link->contains($link)) {
            $this->link[] = $link;
            $link->setIdUser($this);
        }

        return $this;
    }

    public function removeLink(Input $link): self
    {
        if ($this->link->contains($link)) {
            $this->link->removeElement($link);
            // set the owning side to null (unless already changed)
            if ($link->getIdUser() === $this) {
                $link->setIdUser(null);
            }
        }

        return $this;
    }
}
