<?php

namespace Back;

use Jennifer\Auth\Authentication;
use Jennifer\View\ViewInterface;
use thedaysoflife\Model\Admin;
use thedaysoflife\View\ViewBack;

class Login extends ViewBack implements ViewInterface
{
    protected $title = "Dashboard Login";
    protected $contentTemplate = "back/login";
    protected $requiredPermission = false;

    public function __construct(Admin $admin = null)
    {
        parent::__construct();
        $this->admin = $admin ? $admin : new Admin();
    }

    public function prepare()
    {
        if ($this->request->posted()) {
            if ($this->request->hasPost("email")) {
                $email = $this->request->post["email"];
                $password = $this->authentication->encryptPassword($this->request->post["password"]);
                $row = $this->admin->checkLogin($email, $password);
                $message = "";
                if (isset($row['id'])) {
                    $status = $row['status'];
                    // if user is disable
                    if ($status == Authentication::USER_STATUS_DISABLE) {
                        $message = $this->authentication->messages["USER_STATUS_DISABLE"]["message"];
                    } // valid and active user
                    else if ($status == Authentication::USER_STATUS_ACTIVE) {
                        $jwtData = ["id" => $row["id"],
                            "name" => $row["f_name"] . " " . $row["l_name"],
                            "permission" => $row['permission']];
                        $this->authentication->setJWT($jwtData);
                        $this->authentication->getResponse()->redirect("/back/home/");
                    }
                } else {
                    //if invalid email and password
                    $message = $this->authentication->messages["INVALID_AUTHENTICATION"]["message"];
                }
                $this->setData(["message" => $message]);
            }
        }
        return $this;
    }
}