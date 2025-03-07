<?php

namespace Bupple\Engine\Core\Drivers\Contracts;

interface ChatDriverInterface
{
    public function chat(array $messages, array $options = []): mixed;
    public function stream(array $messages, array $options = []): mixed;
    public function getMemoryDriver(mixed $parentModel): mixed;
}
