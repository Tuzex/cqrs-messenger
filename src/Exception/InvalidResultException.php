<?php

declare(strict_types=1);

namespace Tuzex\Cqrs\Messenger\Exception;

use LogicException;
use Tuzex\Cqrs\Query;

final class InvalidResultException extends LogicException
{
    public function __construct(Query $query)
    {
        parent::__construct(sprintf('Result from "%s" must implement "iterable".', $query::class));
    }
}
