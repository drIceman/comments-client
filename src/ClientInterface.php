<?php

declare(strict_types=1);

namespace Example\CommentsClient;

use Example\CommentsClient\Exceptions\BadRequestException;
use Example\CommentsClient\Exceptions\BadResponseException;
use Example\CommentsClient\Responses\CommentsResponse;
use Example\CommentsClient\Responses\DevPingResponse;
use InvalidArgumentException;

interface ClientInterface
{
    /**
     * Тестовое соединение.
     *
     * @param string $value Любая JSON строка (должна вернуться обратно)
     *
     * @return DevPingResponse
     *
     * @throws BadRequestException
     * @throws BadResponseException
     */
    public function devPing(?string $value = null): DevPingResponse;

    /**
     * Получить список комментариев.
     *
     * @return CommentsResponse
     *
     * @throws BadRequestException
     * @throws BadResponseException
     */
    public function getComments(): CommentsResponse;

    /**
     * Добавить комментарий.
     *
     * @param string $name
     * @param string $text
     *
     * @return CommentsResponse
     *
     * @throws InvalidArgumentException
     * @throws BadRequestException
     * @throws BadResponseException
     */
    public function createComment(string $name, string $text): CommentsResponse;

    /**
     * Обновить комментарий.
     *
     * @param int $id
     * @param string $name
     * @param string $text
     *
     * @return CommentsResponse
     *
     * @throws InvalidArgumentException
     * @throws BadRequestException
     * @throws BadResponseException
     */
    public function updateComment(int $id, ?string $name, ?string $text): CommentsResponse;
}
