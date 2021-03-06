<?php

declare(strict_types=1);

namespace Tuzex\Cqrs\Messenger\Exception;

use LogicException;
use Throwable;
use Tuzex\Cqrs\Command;

final class NoHandlerForCommandException extends LogicException
{
    public function __construct(Command $command, Throwable $previous)
    {
        parent::__construct(sprintf('Handler for command "%s" not found.', $command::class), $previous->getCode(), $previous);
    }
}
