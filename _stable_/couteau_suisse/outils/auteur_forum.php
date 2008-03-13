<?php

function Auteur_forum_affichage_final($flux){
	if(_request('page')=='forum') {
		$form = defined('_SPIP19100')?"jQuery('.previsu').parent()":"jQuery('.previsu').parent().parent()";
		$auteur = defined('_SPIP19300')?'session_nom':'auteur';
		#	include_spip('inc/charsets');
		// filtrer et remettre le tout dans le charset cible
		$nom = unicode2charset(html2unicode(_T('cout:nom_forum')));
		$nom = '"' . str_replace('"', '\"', $nom) . '"';
		// code jQuery
		$code =<<<jscode
<script type="text/javascript"><!--
if (window.jQuery) jQuery(document).ready(function(){
 form = $form;
 // SPIP 1.93 remplace 'auteur' par 'session_nom'
 auteur = jQuery('#session_nom');
 if(!auteur.length) auteur = jQuery('#auteur');
 if(form.length && auteur.length)
 	// eviter les forums anonymes
	form.bind('submit', function(event){
		if(auteur.val().length==0) {
			alert($nom);
			auteur.focus();
			auteur.attr('style','border-color:#E86519;');
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