<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Viewer {
	var $template;
	var $header = 'inc/header';
	var $headerdata = array();
	var $footer = 'inc/footer';
	var $footerdata = array();
	var $javascript_up = array();
	var $javascript_down = array();
	var $css = array();
	var $views = array();
	
	var $logged_in = false;
	var $current_controller = 'login';
	var $current_method = 'index';
	private $logtype = 'admin';
	
	function __construct(){
		
		$this->CI =& get_instance();
		$this->template = $this->CI->config->item('active_template');
		$this->setJavascript($this->CI->config->item('default_javascript_up'),'up');
		$this->setJavascript($this->CI->config->item('default_javascript_down'),'down');
		$this->setCSS($this->CI->config->item('default_css'));
		
		$this->current_controller = ($this->CI->uri->segment(1)) ? $this->CI->uri->segment(1) : 'login';
		$this->current_method = ($this->CI->uri->segment(1)) ? $this->CI->uri->segment(2) : 'index';
	}
	
	
	
	function setJavascript($js = '',$part){
		if($js == '') return;
		
		if(is_array($js) && count($js) > 0){
			foreach($js as $val){
				if($part == 'up'){
					$this->javascript_up[] = $val;
				}else{
					$this->javascript_down[] = $val;
				}
			}
		}
		else{
			if($part == 'up'){
				$this->javascript_up[] = $js;
			}else{
				$this->javascript_down[] = $js;
			}
		}
	}
	
	function setCSS($css = ''){
		if($css == '') return;
		
		if(is_array($css) && count($css) > 0){
			foreach($css as $val){
				$this->css[] = $val;
			}
		}
		else{
			$this->css[] = $css;
		}
		
	}
	
	function renderIEPatch($file){
		$js_string = '';
		if(is_array($file) and !empty($file)){
			foreach($file as $f){
				if(file_exists(FCPATH."assets/js/$f.js")){
					$js_string .= "<script type='text/javascript' src='".$this->mainUrl()."assets/js/$f.js'></script>";
				}
			}
		}
		elseif(!is_array($file)){
			if(file_exists(FCPATH."assets/js/$file.js")){
				$js_string .= "<script type='text/javascript' src='".$this->mainUrl()."assets/js/$file.js'></script>";
			}
		}
		return $js_string;
	}
	
	function renderJavascript($part){
		$js_string = '';
		if($part == 'up'){
			if(count($this->javascript_up) > 0){
				foreach($this->javascript_up as $js){
					if(file_exists(FCPATH."assets/".$this->logtype."/js/$js.js")){
						$js_string .= "<script type='text/javascript' src='".$this->mainUrl()."assets/".$this->logtype."/js/$js.js'></script>";
					}
				}
			}
		}else{
			if(count($this->javascript_down) > 0){
				foreach($this->javascript_down as $js){
					if(file_exists(FCPATH."assets/".$this->logtype."/js/$js.js")){
						$js_string .= "<script type='text/javascript' src='".$this->mainUrl()."assets/".$this->logtype."/js/$js.js'></script>";
					}
				}
			}
		}
		
		return $js_string;
	}
	
	function renderCSS(){
		$css_string = '';
		
		if(count($this->css) > 0){
			foreach($this->css as $css){
				
				if(file_exists(FCPATH."assets/".$this->logtype."/css/$css.css")){
					$css_string .= "<link rel='stylesheet' type='text/css' href='".$this->mainUrl()."assets/".$this->logtype."/css/$css.css'/>";
				}
			}
		}
		
		return $css_string;
	}
	
	function setHeader($header,$data = array()){
		$this->header = $header;
		if(is_array($data) and !empty($data)){
			$this->headerdata = array_merge($this->headerdata, $data);
		}
	}
	
	function setFooter($footer,$footerdata = array()){
		$this->footer = $footer;
		if(is_array($footerdata) and !empty($footerdata)){
			$this->footerdata = array_merge($this->footerdata,$footerdata);
		}
	}
	
	function setheaderData($data){
		$this->headerdata = array_merge($this->headerdata, $data);
	}
	
	function setFooterData($data){
		$this->footerdata = array_merge($this->footerdata,$data);
	}
	
	function setView($views = ''){
		if($views == '' ) return ;
		
		$this->views[] = $views;
	}
	
	function renderViews(){
		if($this->header != ''){
			$this->CI->load->view($this->template."/".$this->header,$this->headerdata);
		}
		foreach ($this->views as $view){
			if(is_array($view)){
				$this->CI->load->view($this->template."/".key($view),$view[key($view)]);
			}
			else{
				$this->CI->load->view($this->template."/".$view);
			}
		}
		if($this->footer != ''){
			$this->CI->load->view($this->template."/".$this->footer,$this->footerdata);
		}
	}
	
	function load_view($view,$params = array()){
		$this->CI->load->view($this->template."/$view",$params);
	}
	function mainUrl(){
		$this->CI =& get_instance();
		return $this->CI->config->item('mainUrl');
	}
	function image_path(){
		$this->CI =& get_instance();
		return $this->CI->config->item('image_path');
	}
}

/* End of file viewer.php */
/* Location: ./admin/libraries/viewer.php */