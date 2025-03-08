<?php

namespace BuppleEngine\Core\Drivers\Engine;

use BuppleEngine\Core\Drivers\Engine\Contracts\EngineDriverInterface;
use GuzzleHttp\Client;

abstract class AbstractEngineDriver implements EngineDriverInterface
{
    protected Client $client;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $this->getBaseUri(),
            'headers' => $this->getHeaders(),
        ]);
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    abstract protected function getBaseUri(): string;
    abstract protected function getHeaders(): array;
    abstract protected function formatMessages(array $messages): array;
    abstract protected function formatOptions(array $options): array;
}
