<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function googleajaxsearch_insert_head($flux){
   
    // https://developers.google.com/custom-search/docs/element
    $flux.='<!-- google search -->
    <script src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
        google.load(\'search\', \'1\');
        google.setOnLoadCallback(function(){
          new google.search.CustomSearchControl().draw(\'searchcontrol\');
        }, true);
    </script>';
        
	
	return $flux;
}
	
?>