<?php

use Phalcon\Mvc\Model;
use Phalcon\Image;

class Orders extends Model
{
    public $order_id;
    public $user_id;
    public $fname;
    public $lname;
    public $order_detail;
    public $address;
}