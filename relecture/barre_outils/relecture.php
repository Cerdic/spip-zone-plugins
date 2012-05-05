<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Definition de la barre d'outil utilie pour deposer des commentaires de relecture.
 *
 * @return object
 */
function barre_outils_relecture(){
	$set = new Barre_outils(array(
		'nameSpace'         => 'relecture',
		'markupSet'         => array(
			// Inserer un commentaire
			array(
				"id"        => 'insercom',
				"name"      => _T('relecture:bouton_inserer_commentaire'),
				"className" => "outil_inserer",
				"replaceWith" => "function(h){ return lancer_insertion(h);}",
				"display"   => true,
				"selectionType" => "")
			),
		'functions'			=> "
			// Lancement de l'insertion d'un commentaire a l'emplacement designe
			function lancer_insertion(h) {
				// Reserver la selection pour le reinjecter ensuite
				s = h.selection;
				// Recuperer les offsets de debut et fin de la selection
				d = h.textarea.selectionStart;
				f = h.textarea.selectionEnd;
				// Creer l'url de la page d'edition du commentaire
				u = parametre_url('?exec=commentaire_edit', 'id_relecture', 29);
				u = parametre_url(u, 'debut', d);
				u = parametre_url(u, 'fin', f);
				// Appel de la modalbox pour saisir le commentaire
				// Le retour se fait sur la page en cours
				jQuery.modalboxload(u, {onClose: function (dialog) {jQuery('#').ajaxReload();}});
				return s;
			}
			",
	));
	
	$set->cacher(array(
		'stroke_through',
		'clean', 'preview',
	));
	
	return $set;
}


/**
 * Definitions des liens entre css et icones
 *
 * @return object
 */
function barre_outils_relecture_icones(){
	return array(
		'outil_inserer' => array('inserer_commentaire-16.png',''),
	);
}

?>
