<?php

namespace Bupple\Engine\Core\Drivers;

use Bupple\Engine\Core\Drivers\Contracts\ChatDriverInterface;
use GuzzleHttp\Client;

abstract class AbstractChatDriver implements ChatDriverInterface
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
