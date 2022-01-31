<?php

declare(strict_types=1);

namespace Tuzex\Cqrs\Messenger\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Tuzex\Cqrs\Command;
use Tuzex\Cqrs\Messenger\Exception\NoHandlerForCommandException;
use Tuzex\Cqrs\Messenger\MessengerCommandBus;

final class MessengerCommandBusTest extends TestCase
{
    public function testItDispatchesCommandToMessageBus(): void
    {
        $command = $this->mockCommand();
        $commandBus = new MessengerCommandBus($this->mockMessageBus($command));

        $commandBus->dispatch($command);
    }

    public function testItThrowsExceptionIfCommandHandlerNotExists(): void
    {
        $command = $this->mockCommand();
        $commandBus = new MessengerCommandBus($this->mockMessageBus($command, false));

        $this->expectException(NoHandlerForCommandException::class);
        $commandBus->dispatch($command);
    }

    private function mockCommand(): Command
    {
        return $this->createMock(Command::class);
    }

    private function mockMessageBus(Command $command, bool $handle = true): MessageBusInterface
    {
        $messageBus = $this->createMock(MessageBusInterface::class);
        $dispatchMethod = $messageBus->expects($this->once())
            ->method('dispatch')
            ->willReturn(
                new Envelope($command)
            );

        if (! $handle) {
            $dispatchMethod->willThrowException(new NoHandlerForMessageException());
        }

        return $messageBus;
    }
}
