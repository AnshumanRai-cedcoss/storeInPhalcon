<?php

use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
    public function indexAction()
    {   
        $this->session = $this->container->getSession();
         $res = $this->request->getPost();
        if (isset($res["input1"]) && isset($res["password"])) {
               // $res = new Users;
                $res = Users::findFirst(
                    [
                    'columns'    => '*',
                    'conditions' => 'email = ?1  AND password = ?2',
                    'bind'       => [
                        1 => $res["input1"],
                        2 => $res["password"],
                    ]
                    ]
                );
                
              if(count((array)$res) > 0 )
              {  
                 
                 if($res->role == "admin")
                 {
                    $this->session->set('user',json_decode(json_encode($res)));
                    header("location:/admin"); 
                 }
                 else {
                   if($res->status == "pending")
                   {
                       $msg = "Waiting to be approved! Please try after some time";
                   }
                   else {
                    $this->session->set('user',json_decode(json_encode($res)));
                    header("location:/products");
                   }
                 }
                
              }
              else {
                  $msg  = "Wrong Email or Password";  
              }
         }
         $this->view->message = $msg ;
    }

    public function verifyAction()
    { 
    }
}
