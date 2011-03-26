<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// On déclare le formulaire pour éditer un formulaire
$GLOBALS['formulaires']['editer_formulaire'] = array(
	array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'titre',
			'label' => '<:formidable:editer_titre:>',
			'obligatoire' => 'oui',
			'class' => 'multilang'
		)
	),
	array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'identifiant',
			'label' => '<:formidable:editer_identifiant:>',
			'explication' => '<:formidable:editer_identifiant_explication:>',
			'obligatoire' => 'oui'
		),
		'verifier' => array(
			'type' => 'regex',
			'options' => array(
				'modele' => '/^[\w]+$/'
			)
		)
	),
	array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'message_retour',
			'label' => '<:formidable:editer_message_ok:>',
			'explication' => '<:formidable:editer_message_ok_explication:>',
			'rows' => 5,
			'li_class' => 'editer_texte',
			'class' => 'multilang'
		)
	),
	array(
		'saisie' => 'textarea',
		'options' => array(
			'nom' => 'descriptif',
			'label' => '<:formidable:editer_descriptif:>',
			'explication' => '<:formidable:editer_descriptif_explication:>',
			'rows' => 5,
			'class' => 'multilang'
		)
	),
	array(
		'saisie' => 'selection',
		'options' => array(
			'nom' => 'apres',
			'label' => 'Afficher ensuite',
			'explication' => 'Apres validation, afficher à la place du formulaire :',
			'datas' => 
"formulaire|Le formulaire, à nouveau
valeurs|Les valeurs saisies
rien|Rien du tout",
			'defaut' => 'formulaire',
			'cacher_option_intro' => 'on'
		)
	)
);

function puce_statut_formulaires_reponse_dist($id, $statut, $id_rubrique, $type='formulaires_reponse', $ajax=false){
	global $lang_objet;
	
	static $coord = array(
		'publie' => 1,
		'prop' => 0,
		'poubelle' => 2
	);

	$lang_dir = lang_dir($lang_objet);
	$ajax_node = " id='imgstatut$type$id'";
	$inser_puce = puce_statut($statut, " width='9' height='9' style='margin: 1px;'$ajax_node");

	if (!autoriser('instituer', 'formulaires_reponse', $id)
		or !_ACTIVER_PUCE_RAPIDE
	)
		return $inser_puce;

	$titles = array(
		"orange" => _T('texte_statut_propose_evaluation'),
		"verte" => _T('texte_statut_publie'),
		"poubelle" => _T('texte_statut_poubelle')
	);
	
	$clip = 1 + (11 * $coord[$statut]);

	if ($ajax){
		return 	"<span class='puce_article_fixe'>"
		. $inser_puce
		. "</span>"
		. "<span class='puce_article_popup' id='statutdecal$type$id' style='width:33px; margin-left: -$clip"."px;'>"
		  . afficher_script_statut($id, $type, -1, 'puce-orange.gif', 'prop', $titles['orange'])
		  . afficher_script_statut($id, $type, -12, 'puce-verte.gif', 'publie', $titles['verte'])
		  . afficher_script_statut($id, $type, -23, 'puce-poubelle.gif', 'poubelle', $titles['poubelle'])
		  . "</span>";
	}

	$nom = "puce_statut_";

	if ((! _SPIP_AJAX) AND $type != 'formulaires_reponse') 
	  $over ='';
	else {
		$action = generer_url_ecrire('puce_statut_formulaires',"",true);
		$action = "if (!this.puce_loaded) { this.puce_loaded = true; prepare_selec_statut('$nom', '$type', '$id', '$action'); }";
		$over = "\nonmouseover=\"$action\"";
	}

	return 	"<span class='puce_article' id='$nom$type$id' dir='$lang_dir'$over>"
	. $inser_puce
	. '</span>';
}

if (!function_exists('array_fill_keys')){
	function array_fill_keys($keys, $value){
		array_combine($keys,array_fill(0,count($keys),$value));
	}
}

?>
