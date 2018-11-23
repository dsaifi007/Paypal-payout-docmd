<?php

require(APPPATH.'/libraries/REST_Controller.php');
 //use Restserver\Libraries\REST_Controller;
class Api extends REST_Controller {
    
    public function __construct()
    {

        //$headers = apache_request_headers();
        parent::__construct();
        $this->_auth();
        $this->load->model('book_model');
    }
    private function _auth()
    {
        if (empty($this->input->server('PHP_AUTH_USER')) || empty($this->input->server('PHP_AUTH_PW')))
        {
           header('HTTP/1.0 401 Unauthorized');
           header('HTTP/1.1 401 Unauthorized');
           header('WWW-Authenticate: Basic realm="My Realm"');
           echo 'You must login to use this service'; // User sees this if hit cancel
           die();
        }
        else
        {
            if ($this->input->server('PHP_AUTH_USER')!="admin" || $this->input->server('PHP_AUTH_PW')!=12345) {
                echo 'Wrong email and password';
                die();
            }
        }
    }
    //API - client sends isbn and on valid isbn book information is sent back
    function bookByIsbn_get(){
        $isbn  = $this->get('isbn');
        
        if(!$isbn){

            $this->response("No ISBN specified", 400);

            exit;
        }

        $result = $this->book_model->getbookbyisbn( $isbn );

        if($result){

            $this->response($result, 200); 

            exit;
        } 
        else{

             $this->response("Invalid ISBN", 404);

            exit;
        }
    } 

    //API -  Fetch All books
    function books_get(){
        $result = $this->book_model->getallbooks();

        if($result){

            $this->response($result, 200); 

        } 

        else{

            $this->response("No record found", 404);

        }
    }
     
    //API - create a new book item in database.
    function addBook_post(){
         $name      = $this->post('name');

         $price     = $this->post('price');

         $author    = $this->post('author');

         $category  = $this->post('category');

         $language  = $this->post('language');

         $isbn      = $this->post('isbn');

         $pub_date  = $this->post('publish_date');
        
         if(!$name || !$price || !$author || !$price || !$isbn || !$category){

                $this->response("Enter complete book information to save", 400);

         }else{

            $result = $this->book_model->add(array("name"=>$name, "price"=>$price, "author"=>$author, "category"=>$category, "language"=>$language, "isbn"=>$isbn, "publish_date"=>$pub_date));

            if($result === 0){

                $this->response("Book information coild not be saved. Try again.", 404);

            }else{

                $this->response("success", 200);  
           
            }

        }

    }

    
    //API - update a book 
    function updateBook_put(){
         echo "hello4";
         $name      = $this->put('name');

         $price     = $this->put('price');

         $author    = $this->put('author');

         $category  = $this->put('category');

         $language  = $this->put('language');

         $isbn      = $this->put('isbn');

         $pub_date  = $this->put('publish_date');

         $id        = $this->put('id');
         
         if(!$name || !$price || !$author || !$price || !$isbn || !$category){

                $this->response("Enter complete book information to save", 400);

         }else{
            $result = $this->book_model->update($id, array("name"=>$name, "price"=>$price, "author"=>$author, "category"=>$category, "language"=>$language, "isbn"=>$isbn, "publish_date"=>$pub_date));

            if($result === 0){

                $this->response("Book information could not be saved. Try again.", 404);

            }else{

                $this->response("success", 200);  
            }

        }

    }

    //API - delete a book 
    function deleteBook_delete()
    {

        echo "hello5";
        $id  = $this->delete('id');

        if(!$id){

            $this->response("Parameter missing", 404);

        }
         
        if($this->book_model->delete($id))
        {

            $this->response("Success", 200);

        } 
        else
        {

            $this->response("Failed", 400);

        }

    }
    public function test_get()
    {
        
        $array=["user"=>"danish","email"=>"danish@gmail.com","password"=>sha1("dkn@1234"),"status"=>TRUE];
        if (is_array($array)) {
            $this->response($array);
        }
        else
        {
            $error=["status"=>0];
            $this->response($error);
        }
    }
    public function userlogin_post()
    {
       $input_data=$this->input->post();
       print_r($input_data);
       if ($input_data['email']!='' || $input_data['password']!='') {
           $this->response(["success"]);
       }
       else
       {
            $response['token'] = Authorization::generateToken($input_data);
            $send_response=["status"=>0,"error"=>$response];
            $this->response($send_response);
       }
    }

}
