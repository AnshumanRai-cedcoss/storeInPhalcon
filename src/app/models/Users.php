<?php

use Phalcon\Mvc\Model;

class Users extends Model
{
    public $id;
    public $user_name;
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    public $role;
    public $status;
}