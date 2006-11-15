<?php



function sktheme_insert_head($flux){
  
  if ($GLOBALS['meta']['sktheme_switcher_activated']) {

    $flux .='
	<style type="text/css" media="print">
/* <![CDATA[ */
	#sktheme_switcher { display: none; }
/* ]]> */
	</style>
';

    return $flux;
  
  }  
}


?>
