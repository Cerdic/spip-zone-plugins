<?php
/*
+-------------------------------------------+
| GAFoSPIP v. 0.6 - 26/09/07 - spip 1.9.2
+-------------------------------------------+
| Gestion Alternative des Forums SPIP
+-------------------------------------------+
| Hugues AROUX - SCOTY @ koakidi.com
+-------------------------------------------+
| Fonction du Formulaire post prive
+-------------------------------------------+
*/

spipbb_log("included",3,__FILE__);

#
# La barre de typo dupiquée pour l'occas'
# Modifiee v.0.6 : bouton citer
#
function barre_forum_inspipbb($texte) {
	include_spip('inc/layer');

	if (!$GLOBALS['browser_barre'])
		return "<textarea name='texte' rows='12' class='forml' cols='40'>".$texte."</textarea>";
	static $num_formulaire = 0;
	$num_formulaire++;
	include_spip('inc/barre');
	## citer
	if (!$premiere_passe = _request('previsu')) {
		if(_request('citer')) {
			$id_citation = intval(_request('citer'));
		    $row = sql_fetsel("auteur, texte","spip_forum","id_forum=".$id_citation);
		    $aut_cite = $row['auteur']?$row['auteur']:_T('gaf:anonyme');
		    $text_cite=$row['texte'];
			# ajout de la citation
			$texte="{{ $aut_cite }}\n<quote>\n$text_cite\n</quote>\n";
		}
	}
	return afficher_barre("document.getElementById('formulaire_$num_formulaire')", true) .
	"<textarea name='texte' rows='12' class='forml' cols='40' 
	id='formulaire_$num_formulaire' 
	onselect='storeCaret(this);' 
	onclick='storeCaret(this);' 
	onkeyup='storeCaret(this);' 
	ondbclick='storeCaret(this);'>".$texte."</textarea>";
}



#
# Affiche le forumlaire de redac de post
#
function affiche_form_post()
{
global 	$previsu, $forum, $sujet, 
		$nom_site_forum, $url_site, $texte, $titre,
		$couleur_claire;

if(!$sujet) $sujet='0';

if($forum && $sujet=='0')
	{
		$row=sql_fetsel("titre","spip_articles","id_article=$forum");
		$titre_forum = $row['titre'];
		$text_intro = _T('gaf:sujet_ajout').typo($titre_forum);
	}
else
	{
		$row=sql_fetsel("titre","spip_forum","id_forum=$sujet");
		$titre_sujet = $row['titre'];
		$text_intro = _T('gaf:texte_repondre').typo($titre_sujet);
	}

	// auteur (bloque sur sessions -> pas de modif !!)
	$auteur = $GLOBALS['auteur_session']['nom'];
	$email_auteur = $GLOBALS['auteur_session']['email'];
	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];


// pour la deco !
if($sujet!='0')
	{ $ico_post=_DIR_IMG_SPIPBB."gaf_post.gif"; }
else
	{ $ico_post=_DIR_IMG_SPIPBB."gaf_sujet.gif"; }


if($previsu=='1')
	{
	// trop court ? trop long ouaih !
	if ((strlen($texte) + strlen($titre) + strlen($nom_site_forum) +
		strlen($url_site) + strlen($auteur) + strlen($email_auteur)) > 20 * 1024)
		{ $verrou_ed='oui'; $affiche_texte = "<span style='color:#DD4C5A; font-size:13px;'>"._T('forum_message_trop_long')."</span>"; }
	else if (strlen($texte) < 10 )
		{ $verrou_ed='oui'; $affiche_texte = "<span style='color:#DD4C5A; font-size:13px;'>"._T('forum_attention_dix_caracteres')."</span>"; }
	else if (strlen($titre) < 3 )
		{ $verrou_ed='oui'; $affiche_texte = "<span style='color:#DD4C5A; font-size:13px;'>"._T('forum_attention_trois_caracteres')."</span>"; }
	else { $affiche_texte = propre($texte); }
	}

	
echo "\n<form action='". generer_url_ecrire("spipbb_formpost")."' method='post' name='formulaire'";

if($previsu=='1' && !$verrou_ed)
	{
	if($sujet!='0')
		{ $retour_post = generer_url_ecrire("spipbb_sujet", "id_sujet=".$sujet); }
	else
		{ $retour_post = generer_url_ecrire("spipbb_forum", "id_article=".$forum); }

	echo "onSubmit='window.opener.location.href=\"".$retour_post."\"; return(true)'";
	}

echo ">";


if($previsu=='1')
	{
	// Une securite qui nous protege contre :
	// ... ( ... ) ... voir formulaires/inc-forumlaire_forum.php3
	//
	// Le lock est leve au moment de l'insertion en base .. function enregistre_post_gaf
	include_spip('inc/flock');
	
		$alea = preg_replace('/[^0-9]/', '', $alea);
		if(!$alea OR !@file_exists(_DIR_SESSIONS."forum_$alea.lck")) {
			while (
				# astuce : mt_rand pour autoriser les hits simultanes
				$alea = time() + @mt_rand()
				AND @file_exists($f = _DIR_SESSIONS."forum_$alea.lck")) {};
			spip_touch ($f);
		}

		# et maintenant on purge les locks de forums ouverts depuis > 4 h
		if ($dh = @opendir(_DIR_SESSIONS))
			while (($file = @readdir($dh)) !== false)
				if (preg_match('/^forum_([0-9]+)\.lck$/', $file)
				AND (time()-@filemtime(_DIR_SESSIONS.$file) > 4*3600))
					@spip_unlink(_DIR_SESSIONS.$file);

	// hash gaf
		if(!$hash)
			$hash = calculer_action_auteur("ajout_forum $forum $sujet $alea");

	// supprimer les <form> de la previsualisation
	// (sinon on ne peut pas faire <cadre>...</cadre> dans les forums) .. code dégueu plutot !
	$affiche_texte = preg_replace("@<(/?)f(orm[>[:space:]])@ism", "<\\1no-f\\2", $affiche_texte);

	// affichage du prévisu
	$avant_post="
		<span class='verdana3'><b>"._T('gaf:messages_verifier')."</b></span>
		<br /><br /><span class='verdana2'> 
		<table cellpadding='3' cellspacing='1' border='0' width='100%'>
		<tr width='100%' bgcolor='".$couleur_claire."'>
		<td width='5%'valign='top'>
		<img src='".$ico_post."' alt='type' />
		</td>
		<td width='75%' valign='top'>
		<span class='verdana3'><b>".propre($titre)."</b></span><br />
		<span class='verdana2'><b>".$auteur."</b></span><br />
		</td><td width='20%' valign='top'>
		</td></tr><tr bgcolor='".$couleur_claire."'>
		<td colspan='3' valign='top'>
		<span class='verdana3'>".propre(smileys($affiche_texte))."</span>";
		if ($nom_site_forum)
			{ $avant_post.="
			<div align='right' class='verdana2'><br />- - - - -<br />
			<b><a href='".$url_site."'>".$nom_site_forum."</a></b></div>";
			}
	$avant_post.="
		</div>
		</td></tr>
		</table></span>\n
		<input type='hidden' name='hash' value='".$hash."' />\n
		<input type='hidden' name='alea' value='".$alea."' />\n
		";
	
		if ($verrou_ed!='oui')
		{ $avant_post.="
			<div align='right'>
			<input type='submit' name='valid_post' value='"._T('forum_message_definitif')."' class='fondo' />\n
			</div>\n"; }

	echo $avant_post;
	}


#
# formulaire
#

# prepare le titre du sujet si vide / altere titre du post
if($titre_sujet) { $insert_titre = _T('gaf:re_reponse_post').$titre_sujet; }
if($titre=='') { $titre=$insert_titre; }

	echo "<div style='float:left; width:90px; padding:160px 2px 0px 2px;'>";
	debut_cadre_relief("");
		tableau_smileys('',false);
	fin_cadre_relief();
	echo "</div>";
	
$form_post="
	<div style='padding:10px;' class='verdana3'><img src='".$ico_post."' alt='type' /><b>".$text_intro."</b></div>
	<div class='verdana2' style='margin-left:100px;'>

	<fieldset><legend><b>"._T('forum_titre')."</b></legend>
		<label>
		<input type='text' name='titre' value='".propre($titre)."' class='forml' size='40' />
		</label>
	</fieldset><br />
	<fieldset><legend><b>"._T('forum_texte')."</b></legend>
		<p>"._T('info_creation_paragraphe')."</p>".
		barre_forum_inspipbb($texte).
	"</fieldset><br />
	<fieldset><legend>"._T('forum_lien_hyper')."</legend>
		<p>"._T('forum_page_url')."</p>
		<p><label>"._T('forum_titre')."
		<input type='text' name='nom_site_forum' class='forml' size='40' value='".$nom_site_forum."' />
		</label></p>
		<p><label>"._T('forum_url')."
		<input type='text' name='url_site' class='forml' size='40' value='".($url_site ? $url_site : "http://")."' />
		</label></p>
	</fieldset><br />
	<fieldset><legend>"._T('forum_qui_etes_vous')."</legend>
		<p><label>"._T('forum_votre_nom')."
		<input type='text' name='auteur' value='".$auteur."' class='forml' size='40' />
		</label></p>
		<p><label>"._T('forum_votre_email')."
		<input type='text' name='email_auteur' value='".$email_auteur."' class='forml' size='40' />
		</label></p>
	</fieldset><br />
	
	<input type='hidden' name='previsu' value='1'>
	<input type='hidden' name='id_auteur' value='".$id_auteur."'>
	<input type='hidden' name='forum' value='".$forum."'>
	<input type='hidden' name='sujet' value='".$sujet."'>
	
	<div align='right'>
	<input type='submit' value='"._T('forum_voir_avant')."' class='fondo' />\n
	</div>\n
	
	</div>\n
	";

echo $form_post;
echo "</form>";
}


#
# Traiter et enregistrer le post
#

function enregistre_post_spipbb() {

	#global $_POST;

	// requis
	include_spip("base/abstract_sql");

	
	//
	// Recuperer les donnees postees du formulaire
	foreach (array('auteur', 'email_auteur', 'id_auteur', 
		'nom_site_forum', 'url_site', 'texte', 'titre', 
		'alea', 'hash') as $item)
		{ $$item = $_POST[$item]; }
		/*$p_item = _request($item);
		{ $$item = $p_item; }*/

	foreach (array('forum', 'id_breve', 'id_syndic',
	'id_rubrique', 'sujet') as $id)
		
		if (isset($_POST[$id]))
			$$id = intval($_POST[$id]);
		/*
		$p_id = _request($id);
		if (isset($p_id))
			$$id = intval($p_id);
		*/
		else
			$$id = 0;


	// Verifier hash securite
	if (!verifier_action_auteur("ajout_forum $forum $sujet $alea", $hash)) {
		spipbb_log('erreur hash forum',3,"e_p_s");
		die (_T('forum_titre_erreur')); 	# echec du POST
	}
	// verifier fichier lock
	$alea = preg_replace('/[^0-9]/', '', $alea);
	if (!file_exists($hash = _DIR_SESSIONS."forum_$alea.lck"))
		return /*$retour_forum*/; # echec silencieux du POST
	unlink($hash);


	//
	// premier insert du message dans la base
	//
	# id_thread oblige INSERT puis UPDATE. ??
	$id_message = @sql_insertq('spip_forum', array('date_heure'=>'NOW()'));

	if ($sujet) { $id_thread =  $sujet; }
	else { $id_thread = $id_message; } 
	
	$statut = ($statut == 'non') ? 'off' : (($statut == 'pri') ? 'prop' : 'publie');

	//
	// màj bdd
	//
	@sql_updateq("spip_forum", array(
					'id_parent' => $sujet,
					'id_rubrique' => $id_rubrique,
					'id_article' => $forum,
					'id_breve' => $id_breve,
					'id_syndic' => $id_syndic,
					'id_auteur' => $id_auteur,
					'id_thread' => $id_thread,
					'date_heure' => 'NOW()',
					'titre' => addslashes(corriger_caracteres($titre)),
					'texte' => addslashes(corriger_caracteres($texte)),
					'nom_site' => addslashes(corriger_caracteres($nom_site_forum)),
					'url_site' => addslashes(corriger_caracteres($url_site)),
					'auteur' => addslashes(corriger_caracteres($auteur)),
					'email_auteur' => addslashes(corriger_caracteres($email_auteur)),
					'ip' => $_SERVER['REMOTE_ADDR'],
					'statut' => $statut),
				"id_forum = $id_message" //where
			);
	
	spipbb_log('nouveau post: '.$id_thread,3,"e_p_s");
	
	// Notification
	if ($notifications = charger_fonction('notifications', 'inc'))
		$notifications('forumposte', $id_message);

	//
	// INVALIDATION DES CACHES LIES AUX FORUMS, modifié gaf
	if ($statut == 'publie') {
		include_spip('inc/invalideur');		
		include_spip('inc/forum');
		
		suivre_invalideur ("id='id_forum/" . calcul_index_forum($forum, $id_breve, $id_rubrique, $id_syndic) . "'");
	}
}

function tout_de_selectionner($nomformulaire='') {
	if (empty($nomformulaire)) return ;
	$res = "<script language=\"JavaScript\" type=\"text/javascript\">\n"
		. "<!--\n"
		. "	function check_switch(val)\n"
		. "	{\n"
		. "		for( i = 0; i < document.$nomformulaire.elements.length; i++ )\n"
		. "		{\n"
		. "			document.$nomformulaire.elements[i].checked = val;\n"
		. "		}\n"
		. "	}\n"
		. "//-->\n"
		. "</script>\n"
		. "<div style='text-align:right;margin: 4px 0 4px 0;'><a href=\"javascript:check_switch(true);\">"
		. _T('spipbb:bouton_select_all')
		. "</a> :: <a href=\"javascript:check_switch();\">"
		. _T('spipbb:bouton_unselect_all')."</a></div>\n";

	return $res;
} // tout_de_selectionner

?>