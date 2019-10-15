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

class Login extends REST_Controller {

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

 
 public function login_post()
        {
        	$res=array();
			$email=$this->post('email');
            $pwd=sha1($this->post('password'));
            $dataSel=array('email' => $email,'password'=>$pwd); //,'is_active'=>1
            $result=$this->Model->get_from_where('users',$dataSel); 
			//print_r($result);exit;
            if(!empty($result))
            {	

                    $res['user']=$result;
					$res['response']='Success';
					$this->set_response($res,200);

            }else
            {
                $res['response']='Failed';
				$res['error']='Invalid username or password';
                $this->set_response($res,500);
            }


        }  

 }       