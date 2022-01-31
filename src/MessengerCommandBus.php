<?php

declare(strict_types=1);

namespace Tuzex\Cqrs\Messenger;

use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Tuzex\Cqrs\Command;
use Tuzex\Cqrs\CommandBus;
use Tuzex\Cqrs\Messenger\Exception\NoHandlerForCommandException;

final class MessengerCommandBus implements CommandBus
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {}

    public function dispatch(Command $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        } catch (NoHandlerForMessageException $exception) {
            throw new NoHandlerForCommandException($command, $exception);
        }
    }
}
