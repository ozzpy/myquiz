<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Search\FindForm;
use Application\Model\Table\QuizzesTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class SearchController extends AbstractActionController
{
    private $quizzesTable;

    public function __construct(QuizzesTable $quizzesTable)
    {
        $this->quizzesTable = $quizzesTable;
    }

    public function indexAction()
    {
        $findForm = new FindForm();
        return new ViewModel(['form' => $findForm]);
    }
}
