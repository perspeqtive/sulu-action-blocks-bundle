<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlocksBundle\Entity;

class ActionBlock
{
    public const string RESOURCE_KEY = 'action_block';

    private ?int $id = null;

    private ?string $title;

    private ?string $action;

    private array $configuration = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'action' => $this->action,
            'configuration' => $this->configuration,
        ];
    }
}
