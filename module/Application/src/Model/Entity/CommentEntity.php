<?php

declare(strict_types=1);

namespace Application\Model\Entity;

class CommentEntity
{
    protected $comment_id;
    protected $quiz_id;
    protected $auth_id;
    protected $comment;
    protected $created;
    #user_auth table columns
    protected $username;
    protected $picture;

    public function getCommentId() : int
    {
        return $this->comment_id;
    }

    public function setCommentId(string|int $comment_id): void
    {
        $this->comment_id = $comment_id;
    }

    public function getQuizId() : int
    {
        return $this->quiz_id;
    }

    public function setQuizId(string|int $quiz_id) : void
    {
        $this->quiz_id = $quiz_id;
    }

    public function getAuthId() : int
    {
        return $this->auth_id;
    }

    public function setAuthId(string|int $auth_id) : void
    {
        $this->auth_id = $auth_id;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment) : void
    {
        $this->comment = $comment;
    }

    public function getCreated() : string
    {
        return $this->created;
    }

    public function setCreated(string $created) : void
    {
        $this->created = $created;
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function setUsername(string $username) : void
    {
        $this->username = $username;
    }

    public function getPicture() : string
    {
        return $this->picture;
    }

    public function setPicture($picture) : void
    {
        $this->picture = $picture;
    }
}
