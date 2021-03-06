<?php

namespace Front;

use Jennifer\View\ViewInterface;
use thedaysoflife\Model\User;
use thedaysoflife\View\ViewFront;

class Index extends ViewFront implements ViewInterface
{
    protected $contentTemplate = "front/index";

    public function __construct(User $user = null)
    {
        parent::__construct();
        $this->user = $user ? $user : new User();
    }

    public function prepare()
    {
        $days = $this->user->getDays(0, User::ORDER_BY_ID);
        $this->setData(["days" => $days, "order" => User::ORDER_BY_ID]);

        return $this;
    }
}