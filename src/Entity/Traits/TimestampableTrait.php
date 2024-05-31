<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Monolog\DateTimeImmutable;

trait TimestampableTrait
{
    #[Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist() : void
    {
        $this->setCreatedAt(new \DateTimeImmutable);
        $this->setUpdatedAt(new \DateTimeImmutable);
    }

    #[ORM\PreUpdate]
    public function onPreUpdate() : void
    {
        $this->setUpdatedAt(new \DateTimeImmutable);
    }
}