<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
function exec_ge_sa(){
	
	 pipeline('fast_plug',array('args'=>array('exec'=>'ge_sa','type'=>'simple'),'data'=>''));
 
}
?>