<?php

function Auteur_forum_affichage_final($flux){
	if(_request('page')=='forum') {
		$form = $GLOBALS['spip_version_code']<1.92?"$('.previsu').parent()":"$('.previsu').parent().parent()";
		#	include_spip('inc/charsets');
		// filtrer et remettre le tout dans le charset cible
		$nom = unicode2charset(html2unicode(_T('cout:nom_forum')));
		$nom = '"' . str_replace('"', '\"', $nom) . '"';
		// code jQuery
		$code =<<<jscode
<script type="text/javascript"><!--
$(document).ready(function(){
 form = $form;
 auteur = $('#auteur');
// label = auteur.prev();
 if(form.length && auteur.length)
 	// eviter les forums anonymes
	form.bind('submit', function(event){
		if(auteur.val().length==0) {
			alert($nom);
			auteur.focus();
			auteur.attr('style','border-color:#E86519;');
//			label.attr('style','color:#E86519;');
//			label.color("#E86519");
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