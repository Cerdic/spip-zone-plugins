<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function googleajaxsearch_insert_head($flux){
   
    // https://developers.google.com/custom-search/docs/element
    $flux.='<!-- google search -->
    <script src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
		function getParameterByName(name) {
			name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
			var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
				results = regex.exec(location.search);
			return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
		}
        google.load(\'search\', \'1\');
        google.setOnLoadCallback(function(){
          var searchControl = new google.search.CustomSearchControl();
		  searchControl.draw(\'searchcontrol\');
 		  searchControl.execute(getParameterByName(\'recherche\'));
       }, true);
    </script>';
	
	return $flux;
}
	
?>