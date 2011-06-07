<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');


function exec_contact_un_message_dist()
{
	exec_contact_un_message_args(intval(_request('id_message')));
}

// Marque le message comme vu et demande à afficher le message dont l'id_message est passé en paramètre
function exec_contact_un_message_args($id_message)
{
	pipeline('exec_init',array('args'=>array('exec'=>'contact_un_message','id_message'=>$id_message),'data'=>''));

	$row = sql_fetsel("*", "spip_messages", "id_message=$id_message AND type='contac'");

	if (!$row
	OR !autoriser('voir', 'message', $id_message)) {
		include_spip('inc/minipres');
		echo minipres(_T('contact:aucun_message'));
	} else {
		marquer_message_vu($id_message, $GLOBALS['visiteur_session']['id_auteur']);
		$row['titre'] = sinon($row["titre"]);

		$res = debut_gauche('accueil',true)
		  .  message_affiche($id_message, $row)
		  . "<br /><br /><div class='centered'>"
		. "</div>"
		. fin_gauche();

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page("&laquo; ". $row['titre'] ." &raquo;", "accueil", "messagerie");

		echo debut_grand_cadre(true),
			fin_grand_cadre(true),
			$res,
			fin_page();
	}
}



// Affichage de la page + message
function message_affiche($id_message, $row)
{
	$id_message = $row['id_message'];
	$date_heure = $row["date_heure"];
	$titre = typo($row["titre"]);
	$id_auteur = $row['id_auteur'];

	$iconifier = charger_fonction('iconifier', 'inc');
	$icone = $iconifier('id_message', $id_message,'messages', false, $flag_editable);

	$boite = pipeline ('boite_infos', array('data' => '',
		'args' => array(
			'type'=>'message_contact',
			'id' => $id_message,
			'row' => $row
		)
	));

	$actions = icone_inline(_T('contact:msg_supprimer_message'), redirige_action_auteur("supprimer_message", "$id_message", 'contact_messages'), find_in_path("images/contact-24.png"), "supprimer.gif",$GLOBALS['spip_lang_right']);

	$haut =
		"<div class='bandeau_actions'>$actions</div>".
		(_INTERFACE_ONGLETS?"":"<span $dir_lang class='arial1 spip_medium'><b>" . _T('contact:msg_contact') . "</b></span>\n")
		. gros_titre($titre, '' , false)
		. (_INTERFACE_ONGLETS?"":"<span $dir_lang class='arial1 spip_medium'><b>" . nom_jour($date_heure) ." ".affdate_heure($date_heure). "</b></span><br /><br />\n");

	$navigation =
	  debut_boite_info(true). $boite . fin_boite_info(true)
	  . $icone
	  . pipeline('affiche_gauche',array('args'=>array('exec'=>'contact_un_message','id_message'=>$id_message),'data'=>''));

	$extra = creer_colonne_droite('', true)
	  . pipeline('affiche_droite',array('args'=>array('exec'=>'contact_un_message','id_message'=>$id_message),'data'=>''))
	  . debut_droite('',true);

	$onglet_contenu =
	  http_affiche_un_message($id_message);

	$onglet_documents = afficher_pieces_jointes('message', intval($id_message));

  	return
	  $navigation
	  . $extra
	  . pipeline('afficher_fiche_objet',array('args'=>array('type'=>'message','id'=>$id_message),'data'=>
	   "<div class='fiche_objet'>"
	  . $haut
	  . afficher_onglets_pages(
	  	array(
	  	'voir' => _T('onglet_contenu'),
	  	'docs' => _T('onglet_documents')),
	  	array(
	    'voir'=>$onglet_contenu,
	    'docs'=>$onglet_documents))
	  . "</div>"
			));
}

/**
 * Marque le message comme vu
 *
 * @param int $id_message
 * @param int $id_auteur
 */
function marquer_message_vu($id_message, $id_auteur) {

	sql_updateq("spip_auteurs_messages", array("vu" => 'oui'), "id_message=$id_message AND id_auteur=$id_auteur");
	/**
	 * Si le message est affiché dans l'espace public, on invalide le cache
	 */
	include_spip('inc/invalideur');
	suivre_invalideur("message/".$id_message);
}

// Affiche le message
function http_affiche_un_message($id_message)
{
	$type = 'message_contact';
	$contexte = array(
		'id'=>$id_message,
	);

	$fond = recuperer_fond("prive/contenu/$type",$contexte);

	//permettre aux plugins de faire des modifs ou des ajouts
	$fond = pipeline('afficher_contenu_objet',
		array(
		'args'=>array(
			'type'=>$type,
			'id_objet'=>$id_message,
			'contexte'=>$contexte),
		'data'=> ($fond)));

	$res .= "<div id='wysiwyg'>$fond</div>";

	return $res;
}

// Fonction d'affichage des pièces jointes
function afficher_pieces_jointes($type, $id) {
	$documenter = charger_fonction('documenter', 'inc');

	return "<div id='portfolio'>" . $documenter($id, $type, 'portfolio') . "</div><br />"
	. "<div id='documents'>" . $documenter($id, $type, 'documents') . "</div>";
}
?>
