<?php
 
class Model extends CI_Model {
  
  	function __construct() 
 	 {
    	/* Call the Model constructor */
   	 	parent::__construct();
 	 }
  	function get_all($table)
  	{   //$this->db->order_by('detail_id', "desc")->limit(;
	  	$query = $this->db->get($table);
	  	return $query->result_array();
  	}
	function get_all_from_where($table,$data)
  	{
	  	$query = $this->db->get_where($table, $data);
	  	return $query->result_array();
  	}
	function get_from_where($table,$data)
	{
		$query = $this->db->get_where($table, $data);
		return $query->result();
	}
        function get_one($table,$data)
	{
		$query = $this->db->get_where($table, $data);
		return $query->row_array();
	}
	function update_where($table,$where,$data)
	{
		$this->db->where($where);
		$query=$this->db->update($table, $data); 
		return $query;
	}
	function delete_where($table,$data)
	{
		$query=$this->db->delete($table, $data); 
		return $query;
	}
        
	function insert_to($table, $data)
	{
		$this->db->insert($table, $data); 
		return $this->db->insert_id();
	}
	function join_two($table1,$table2,$condition,$where)
	{
		$this->db->select('*');
		$this->db->from($table1);
		$this->db->join($table2, $condition);
		$this->db->where($where); 
		$query = $this->db->get();
		return $query->result_array();
	}
	function get_distinct($table,$data)
	{
	$this->db->distinct('reg.registration_id');
	$query = $this->db->get_where($table, $data);
	return $query->result();
	}
	
	 public function where_AlltableData($table,$data,$column)
    { 
        $this->db->from($table) ;
        $this->db->where($data);
		$this->db->order_by($column, "desc");
        $query =  $this->db->get();    
    return $query->result();
    }
	 public function where_tableColumn($table,$data,$column)
    { 
	    $this->db->select($column);
        $query = $this->db->get_where($table, $data);
		return $query->row_array();    
    
    }
	
	function delete_from_where($table,$where)
	{
		$this->db->where($where);
        $query = $this->db->delete($table);
		return $query;
	}
	
 /* function get_row_count()
  	{
    	return $this->db->count_all($this->table);
  	}function get_last_item()
  {
    $this->db->order_by('id', 'DESC');
    $query = $this->db->get($this->table, 1);
    
    return $query->result();
  }
  
  
  function insert_item($item)
  {
    $this->item = $item;
    
    $this->db->insert($this->table, $this);
  }

  function remove_item($itemid)
  {
    $this->db->delete($this->table, array('id' => $itemid));
  }

  function get_row_count()
  {
    return $this->db->count_all($this->table);
  }


 
*/
    public function getid()
    { 
        $result=$this->db->select('*')->from('tbl_login');
        $query = $this->db->get();
        return $query->result();
    }
    public function replace_to($dbname,$dataarray)
    { 
        $this->db->where('regId',$dataarray['regId']);
        $q = $this->db->get($dbname);
        $this->db->reset_query();
        if ( $q->num_rows() > 0 ) 
        {
            $this->db->where('regId', $dataarray['regId']);
            $this->db->update($dbname, $dataarray);
                // $this->db->where('id', $id)->update(dbname, $dataarray);
        } else {
                //$this->db->set('id', $id);
                $this->db->insert($dbname, $dataarray);
                // $this->db->set('id', $id)->insert($dbname, $dataarray);
        }
        $id = $this->db->insert_id();
        $q = $this->db->get_where($dbname, array('regId' => $dataarray['regId']));
        return $q->result();
    }
    public function run_query($SQL)
    { 
        $query = $this->db->query($SQL);
        return $query->row_array();
    }
    public function run_Allquery($SQL)
    { 
        $query = $this->db->query($SQL);
        return $query->result();
    }
	 public function run_query_getAll($SQL)
    { 
        $query = $this->db->query($SQL);
        return $query->result_array();
    }
    public function multiple_insert($table,$data)
    { 
        $query =$this->db->insert_batch($table,$data);
        return $query;
    }
    public function replace_login($dbname,$dataarray)
    { 
        $this->db->where('mobNo',$dataarray['mobNo']);
        $q = $this->db->get($dbname);
        $this->db->reset_query();

        if ( $q->num_rows() > 0 ) 
        {
                $this->db->where('mobNo', $dataarray['mobNo']);
                $this->db->update($dbname, $dataarray);
                // $this->db->where('id', $id)->update(dbname, $dataarray);
        } else {
                //$this->db->set('id', $id);
                $this->db->insert($dbname, $dataarray);
                // $this->db->set('id', $id)->insert($dbname, $dataarray);
        }
    }
    public function run_querylike($likedata,$SQL,$loginmobileno)
    {  
            //echo $loginmobileno;
        $likedata='%'.$likedata;
        $this->db->select('*');
        $this->db->from('se_register');
        $query=$this->db->join('se_userdetails','se_register.regId=se_userdetails.regId')->where("se_register.mobNo LIKE ".$this->db->escape($likedata)." and se_register.mobNo!=".$loginmobileno, NULL, FALSE);
		//echo $query;exit;
      //$query =$this->db->query($SQL);
      //$this->db->like('mobNo', $likedata);

      //q=$this->db->escape_like_str($query1);
        //$query = $this->db->get();
      //echo $query->compile_select();
       $query->last_query();
        return $query->get()->row_array();
    }
	/* public function run_querylike1($likedata,$userId,$loginmobileno)
    {          
       $likedata='%'.$likedata;
		//return $likedata;
		$query="select * from se_userdetails u,se_register r where r.regId=u.regId and r.mobNo  LIKE ".$this->db->escape($likedata)." and r.mobNo!=".$loginmobileno;
     $querydata= $this->db->query($query);
       return  $querydata->result();
		//return $query;
    } */
	  public function run_queryarray($SQL)
    { 
        $query = $this->db->query($SQL);
        return $query->result();
    }
    public function run_query_result_array($SQL)
    { 
        $query = $this->db->query($SQL);
        return $query->result_array();
    }

    public function run_queryarray_one($SQL)
    { 
        $query = $this->db->query($SQL);
        return $query->row_array();
    }
    public function where_Alltable($table,$data)
    { 
        $this->db->from($table) ;
        //$query =  $this->db->where($data)->get();
        $this->db->where($data);
$query =  $this->db->get();    
    return $query->result();
    }
    public function where_table($table,$data)
    { 
        $this->db->from($table) ;
        //$query =  $this->db->where($data)->get();
        $this->db->where($data);
$query =  $this->db->get();
return $query->row_array();
        
        //return $query->result();
    }
	public function run_query_check($SQL)
    { 
        $query = $this->db->query($SQL);
		if($query->num_rows() > 0)
			return $query->result();
		else 
			return false;
    }
	
	  public function result_count($table,$data)
    { 
	//print_r($data);
         $this->db->from($table) ; 
        $this->db->where($data);
      $query= $this->db->get();  
//print_r($this->db->last_query());     
// exit;

       return $query->num_rows();
    }
	
}