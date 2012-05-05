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
		'onShiftEnter'      => array('keepDefault'=>false, 'replaceWith'=>"\n_ "),
		'onCtrlEnter'       => array('keepDefault'=>false, 'replaceWith'=>"\n\n"),
		// garder les listes si on appuie sur entree
		'onEnter'           => array('keepDefault'=>false, 'selectionType'=>'return', 'replaceWith'=>"\n"),
		'markupSet'         => array(
			// Inserer un commentaire
			array(
				"id"        => 'insercom',
				"name"      => 'essai',
				"className" => "outil_insercom",
				"replaceWith" => "function(h){ return essai(h);}",
//				"openWith"	=> "[[[",
//				"closeWith" => "]]]",
				"display"   => true,
				"selectionType" => "")
			),
		'functions'			=> "
			// essai
			function essai(h) {
				s = h.selection;
				alert(h.textarea.selectionStart);
				alert(h.textarea.selectionEnd);
				console.log(h);
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
		'outil_insercom' => array('spt-v1.png','-10px -226px'), //'intertitre.png'
	);
}
?>
