<?php
// --------------------------------
// partie publique
// --------------------------------
function googleajaxsearch_insert_head($flux){

    $langue_site = $GLOBALS['meta']['langue_site'];
    $nom_site = $GLOBALS['meta']['nom_site'];
    $adresse_site = $GLOBALS['meta']['adresse_site'];
        
		
		$flux.='
    <!-- google ajax api search -->
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
    google.load("search", "1", {"language" : "'.$langue_site.'"});
    function OnLoadGoogle() {
      var recherche = $(\'#searchcontrol span\').text();
      var searchControl = new google.search.SearchControl();      
      var siteSearch = new google.search.WebSearch();
      siteSearch.setUserDefinedLabel("'.supprimer_tags($nom_site).'");
      siteSearch.setUserDefinedClassSuffix("siteSearch");
      siteSearch.setSiteRestriction("'.$adresse_site.'");
	  searchControl.setResultSetSize(google.search.Search.LARGE_RESULTSET)
      options = new google.search.SearcherOptions();
      options.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
      searchControl.addSearcher(siteSearch,options);
      searchControl.draw(document.getElementById("searchcontrol"));
      searchControl.execute(recherche);
    }     
    google.setOnLoadCallback(OnLoadGoogle);
    </script>';
	
	return $flux;
}
	
?>