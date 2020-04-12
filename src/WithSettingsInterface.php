<?php

declare(strict_types=1);

namespace Example\CommentsClient;

interface WithSettingsInterface
{
    /**
     * Получить настройки.
     *
     * @return Settings
     */
    public function getSettings(): Settings;
}
