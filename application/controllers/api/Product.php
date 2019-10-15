<?php  
use Restserver\Libraries\REST_Controller;
//if ( ! defined('BASEPATH')) exit('No direct script access allowed');


//require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';
class Product extends REST_Controller {


 public function __construct()
                {
                    header('Access-Control-Allow-Origin: *');
                    header("Access-Control-Allow-Headers:  Authorization,Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
                    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
          date_default_timezone_set("Asia/Kolkata");
                    $method = $_SERVER['REQUEST_METHOD'];
                    // if($method == "OPTIONS") {
                    //     die();
                    // }
                    parent::__construct();
                   // $this->methods['user_get']['limit'] = 500;
                   // $this->methods['user_post']['limit'] = 100;
                   // $this->methods['user_delete']['limit'] = 50;

                    $this->load->model('Model'); 
                    $data = array();
                }


    // public function __construct()

    // {
    //     parent::__construct();

    //       //$this->load->model('product_model');
    //       $this->load->model('Model'); 

    //      $data = array();
    // }

    public function request_post(){

      // $uToken=  apache_request_headers()['Authorization'];  
      // $result= FUNC::decodeToken($uToken); // Decoding the generated token to get logged in mobile no and regId                       
      // foreach ($result as $row1) {$userId = $row1->userId;} 

   
      $description=$this->post('description');
      $location=$this->post('location');
      $status='Requested';
      $current_date_time=date('d-m-Y g:i a.',time());

      $InsertRequest=array('description'=>$description,'request_location'=>$location,'request_status'=>$status,'request_date_time'=>$current_date_time);
      $request_res=$this->Model->insert_to('request',$InsertRequest);

       // $condition='product.brand_id = brand.brand_id';
       // $where=array('product_id'=>$product_id);
       // //$get_one=$this->Model->get_one('product',$where);
       // $get_one=$this->Model->join_two_return_row('product','brand',$condition,$where);
       
      $response=array();
        if($request_res){
            $response['status']='Success';
            $response['request_id']=$request_res;

            $response['result']='Requsest Submitted Successfully';
        }else{
            $response['status']='Failed';
            $response['result']='Requsest Submition Failed';
        }
      
      $this->set_response($response,200);

    }





     public function confirm_request_post(){

      $path='assets/admin/images/sign';
      $result1=FUNC::uploadImg($path,'Image');
      $image_name=$result1['file_name']; 
      $mainUrl = $this->config->item('mainUrl');
      $url=$mainUrl.'assets/admin/images/sign/'.$image_name;

     
      $status='Received';
      $request_id=$this->post('request_id');
      $username=$this->post('username');
      $current_date_time=date('d-m-Y g:i a.',time());

     // $confirm=array('product_id'=>$product_id,'quantity'=>$quantity,'status'=>$status);
     // $confirm_res=$this->Model->insert_to('asset_type',$confirm);
       $UpdateData=array('request_status'=>$status,'sign'=>$url,'confirmed_date_time'=>$current_date_time);
            $UpdateWhere=array('request_id'=> $request_id);
            $update=$this->Model->update_where('request',$UpdateWhere,$UpdateData);

            if($update){

               $query='select * from request_details where request_id="'.$request_id.'"' ;
               $request_details = $this->Model->run_queryarray($query);
                foreach($request_details as $details){
                   $id=$details->asset_id;
                   $UpdateData=array('status'=>'UnAllocated');
                   $where=array('asset_id'=>$id);
                   $res=$this->Model->update_where('assets',$where,$UpdateData);

                   
                   $au_name = $username;
                   $current_date_time=date('d-m-Y g:i a.',time());
                   $description='Signed by user';
                   $history_status='Received';
                   $InsertHistoryData=array('asset_id'=>$id,'request_id'=>$request_id,'description'=>$description,' history_status'=>$history_status,'datetime'=>$current_date_time,'user'=>$au_name,'sign'=>$url);

                   $history_res=$this->Model->insert_to('assettranshistory',$InsertHistoryData);

                   $description='Asset received by Vodacom';
                   $history_status='Unallocated';

                    $InsertHistoryData1=array('asset_id'=>$id,'request_id'=>$request_id,'description'=>$description,' history_status'=>$history_status,'datetime'=>$current_date_time,'user'=>$au_name);

                   $history_res=$this->Model->insert_to('assettranshistory',$InsertHistoryData1);

                }

              
            }

      $response=array();
        if($update){
            $response['status']='Success';
            $response['res']=$result1;
            $response['request_details']=$request_details;
            $response['result']='Requsest Confirmed Successfully';
           
        }else{
            $response['status']='Failed';
            $response['result']='Requsest Confirmation Failed';
            
        }


          $this->set_response($response,200);

    }




    public function assign_post(){

      $asset_id=$this->post('product_id');
      $request_id=$this->post('request_id');
      $user_id=$this->post('user_id');
      $location_id=$this->post('location_id');
      $request_detail_id=$this->post('request_detail_id');
      $status='Assigned';
      $username=$this->post('username');
      // $assign_qty=$this->post('assign_qty');
      // $first_name=$this->post('first_name');
      // $last_name=$this->post('last_name');
      // $region=$this->post('region');
      // $location=$this->post('location');
      $assigned_at=date('d-m-Y H:i A',time());

      $InsertRequest=array('asset_id'=>$asset_id,'request_id'=>$request_id,'user_id'=>$user_id,'assigned_at'=>$assigned_at,'assign_status'=>$status,'user_location'=>$location_id);

      $request_res=$this->Model->insert_to('assign_details',$InsertRequest);

      if($request_res){
        $where=array('request_detail_id'=>$request_detail_id);
        $data=array('request_detail_status'=>'Assigned');
        $update_res=$this->Model->update_where('request_details',$where,$data);

        $UpdateData=array('status'=>'Dispatched');
        $Updatewhere=array('asset_id'=>$asset_id);
        $res=$this->Model->update_where('assets',$Updatewhere,$UpdateData);

        $au_name = $username;
       $current_date_time=date('d-m-Y g:i a.',time());
       $description='Asset allocated to user Sean';
       $history_status='Allocated';
       $InsertHistoryData=array('asset_id'=>$asset_id,'request_id'=>$request_id,'description'=>$description,' history_status'=>$history_status,'datetime'=>$current_date_time,'user'=>$au_name);

       $history_res=$this->Model->insert_to('assettranshistory',$InsertHistoryData);

        $description='Asset dispatched to user';
       $history_status='Dispatched';
       $InsertHistoryData1=array('asset_id'=>$asset_id,'request_id'=>$request_id,'description'=>$description,' history_status'=>$history_status,'datetime'=>$current_date_time,'user'=>$au_name);

       $history_res=$this->Model->insert_to('assettranshistory',$InsertHistoryData1);

      }


      $response=array();
        if($request_res){
            
               $response['status']='Success';
               $response['request_id']=$request_res;
               $response['result']='Asset Assigned Successfully';
            }
            else{
            $response['status']='Failed';
            $response['result']='Asset Assign Failed';
        }
      
      $this->set_response($response,200);

    }




  public function request_list_get()

    {

        $where=array('request_status!='=>'Received');
        $request_list = $this->Model->get_from_where('request',$where);

     // $request_list=$this->Model->get_all('request');

      $response=array();
        if($request_list){

               $response['status']='Success';
               $response['request_list']=$request_list;
               $this->set_response($response,200);

        }else{
               $response['status']='Failed';
               $response['request_list']=[];
               $this->set_response($response,200);
        }


    }




    public function request_details_get()

    {
          $request_id=$this->get('request_id');

          $where=array('request_id'=>$request_id);
          $get_one=$this->Model->get_one('request',$where);

          $status=$get_one['request_status'];
          $response=array();

          if($status!='Requested'){

             $query='select * from request_details as rd inner join assets as pr on rd.asset_id=pr.asset_id inner join brand as br on pr.brand_id=br.brand_id inner join asset_type as at on pr.asset_type_id = at.asset_type_id where rd.request_id="'.$request_id.'"' ;
           $request_details = $this->Model->run_queryarray($query);

           
           if($request_details){

               $response['status']='Success';
               $response['request_id']=$request_id;
               $response['order_details']=$request_details;
               $response['request']=$get_one;
               $this->set_response($response,200);


           }

          }else{

            $response['status']='Success';
            $response['request_id']=$request_id;
            $response['request']=$get_one;
            $this->set_response($response,200);

          }

          }

    

     public function dashboard_get()
            {

         // $query1='select count(request_id) as total from request' ;
         // $total = $this->Model->run_queryarray($query1);
         // foreach($total as $req){
         //  $response['request']=$req->total;
         // }

         // $query2='select count(request_id) as total from request where request_status="Dispatch"' ;
         // $order = $this->Model->run_queryarray($query2);
         // foreach($order as $ord){
         //  $response['orders']=$ord->total;
         // }

         // $query3='select count(request_id) as total from request where request_status="Received"' ;
         // $received = $this->Model->run_queryarray($query3);
         // foreach($received as $rec){
         //  $response['received']=$rec->total;
         // }

        $response=array();

        $query='select * from assign_details as ad inner join assets as pr on ad.asset_id=pr.asset_id inner join brand as br on pr.brand_id=br.brand_id inner join asset_type as at on pr.asset_type_id = at.asset_type_id inner join users as usr on ad.user_id = usr.user_id inner join locations as lo on lo.location_id= usr.location_id where ad.assign_status="Assigned" order by assign_id DESC limit 10' ;
        $request_details = $this->Model->run_queryarray($query);

        $response['notification']=$request_details;
         
         $this->set_response($response,200);
                    
            }
            


     public function assign_list_get()
            {

          $response=array();

          $query='select * from assign_details as ad inner join assets as pr on ad.asset_id=pr.asset_id inner join brand as br on pr.brand_id=br.brand_id inner join asset_type as at on pr.asset_type_id = at.asset_type_id inner join users as usr on ad.user_id = usr.user_id inner join locations as lo on lo.location_id= usr.location_id where ad.assign_status!="Completed" order by ad.assign_id DESC' ;
           $assign_details= $this->Model->run_queryarray($query);

          $response['assign_details']=$assign_details;
          $this->set_response($response,200);

        }



  public function confirm_assign_post()

    {

      $path='assets/admin/images/sign';
      $result1=FUNC::uploadImg($path,'Image');
      $image_name=$result1['file_name']; 
      $mainUrl = $this->config->item('mainUrl');
      $url=$mainUrl.'assets/admin/images/sign/'.$image_name;
      $username=$this->post('username');

     
      $status='Completed';
      $assign_id=$this->post('assign_id');
      $current_date_time=date('d-m-Y g:i a.',time());

     // $confirm=array('product_id'=>$product_id,'quantity'=>$quantity,'status'=>$status);
     // $confirm_res=$this->Model->insert_to('asset_type',$confirm);
       $UpdateData=array('assign_status'=>$status,'confirm_sign'=>$url,'confirmed_at'=>$current_date_time);
            $UpdateWhere=array('assign_id'=> $assign_id);
            $update=$this->Model->update_where('assign_details',$UpdateWhere,$UpdateData);
            
            if($update){
               $query='select * from assign_details where assign_id="'.$assign_id.'"' ;
               $assign_details = $this->Model->run_queryarray($query);
                foreach($assign_details as $details){
                   $id=$details->asset_id;
                   $request_id=$details->request_id;
                   $UpdateData=array('status'=>'Deployed','remark'=>'Deployed');
                   $where=array('asset_id'=>$id);
                   $res=$this->Model->update_where('assets',$where,$UpdateData);

                   $au_name = $username;
                   $current_date_time=date('d-m-Y g:i a.',time());
                   $description='Signed and accepted by user';
                   $history_status='Accepted';
                   $InsertHistoryData=array('asset_id'=>$id,'request_id'=>$request_id,'description'=>$description,' history_status'=>$history_status,'datetime'=>$current_date_time,'user'=>$au_name,'sign'=>$url);

                   $history_res=$this->Model->insert_to('assettranshistory',$InsertHistoryData);

                    $description='Asset flagged as deployed';
                   $history_status='Deployed';
                   $InsertHistoryData1=array('asset_id'=>$id,'request_id'=>$request_id,'description'=>$description,' history_status'=>$history_status,'datetime'=>$current_date_time,'user'=>$au_name);

                   $history_res=$this->Model->insert_to('assettranshistory',$InsertHistoryData1);

              
            }
          }


      $response=array();
        if($update){
            $response['status']='Success';
            $response['res']=$result1;
            $response['result']='Assign Confirmed Successfully';
           
        }else{
            $response['status']='Failed';
            $response['result']='Assign Confirmation Failed';
            
        }


          $this->set_response($response,200);

    }


 public function user_list_get()

    {
       $location=$this->get('location_id');
       $where1 =array('users.location_id'=>$location);
       $condition='users.location_id = locations.location_id';
       $users = $this->Model->join_two('users','locations',$condition,$where1);  
       $response['users']=$users;
       $this->set_response($response,200);

    }


    

  public function asset_list_get()
    {   
         $type=$this->get('type');
         $brand=$this->get('brand');

        //$type=1;
        //$brand=1;

         if(!empty($type)&&!empty($brand)){
           $where=array('asset_type_id'=>$type,'brand_id'=>$brand,'status!='=>'Booked');

         }else if(!empty($type)){
            $where=array('asset_type_id'=>$type,'status!='=>'Booked');

         }else if(!empty($brand)){
            $where=array('brand_id'=>$brand,'status!='=>'Booked');
         }else{
            $where=array('status!='=>'Booked');
         }

        $data = $this->Model->get_from_where('assets',$where);
        // $output = array(
        //     "draw" => '22',
        //     "recordsTotal" => count($data),
        //     "data" => $data,
        // );
        

        $response['assets']=$data;
        $this->set_response($response,200);
          
        //echo json_encode($output); 
    }


       public function dispatch_post()
    {

      $select=$this->post('select');
      $request_id=$this->post('request');
      foreach($select as $val){
        echo $val;
        $status='Dispatch';
        $InsertData=array('request_id'=>$request_id,'asset_id'=>$val,'request_detail_status'=>$status);
        $res=$this->Model->insert_to('request_details',$InsertData);

         $pro_where=array('asset_id'=>$val);
         $pro_UpdateData=array('status'=>'Booked');
         $result=$this->Model->update_where('product',$pro_where,$pro_UpdateData);
      }

      $where=array('request_id'=>$request_id);
      $UpdateData=array('request_status'=>$status);
      $res=$this->Model->update_where('request',$where,$UpdateData);
      
      if($res){
       $response['staus']='success';
       }else{
       $response['users']='Failed';
       }
       $this->set_response($response,200);

    }


 public function dispach_list_get()

    {
        $response=array();
        $query='select * from request_details as rd inner join assets as pr on rd.asset_id=pr.asset_id inner join brand as br on pr.brand_id=br.brand_id inner join asset_type as at on pr.asset_type_id = at.asset_type_id inner join request as r on r.request_id = rd.request_id inner join locations as lo on lo.location_id= r.request_location where r.request_status="Received" and rd.request_detail_status="Dispatched"' ;
           $request_details = $this->Model->run_queryarray($query);


       $response['dispach_list']=$request_details;
       $this->set_response($response,200);

    }

     public function dispatched_request_list_get(){

           $response=array();
           $query='select * from request as r inner join locations as lo on lo.location_id= r.request_location where request_status="Dispatched" || request_status="Requested" order by request_date_time' ;
           $dispatched_request_list = $this->Model->run_queryarray($query);

           foreach($dispatched_request_list as $list){
            if($list->request_status=='Requested'){
               $list->type=1;
            }else{
               $list->type=2;
            }
            $response['request_list'][]=$list;
           }

      
            $this->set_response($response,200);
    }
   

     public function locations_list_get(){

       $locations = $this->Model->get_all('locations');

        $response['locations']=$locations;

        $this->set_response($response,200);

     }

      public function all_users_get(){

       $users = $this->Model->get_all('users');

        $response['users']=$users;

        $this->set_response($response,200);

     }


   



}