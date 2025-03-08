<?php

namespace Bupple\Engine\Core\Drivers\Contracts;

interface ChatDriverInterface
{
    /**
     * Send a message to the AI and get a response.
     *
     * @param array $messages
     * @return array
     */
    public function send(array $messages): array;

    /**
     * Stream a chat completion.
     *
     * @param array $messages
     * @return \Generator
     */
    public function stream(array $messages): \Generator;

    /**
     * Get the driver's configuration.
     *
     * @return array
     */
    public function getConfig(): array;
}
