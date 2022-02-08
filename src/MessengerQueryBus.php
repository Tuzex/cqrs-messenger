<?php

declare(strict_types=1);

namespace Tuzex\Cqrs\Messenger;

use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Tuzex\Cqrs\Messenger\Exception\InvalidResultException;
use Tuzex\Cqrs\Messenger\Exception\NoHandlerForQueryException;
use Tuzex\Cqrs\Query;
use Tuzex\Cqrs\QueryBus;

final class MessengerQueryBus implements QueryBus
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {}

    public function dispatch(Query $query): mixed
    {
        try {
            $envelope = $this->messageBus->dispatch($query);
        } catch (NoHandlerForMessageException $exception) {
            throw new NoHandlerForQueryException($query, $exception);
        }

        $stamp = $envelope->last(HandledStamp::class);
        if (! $stamp instanceof HandledStamp) {
            throw new InvalidResultException($query);
        }

        return $stamp->getResult();
    }
}
