<?php

declare(strict_types=1);

namespace Application\Model\Entity;

use Application\Model\SlugTrait;

class QuizEntity
{
    use SlugTrait;

    protected $quiz_id;
    protected $auth_id;
    protected $category_id;
    protected $title;
    protected $question;
    protected $status;
    protected $allow;
    protected $total;
    protected $comments;
    protected $views;
    protected $timeout;
    protected $created;
    #app_categories table column
    protected $category;
    #user_auth table column
    protected $username;

    public function getQuizId() : int
    {
        return $this->quiz_id;
    }

    public function setQuizId($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function getAuthId()
    {
        return $this->auth_id;
    }

    public function setAuthId($auth_id)
    {
        $this->auth_id = $auth_id;
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestion($question) : void
    {
        $this->question = $question;
    }

    public function getStatus() : int
    {
        return $this->status;
    }

    public function setStatus($status) : void
    {
        $this->status = $status;
    }

    public function getAllow()
    {
        return $this->allow;
    }

    public function setAllow($allow)
    {
        $this->allow = $allow;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    public function getViews()
    {
        return $this->views;
    }

    public function setViews($views)
    {
        $this->views = $views;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }
}
