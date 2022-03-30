<?php

use Phalcon\Mvc\Controller;

class UserController extends Controller{

    public function IndexAction()
     {
        $res = $this->request->getPost() ;
      
         if($res["password"] != $res["rpass"])
         {
            return "Password not matched";
         }
         else if(count($res) > 0 )
         {
            // print_r($this->request->getPost());
            // die("gdags");
            $user = new Users();
        
            $user->assign(
            $this->request->getPost(), 
            [
                'user_name',
                'first_name',
                'last_name',
                'email',
                'password'
            ]
        );
        try
    {    
        $success = $user->save(); 
        if($success){
            $this->view->message = "Register succesfully";
            $this->view->success = $success;
        }else{
            $this->view->message = "Not Register succesfully due to following reason: <br>".implode("<br>", $user->getMessages());
            $this->view->success = false;
        }     
    }
    catch (Exception $e)
    {
        $this->view->message = "Email already exists ! Kindly Sign In";
        $this->view->success = false;
    }
   
    }
    }

    public function signoutAction()
    {
       $this->session->remove('cart');
       $this->session->remove('user');
       $this->session->destroy();
      
       $this->response->redirect("http://localhost:8080");
    }

    public function userdashAction()
    {
        if(!isset($this->session->user))
        {
            $this->response->redirect("login");
        }
        else {
            $user = Users::findFirst($this->session->user->id);
            $user = json_decode(json_encode($user));
            $this->session->user = $user ;
            $this->view->user = $user;
        }
        
    }

    public function editAction()
    {
        if(!isset($this->session->user))
        {
            $this->response->redirect("login");
        }
        else {
            if(count($this->request->getPost()) > 0)
            {
            $user = new Users();

            $user = Users::findFirst($this->request->getPost()["id"]);
            $user->first_name = $this->request->getPost()["first_name"];
            $user->last_name = $this->request->getPost()["last_name"];
            $user->password = $this->request->getPost()["password"];

            $success = $user->save(); 
            echo($success);
            $this->response->redirect("user/userdash");
        
        }
        
    }
}
}