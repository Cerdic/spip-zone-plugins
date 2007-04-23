<?php

function Auteur_forum_affichage_final($flux){
	if(_request('page')=='forum') {
		#	include_spip('inc/charsets');
		// filtrer et remettre le tout dans le charset cible
		$nom = unicode2charset(html2unicode(_T('tweak:nom_forum')));
		$nom = '"' . str_replace('"', '\"', $nom) . '"';
		$code =<<<jscode
<script type="text/javascript"><!--
$(document).ready(function(){
 if($('#formulaire_forum @confirmer_forum').length)
 	// eviter les forums anonymes
	$('#formulaire_forum').bind('submit', function(event){
		 t = $('#formulaire_forum #auteur');
		if(t.val().length==0) {
			t.attr('style','border-color:#E86519;');
			t.focus();
			alert($nom);
			return false;
		}
	});
});
//--></script>
jscode;
		$flux = str_replace("</head>","$code\n</head>",$flux);
	}
	return $flux;
}

?>