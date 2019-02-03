<?php

namespace back;

use jennifer\fb\FacebookHelper;
use jennifer\view\ViewInterface;
use thedaysoflife\model\Admin;
use thedaysoflife\view\ViewBack;

class Days extends ViewBack implements ViewInterface
{
    protected $title = "Dashboard :: Days";
    protected $contentTemplate = "back/days";
    protected $fbHelper;

    public function __construct(Admin $admin = null, FacebookHelper $facebookHelper = null)
    {
        parent::__construct();
        $this->admin = $admin ? $admin : new Admin();
        $this->fbHelper = $facebookHelper ? $facebookHelper : new FacebookHelper();
    }

    public function prepare()
    {
        $days = $this->admin->getDayList(1);
        $this->setData(["days" => $days]);
        $this->fbHelper->fbLogin();
        return $this;
    }
}