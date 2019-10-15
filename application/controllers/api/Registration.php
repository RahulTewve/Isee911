<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
        defined('BASEPATH') OR exit('No direct script access allowed');
    //require(APPPATH.'libraries/REST_Controller.php');
//use Restserver\Libraries\REST_Controller;
    require APPPATH . 'libraries/REST_Controller.php';
    require_once APPPATH . '../vendor/autoload.php'; // change path as needed
    use OpenTok\OpenTok;
    use OpenTok\MediaMode;
    use OpenTok\ArchiveMode;

class Registration extends REST_Controller {

     public function __construct()
        {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers:  Authorization,Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
            date_default_timezone_set("Asia/Kolkata");
            $method = $_SERVER['REQUEST_METHOD'];
            if($method == "OPTIONS") {
                die();
            }
            parent::__construct();
            $this->methods['user_get']['limit'] = 500;
            $this->methods['user_post']['limit'] = 100;
            $this->methods['user_delete']['limit'] = 50;
            
            $this->load->model('Model'); 
          
        }

    // public function test_post()
    //     {  $res = array();
    //        $tokenData = array();
				// 		$tokenData['user_id'] = 2; 
				// 		$tokenData['username'] = 'vodacom'; 
				// 		$tokenData['exp'] = 1528354867; 
				// 		$tokenData['email'] = 'vodacom123@gmail.com'; //TODO: Replace with data for token                      
				// 		$token=AUTHORIZATION::generateToken($tokenData);  
				// 		$res['token']= $token;	

						
    //             $this->set_response($res,500);
    //     }

 
   public function register_post(){

      // $uToken=  apache_request_headers()['Authorization'];  
      // $result= FUNC::decodeToken($uToken); // Decoding the generated token to get logged in mobile no and regId                       
      // foreach ($result as $row1) {$userId = $row1->userId;} 

   
      $f_name=$this->post('first_name');
      $l_name=$this->post('last_name');
      $address=$this->post('address');
      $mobile=$this->post('mobile');
      $email=$this->post('email');
      $password=sha1($this->post('password'));
      $status='1';
     // $current_date_time=date('d-m-Y g:i a.',time());

      $InsertRequest=array('first_name'=>$f_name,'last_name'=>$l_name,'address'=>$address,'mobile'=>$mobile,'email'=>$email,'password'=>$password,'user_status'=>$status);
      $request_res=$this->Model->insert_to('users',$InsertRequest);

       // $condition='product.brand_id = brand.brand_id';
       // $where=array('product_id'=>$product_id);
       // //$get_one=$this->Model->get_one('product',$where);
       // $get_one=$this->Model->join_two_return_row('product','brand',$condition,$where);
       
      $response=array();
        if($request_res){
            $response['status']='Success';
            $response['event_id']=$request_res;

            $response['result']='Registered Successfully';
        }else{
            $response['status']='Failed';
            $response['result']='Registration Failed';
        }
      
      $this->set_response($response,200);

    }
 

 }       