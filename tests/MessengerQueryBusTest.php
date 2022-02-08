<?php

declare(strict_types=1);

namespace Tuzex\Cqrs\Messenger\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Tuzex\Cqrs\Messenger\Exception\NoHandlerForQueryException;
use Tuzex\Cqrs\Messenger\MessengerQueryBus;
use Tuzex\Cqrs\Query;
use Tuzex\Cqrs\QueryHandler;

final class MessengerQueryBusTest extends TestCase
{
    public function testItReturnsQueryResult(): void
    {
        $query = $this->mockQuery();
        $queryBus = new MessengerQueryBus(
            $this->mockMessageBus($query, [
                new HandledStamp('', QueryHandler::class),
            ])
        );

        $this->assertIsString($queryBus->dispatch($query));
    }

    public function testItThrowsExceptionIfQueryHandlerNotExists(): void
    {
        $query = $this->mockQuery();
        $queryBus = new MessengerQueryBus($this->mockMessageBus($query, handle: false));

        $this->expectException(NoHandlerForQueryException::class);
        $queryBus->dispatch($query);
    }

    private function mockQuery(): Query
    {
        return $this->createMock(Query::class);
    }

    private function mockMessageBus(Query $query, array $stamps = [], bool $handle = true): MessageBusInterface
    {
        $envelope = new Envelope($query, $stamps);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $dispatchMethod = $messageBus->expects($this->once())
            ->method('dispatch')
            ->willReturn($envelope);

        if (! $handle) {
            $dispatchMethod->willThrowException(new NoHandlerForMessageException());
        }

        return $messageBus;
    }
}
