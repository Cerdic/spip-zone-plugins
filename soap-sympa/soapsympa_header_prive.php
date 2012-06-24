<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function soapsympa_header_prive($flux) {

$exec = _request('exec');
	
if(($exec == 'auteur_infos')||($exec == 'soapsympa_review')) {

	
$script = '
<style type="text/css">

table td:hover {background-color:#ffffff; text-decoration:none;} /* background-color pour IE6*/
td.tooltip  span {display:none; padding:2px 3px; margin-left:10px; width:150px;}
td.tooltip:hover span{display:inline; position:absolute; border:1px solid #cccccc; background:#ffffff; color:#dd;}
.opacity {opacity: 0.5}
#soapsympa > div{padding: 1em;}
</style>
';



$flux .= $script;
}
return $flux;
}

?>
