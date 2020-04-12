<?php

declare(strict_types=1);

namespace Example\CommentsClient\Types;

class CommentType implements CommentTypeInterface, CanCreateSelfFromArrayInterface
{
    protected int $id;
    protected string $name;
    protected string $text;

    /**
     * Создание нового экземпляра.
     *
     * @param int $id
     * @param string $name
     * @param string $text
     */
    public function __construct(int $id, string $name, string $text)
    {
        $this->id = $id;
        $this->name = $name;
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $data): self
    {
        return new static(
            $data['id'],
            $data['name'],
            $data['text'],
        );
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getText(): string
    {
        return $this->text;
    }
}
