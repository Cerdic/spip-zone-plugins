<?php

function switcher_insert_head($flux){

	$flux .='
	<style type="text/css" media="print">
/* <![CDATA[ */
	#plugin_switcher { display: none; }
/* ]]> */
	</style>
';

		return $flux;
	}

?>