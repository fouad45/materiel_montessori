<?php
header('Access-Control-Allow-Origin: *');
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
include(dirname(__FILE__).'/fieldstaticfooter.php');

 $field = new fieldstaticfooter();
 $name_module = $_POST['module_id'];
  $hookArrays = array();
 if($name_module=='Chose Module'){
	$hookArrays='';
 }else{
 $module = Module::getInstanceByName($name_module);
 $id_module = $module->id;
 $hooks = $field->getHooksByModuleId($id_module);
 foreach($hooks as $key => $hook) {
	$hookArrays[$key] = array('id_hook'=>$hook['name'], 'name' => $hook['name']);
 }
 }
 $json = json_encode($hookArrays); 
die(json_encode($json));

?>
