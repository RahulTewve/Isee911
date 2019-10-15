<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed'); 
require_once APPPATH."/vendor/opentok/opentok";
echo APPPATH;
exit;
class Opentok extends PHPExcel {
 public function __construct() {
 parent::__construct();
 }
}