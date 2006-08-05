<?php

function Pagination_Article2_insert_head($flux){

$flux .=

'
<script src=\''.url_absolue(find_in_path('pagination_article2.js')).'\' type=\'text/javascript\'></script>
';

	return $flux;
}

?>