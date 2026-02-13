<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Entity;

class ActionBlock
{
    public const string RESOURCE_KEY = 'action_block';

    private ?int $id = null;

    private ?string $title;

    private array $configuration = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
