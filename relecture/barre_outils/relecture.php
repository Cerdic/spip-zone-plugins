<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Definition de la barre d'outil utilisee pour ajouter des commentaires de relecture.
 *
 * @return object L'objet barre_outils configure
 */
function barre_outils_relecture(){
	$set = new Barre_outils(array(
		'nameSpace'         => 'relecture',
		'markupSet'         => array(
			// Bouton Inserer un commentaire
			array(
				"id"        => 'insercom',
				"name"      => _T('relecture:bouton_ajouter_commentaire'),
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
				if (!d) { d = '0'; }
				f = h.textarea.selectionEnd;
				if (!f) { f = '0'; }

				// Creer l'url de la page d'edition du commentaire
				p = get_parametre_url();
				if (!p['element']) {
					p['element'] = 'texte';
				}
				u = parametre_url('?exec=commentaire_edit', 'new', 'oui');
				u = parametre_url(u, 'id_relecture', p['id_relecture']);
				u = parametre_url(u, 'element', p['element']);
				u = parametre_url(u, 'index_debut', d);
				u = parametre_url(u, 'index_fin', f);
				u = parametre_url(u, 'var_zajax', 'contenu');

				// Appel de la modalbox pour saisir le commentaire
				// Le retour se fait sur la page en cours
				jQuery.modalboxload(u, {onClose: function (dialog) {jQuery('#').ajaxReload();}});
				return s;
			}

			function get_parametre_url() {
			    var vars = [], hash;
			    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			    for (var i = 0; i < hashes.length; i++) {
			        hash = hashes[i].split('=');
			        vars.push(hash[0]);
			        vars[hash[0]] = hash[1];
			    }
			    return vars;
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
 * @return array
 */
function barre_outils_relecture_icones(){
	return array(
		'outil_inserer' => array('inserer_commentaire-16.png',''),
	);
}

?>
