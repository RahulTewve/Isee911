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
	use OpenTok\Archive;
	use OpenTok\MediaMode;
	use OpenTok\ArchiveMode;
	use OpenTok\ArchiveList;
	use OpenTok\Session;
	
	class Opentokcall extends REST_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	
	 */



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
            
       
			$this->load->library('s3');
            $this->load->model('Model');

           
          
        }


		public function GetVideos_get()
	    { 
			$StreamDetails = $this->Model->get_all('911_StreamDetails');

$res=array();
foreach ($StreamDetails as $value) {
	$url='https://iseeappnew.s3-us-west-1.amazonaws.com/'.$value['apiKey'].'/'.$value['ArchiveId'].'/archive.mp4';
	array_push($res,array('Emergencytype'=>$value['Emergencytype'],'severity'=>$value['severity'],'url'=>$url,'StartDateTime'=>$value['StartDateTime'],'EndDateTime'=>$value['EndDateTime']));
}
			// $listobject=$this->s3->getBucket('iseeappnew');
			// print_r($listobject['46411532/460ef06b-a38d-4c60-9e68-297829917147/archive.mp4']);
			// exit;
			// $apiKey=$this->config->item('apiKey');
            // $apiSecret=$this->config->item('apiSecret');
	    	
			// $opentok = new OpenTok($apiKey, $apiSecret);
			// // $streamList = $opentok->listStreams('1_MX40NjQxMTUzMn5-MTU2Njk4ODU1OTIwNn5Hd3czT0UxN3ZnN3NubWlnd2JubGx0Wnl-QX4');
			// // print_r($streamList);
			// $archiveLists = $opentok->listArchives(2,10);
			// $t = $archiveLists->getItems();
			// $t=$t->array();
			//  $totalCount = $archiveLists->totalCount();

			//  foreach ($t as $value) {
			// // 	foreach (json_decode($value) as $again) {
			// 	print("<pre>".print_r($value,true)."</pre>");
				
			// 	exit;
			// 	}
			// }
			 $result=array();
			 $result['response']=$res;  
			//  $result['count']=$totalCount;  
			 $this->set_response($result, REST_Controller::HTTP_OK);
			//print_r($archives);
		}
		public function startArchive_get(){
			$apiKey=$this->config->item('apiKey');
            $apiSecret=$this->config->item('apiSecret');
	    	
            $opentok = new OpenTok($apiKey, $apiSecret);
			 $sessionId=$this->get('id');
			 try {
			$archive = $opentok->startArchive($sessionId);
			
			log_message('info','startArchive');
            $UpdateData=array('ArchiveId'=>$archive->id);
            $UpdateWhere=array('SessionId'=> $sessionId);
			$update=$this->Model->update_where('911_StreamDetails',$UpdateWhere,$UpdateData);
			$this->set_response($update, REST_Controller::HTTP_OK);
			}

	    	//catch exception
		catch(Exception $e) {
			log_message('Error',$e->getMessage());
			
			$result=array('Res'=>$e->getMessage());
			$this->set_response($result, REST_Controller::HTTP_NOT_ACCEPTABLE); 
		}
		}
	    public function startLive_get()
	    { 
	    	//$group_id=$this->get('group_id');
	    	//$user_id=$this->get('user_id');
			log_message('info','Starting Tokbox');
	    	$apiKey=$this->config->item('apiKey');
            $apiSecret=$this->config->item('apiSecret');
	    	
            $opentok = new OpenTok($apiKey, $apiSecret);


			$sessionOptions = array(
			    // 'archiveMode' => ArchiveMode::ALWAYS,
			    'mediaMode' => MediaMode::ROUTED
			);
			$session = $opentok->createSession($sessionOptions);
						$sessionId = $session->getSessionId();
					
						 $token = $opentok->generateToken($sessionId); 
						//  	Emergencytype,severity 
			 $InsertData=array('session_id'=> $sessionId,'token'=> $token);
			$res=$this->Model->insert_to('details',$InsertData);
			
			$InsertData=array('SessionId'=> $sessionId,'Token'=> $token,'apiKey'=>$apiKey,'severity'=>$this->get('severity'),'Emergencytype'=>$this->get('Emergencytype'));
			$ress=$this->Model->insert_to('911_StreamDetails',$InsertData);
			log_message('info','Session Details'.json_encode($InsertData));

			 $result['response']='Success';  
				$result['sessionId']=$sessionId; 
				$result['apiKey']=$apiKey;
				$result['token']=$token;
				$result['id']=$ress;
				$this->set_response($result, REST_Controller::HTTP_OK);
			

	    }
		public function end_Live_get()
	    {
			$id=$this->get('id');
			 $date = date('Y-m-d h:i:s', time());
			$UpdateData=array('EndDateTime'=>$date);
            $UpdateWhere=array('Id'=> $id);
			$update=$this->Model->update_where('911_StreamDetails',$UpdateWhere,$UpdateData);
			$where=array('detail_id' => $id);
			$delete=$this->Model->delete_from_where('details',$where);
			
			    $result['response']='Success';  
				$result['id']=$id;
				$this->set_response($result, REST_Controller::HTTP_OK);

		}
		
		public function list_get()
	    {
			
			$list=$this->Model->get_all('details');
			$result['response']='Success';  
			$result['list']=$list;
			log_message('info','got list');
			$this->set_response($result, REST_Controller::HTTP_OK);
		}
		
		

	   

}