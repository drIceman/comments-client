<?php

declare(strict_types=1);

namespace Example\CommentsClient\Types;

interface CanCreateSelfFromArrayInterface
{
    /**
     * Создание экземпляра из входного массива.
     *
     * @param array $data
     *
     * @return self
     */
    public static function fromArray(array $data): self;
}
