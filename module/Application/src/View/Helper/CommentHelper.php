<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Application\Form\Comment\CreateForm;
use Application\Form\Comment\EditForm;
use Application\Model\Table\CommentsTable;
use Laminas\Router\RouteMatch;
use Laminas\View\Helper\AbstractHelper;

class CommentHelper extends AbstractHelper
{
    protected $commentsTable;
    protected $routeMatch;

    public function getCommentsTable()
    {
        return $this->commentsTable;
    }

    public function setCommentsTable(CommentsTable $commentsTable)
    {
        $this->commentsTable = $commentsTable;
        return $this;
    }

    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    public function setRouteMatch($routeMatch)
    {
        if (! $routeMatch instanceof RouteMatch) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s expects a %s or %s instance; received %s',
                    __METHOD__,
                    RouteMatch::class,
                    (is_object($routeMatch) ?
                        get_class($routeMatch) :
                        gettype($routeMatch)
                    )
                )
            );
        }

        $this->routeMatch = $routeMatch;
        return $this;
    }

    public function getRouteParam(string $param)
    {
        return $this->getRouteMatch()->getParam($param, $default = null);
    }

    public function getRouteName()
    {
        return $this->getRouteMatch()->getMatchedRouteName();
    }

    public function getQuizId()
    {
        return $this->getRouteParam('id');
    }

    public function getCommentForm()
    {
        return $this->getView()->partial(
            'partial/comment-create.phtml',
            [
                'form' => new CreateForm(),
                'quizId' => $this->getQuizId(),
            ]
        );
    }

    public function getCommentsList()
    {
        return $this->getView()->partial(
            'partial/comment-view.phtml',
            [
                'comments' => $this->getCommentsTable()->fetchCommentsByQuizId(
                    (int) $this->getQuizId()
                )
            ]
        );
    }
}
