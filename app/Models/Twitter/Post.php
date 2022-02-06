<?php

namespace App\Models\Twitter;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Twitter Post Model
 *
 */
class Post implements Arrayable {
    protected string    $id;
    protected string    $text;
    protected User      $author;
    protected Carbon    $createdAt;

    public function __construct(string $id, string $text, User $author, Carbon $createdAt) {
        $this->setId($id)->setText($text)->setAuthor($author)->setCreatedAt($createdAt);
    }

    public function getId() : string {
        return $this->id;
    }

    public function setId(string $id) : self {
        $this->id           = $id;
        return $this;
    }

    public function getText(): string {
        return $this->text;
    }

    public function setText(string $text) : self {
        $this->text         = $text;
        return $this;
    }

    public function getAuthor() : User {
        return $this->author;
    }

    public function setAuthor(User $author) : self {
        $this->author       = $author;
        return $this;
    }

    public function getCreatedAt() : Carbon {
        return $this->createdAt;
    }

    public function setCreatedAt(Carbon $createdAt) : self {
        $this->createdAt    = $createdAt;
        return $this;
    }

    public function getUrl() : string {
        $parts      = [
            $this->getAuthor()->getUrl(),
            "status",
            $this->getId(),
        ];

        return implode("/", $parts);
    }

    public function toArray() {
        return [
            'id'            => $this->getId(),
            'text'          => $this->getText(),
            'created_at'    => $this->getCreatedAt()->toISOString(),
            'author'        => $this->getAuthor()->toArray(),
        ];
    }
}
