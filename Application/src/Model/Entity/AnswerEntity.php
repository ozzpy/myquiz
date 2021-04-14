<?php

declare(strict_types=1);

namespace Application\Model\Entity;

class AnswerEntity
{
    protected $answer_id;
    protected $quiz_id;
    protected $answer;
    protected $tally;
    #app_tallies table column
    protected $auth_id;
    protected $created;

    public function getAnswerId(): int
    {
        return $this->answer_id;
    }

    public function setAnswerId(int $answer_id)
    {
        $this->answer_id  = $answer_id;
    }

    public function getQuizId(): int
    {
        return $this->quiz_id;
    }

    public function setQuizId(int $quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function getAnswer()
    {
        return $this->answer;
    }

    public function setAnswer(string $answer)
    {
        $this->answer = $answer;
    }

    public function getTally()
    {
        return $this->tally;
    }

    public function setTally(int $tally)
    {
        $this->tally = $tally;
    }
}
