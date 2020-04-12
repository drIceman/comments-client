<?php

declare(strict_types=1);

namespace Example\CommentsClient\Types;

interface CommentTypeInterface
{
    /**
     * Получить ID комментария.
     */
    public function getId(): int;

    /**
     * Получить название комментария.
     */
    public function getName(): string;

    /**
     * Получить текст комментария.
     */
    public function getText(): string;
}
