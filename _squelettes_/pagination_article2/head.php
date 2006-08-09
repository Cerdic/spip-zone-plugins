<?php

function Pagination_Article2_insert_head($flux){
global $debut_intertitre;

if(preg_match('!<(\w+)(?:[^>]+?class=[\'"]([^\'"]+)[\'"])?[^>]*>!',$debut_intertitre,$m)) {
	$selector = $m[1].($m[2]?'.'.$m[2]:'');
	//Do not use pagination2 if the headings are not block elements 
	//because SPIP wrap them with <p class="spip"> and the jquery script fails
	if(!preg_match('!_BALISES_BLOCS!i',$m[1])) return $flux;	
	$flux .="<script type='text/javascript'>var pagination_article2_HEADING='$selector'</script>\n";
}

$flux .=

'
<script src=\''.url_absolue(find_in_path('pagination_article2.js')).'\' type=\'text/javascript\'></script>
';

	return $flux;
}

?>
