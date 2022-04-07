<?php

use Phalcon\Mvc\Controller;
use Phalcon\Escaper;
use Phalcon\Http\Message\Stream;
use Phalcon\Http\Message\UploadedFile;

class AdminController extends Controller
{   
    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->session = $this->container->getSession();
    //  print_r($this->session->user);
        $this->view->users = Users::find(
            [
                'columns'    => '*',
                'conditions' => 'role = ?1  ',
                'bind'       => [
                    1 => "user",
                ]
                ]
        );
    }
    
    public function productsAction()
    {
        $this->view->products = Products::find();
    }

    /**
     * addUser
     *
     * @return void
     */
    public function addUserAction()
    {
        $res = $this->request->getPost() ;
      
         if($res["password"] != $res["rpass"])
         {
            return "Password not matched";
         }
         else if(count($res) > 0)
         {
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
   public function editAction()
   {

    if(isset($this->request->getPost()["submit2"]))
    {
        $res = $this->request->getPost();
        $img = $this->request->getUploadedFiles();
        $imgName = $img[0]->getname();   

       
        $product = Products::findFirst($res["prodID"]);
        $product->product_sale_price = $res["sale"];
        $product->product_list_price = $res["list"];
        $product->product_name = $res["pname"];
        $product->product_details = $res["detail"];
        if($imgName != "")
        {
            if(move_uploaded_file($img[0]->getTempname(), "../public/images/$imgName"))
            {
                unlink("../public/images/$product->product_image");
                $product->product_image = $imgName;
            }
            
            }
            $success = $product->save();  
            $this->response->redirect("admin/products"); 
        }
 
        

    $product = Products::findFirst($this->request->getPost()["chId"]);
    $product = json_decode(json_encode($product));
    $this->view->product = $product;
    }
    
    
  public function ordersAction()
  {
    $this->view->orders = json_decode(json_encode(Orders::find()));
  }
    public function addProductAction()
    {
           if(count($this->request->getPost()) > 0 )
           {
                 $res = $this->request->getPost();
                 $img  = $this->request->getUploadedFiles();
                //  echo "<pre>";
                //  print_r($img);
                //  die;
                  $imgName = $img[0]->getname(); 
                  echo $img[0]->getTempname()." "; 
                  echo($imgName);
                  $product = new Products();
                  $res["product_image"] = $imgName ;
                  
                  $escaper = new Escaper();
                //  $escaper->escapeHtml($title);

                  $escInput = array(
                     "product_name" => $escaper->escapeHtml($res["product_name"]),
                     "product_details" => $escaper->escapeHtml($res["product_details"]),
                     "product_sale_price" => $escaper->escapeHtml($res["product_sale_price"]),
                     "product_list_price" => $escaper->escapeHtml($res["product_list_price"]),
                     "product_image" => $escaper->escapeHtml($res["product_image"])
                  ); 
                  $product->assign(
                    $escInput, 
                    [
                        'product_name',
                        'product_details',
                        'product_sale_price',
                        'product_list_price',
                        'product_image'
                    ]
                ); 
                move_uploaded_file($img[0]->getTempname(), "../public/images/$imgName");

                $success = $product->save();
              if($success)
              {
                
                 $this->response->redirect("admin/products");
              } else {
                 // $message = "There was some error";
                  $this->view->message = "error because ".implode("<br> ",$product->getMessages()) ;
              }
               
                }     
           }
    


  

    /**
     * change status of user 
     *
     * @return void
     */
    public function statusAction()
    {
        $id = $this->request->getPost()["chId"];
        $st = $this->request->getPost()["status"];
      //  die($st);
        $all = Users::findFirst($id); //finding user by id
        // print_r(json_encode($all));
        // die;
        if($st == "approved")
        {
           // die($all->status);
            $all->status = 'pending';
        } 
        else 
        {
            $all->status = 'approved'; 
        }
        $all->save();
        
        $this->response->redirect("admin");
    }

    /**
     * delete user from database
     *
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->request->getPost()["delId"];
        $useFor = $this->request->getPost()["action"];
        if($useFor == "Products")
        {
            $products =  Products::findFirst($id); //finding products by id
            $products->delete();
            unlink("../public/images/$products->product_image");
            $this->response->redirect("admin/products");  
        }
        else if($useFor == "Users")
        {
            $user =  Users::findFirst($id); //finding user by id
            $user->delete();
            $this->response->redirect("admin"); 
        }
    }
}