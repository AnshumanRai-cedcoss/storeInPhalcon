<?php

use Phalcon\Mvc\Controller;


class ProductsController extends Controller
{
    public function indexAction()
    {
        $this->session = $this->container->getSession();
        $this->view->products = Products::find();
    }

    public function viewDetailAction()
    {
        if(count($this->request->getPost()) > 0)
        {
           // print_r($this->request->getPost());
           $result = Products::findFirst($this->request->getPost()["id"]);
           $result = json_decode(json_encode($result));
           $this->view->product = $result ;
        }
        else 
        {
            $this->response->redirect("products");
        }
            
    }
 
    public function addCartAction()
    {
        
        if(isset($this->request->getPost()["deleteCart"]))
        {
            
            $res = $this->session->cart;
            $key = $this->request->getPost()["delKey"];
            array_splice($res,$key,1);  
            $this->session->set('cart',$res);
            $this->view->cart = $this->session->cart;    
        }
        else if(isset($this->request->getPost()["UpdateCart"]))
        {
            $res = $this->session->cart;
            $qua = $this->request->getPost()["quantity"];
            $key = $this->request->getPost()["upKey"];
            $res[$key]["quantity"] = $qua; 
            $this->session->set('cart',$res);
            $this->view->cart = $this->session->cart;     
        }
        else if(isset($this->request->getPost()["cartBtn"]))
        {    
                $result = Products::findFirst($this->request->getPost()["id"]);
                $result = json_decode(json_encode($result));
                echo "<pre>";
                
                $res =array(
                "id" => $result->product_id ,
                "name" => $result->product_name,
                "price" => $result->product_sale_price,
                "quantity" => 1
                );  
                if($this->session->has('cart'))
                {   
                    $nres = array();
                    $flag = 0;
                    $result = $this->session->cart;
                    $id = $this->request->getPost()["id"];
                    foreach ($result as $key => $value) {
                        if($value["id"] == $id)
                        {
                        $flag = 1;
                        $result[$key]["quantity"] += 1;
                        print_r($result);
                        }
                    }
                    if($flag == 0)
                    array_push($result,$res);
                    $nres = $result;
                } 
                else
                {
                    $nres = array();
                    $nres[0] = $res ; 
                }
                
                    $this->session->set('cart',$nres);
                
                
                $this->view->cart = $this->session->cart;
                
        } 
        else 
        {
            $this->view->cart = $this->session->cart;
        }
        
    }
  
   public function checkoutAction()
   {
       if(!isset($this->session->user))
       {
            $this->response->redirect("login");
       }
       else if(isset($this->request->getPost()["orderBtn"]))
       {
           $res = $this->request->getPost();
           $or = json_encode($this->session->cart);
          
           $add = json_encode("H-No-".$res["add1"]." Area-".$res["add2"]." country-".$res["country"]." state-".$res["state"]." pincode-".$res["zip"]);
           $result = array('user_id'=>$res['id'],'fname'=>$res['fname'],'lname'=>$res['lname'],'order_detail'=>$or,'address'=>$add);
           $order = new Orders();
        
            $order->assign(
            $result, 
            [
                'user_id',
                'fname',
                'lname',
                'order_detail',
                'address'
            ]
        );
            
          $success =  $order->save();
          if($success)
          {
            $mess = $this->session->cart;
            $message = true;
            array_push($mess,$message);
            $this->session->set('cart',$mess);
            $this->response->redirect("http://localhost:8080/products/message");
          }
          else {
            $mess = $this->session->cart;
            $message = false;
            array_push($mess,$message);
            $this->session->set('cart',$mess);
            $this->response->redirect("http://localhost:8080/products/message");
          }
       }
       else {
          $this->view->data = $this->session->user;
       }
    
   }

   public function messageAction()
   {
    $this->view->data = $this->session->cart;
   }

}