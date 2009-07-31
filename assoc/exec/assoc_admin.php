<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_assoc_admin(){
	
	 pipeline('fast_plug',array('args'=>array('exec'=>'assoc_admin','type'=>'simple'),'data'=>''));
 
}
?>