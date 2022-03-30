<?php

use Phalcon\Mvc\Model;
use Phalcon\Image;

class Products extends Model
{
    public $product_id;
    public $product_name;
    public $product_details;
    public $product_sale_price;
    public $product_list_price;
    public $product_image ;
}