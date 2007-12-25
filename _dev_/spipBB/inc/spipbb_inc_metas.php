<?php
/*
|
*/

spipbb_log("included",3,__FILE__);

//----------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
//----------------------------------------------------------------------------
function spipbb_init_metas()
{
	// priorite a la config de spipbb
	$old_meta = @unserialize($GLOBALS['meta']['spipbb']); // recupere de vieilles metas
	
	// chargement de la config de gafospip le vrai !!
	if (!is_array($old_meta)) $old_meta=spipbb_import_gafospip_metas();
	
	unset($spipbb_meta);
	
	$spipbb_meta=array();
	
	$spipbb_meta['configure'] = $old_meta['configure'] ? $old_meta['configure'] :'non';
	$spipbb_meta['version']= $GLOBALS['spipbb_plug_version'];
	$spipbb_meta['id_secteur'] = $old_meta['id_secteur'] ? $old_meta['id_secteur'] : 0;
	$spipbb_meta['config_id_secteur'] = $old_meta['config_id_secteur'] ? $old_meta['config_id_secteur'] : 'non';

	$spipbb_meta['squelette_groupeforum']= $old_meta['squelette_groupeforum'] ? $old_meta['squelette_groupeforum'] : "groupeforum";
	$spipbb_meta['squelette_filforum']= $old_meta['squelette_filforum'] ? $old_meta['squelette_filforum'] : "filforum";
	if ( find_in_path("groupeforum.html") AND find_in_path("filforum.html") )
		$spipbb_meta['config_squelette'] = 'oui';
	else 
		$spipbb_meta['config_squelette'] = 'non';

	// les mots cles specifiques
	$spipbb_meta['id_groupe_mot'] = $old_meta['id_groupe_mot'] ? $old_meta['id_groupe_mot'] : 0;
	$spipbb_meta['config_groupe_mots'] = $old_meta['config_groupe_mots'] ? $old_meta['config_groupe_mots'] : 'non';
	$spipbb_meta['id_mot_ferme'] = $old_meta['id_mot_ferme'] ? $old_meta['id_mot_ferme'] : 0;
	$spipbb_meta['id_mot_annonce'] = $old_meta['id_mot_annonce'] ? $old_meta['id_mot_annonce'] : 0;
	$spipbb_meta['id_mot_postit'] = $old_meta['id_mot_postit'] ? $old_meta['id_mot_postit'] : 0;
	$spipbb_meta['config_mot_cles'] = $old_meta['config_mot_cles'] ? $old_meta['config_mot_cles'] : 'non';

	// gafospip
	#stockage des champs supplementaires
	$spipbb_meta['support_auteurs'] = $old_meta['support_auteurs'] ? $old_meta['support_auteurs'] : 'extra'; //$options_sap = array('extra','table');
	$spipbb_meta['table_support'] = $old_meta['table_support'] ? $old_meta['table_support'] : '';
	#champs supplementaires auteurs
	$champs_requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
	$champs_definis=array();
	foreach ($GLOBALS['champs_sap_spipbb'] as $champ => $params) {
		$champs_definis[]=$champ;
	}
	$champs_optionnels = array_diff($champs_definis,$champs_requis);
	foreach ($champs_optionnels as $champ_a_valider) {
		$spipbb_meta['affiche_'.$champ_a_valider]=$old_meta['affiche_'.$champ_a_valider] ? $old_meta['affiche_'.$champ_a_valider] : 'oui';
	}
	# autres parametres
	$spipbb_meta['fixlimit'] = $old_meta['fixlimit'] ? $old_meta['fixlimit'] : 10;
	$spipbb_meta['lockmaint'] = $old_meta['lockmaint'] ? $old_meta['lockmaint'] : 600;
	$spipbb_meta['affiche_bouton_abus'] = $old_meta['affiche_bouton_abus'] ? $old_meta['affiche_bouton_abus'] : 'non';
	$spipbb_meta['affiche_bouton_rss'] = $old_meta['affiche_bouton_rss'] ? $old_meta['affiche_bouton_rss'] : 'un';
	$spipbb_meta['affiche_avatar'] = $old_meta['affiche_avatar'] ? $old_meta['affiche_avatar'] : 'oui';
	$spipbb_meta['taille_avatar_suj'] = $old_meta['taille_avatar_suj'] ? $old_meta['taille_avatar_suj'] : 50;
	$spipbb_meta['taille_avatar_cont'] = $old_meta['taille_avatar_cont'] ? $old_meta['taille_avatar_cont'] : 80;
	$spipbb_meta['taille_avatar_prof'] = $old_meta['taille_avatar_prof'] ? $old_meta['taille_avatar_prof'] : 80;
	$spipbb_meta['affiche_bouton_abus'] = $old_meta['affiche_bouton_abus'] ? $old_meta['affiche_bouton_abus'] : 'non';
	$spipbb_meta['affiche_bouton_rss'] = $old_meta['affiche_bouton_rss'] ? $old_meta['affiche_bouton_rss'] : 'un';
	// chemin icones et smileys ?

	// spam words
	$spipbb_meta['config_spam_words'] = $old_meta['config_spam_words'] ? $old_meta['config_spam_words'] : 'non';
	$spipbb_meta['sw_nb_spam_ban'] = $old_meta['sw_nb_spam_ban'] ? $old_meta['sw_nb_spam_ban'] : 3;
	$spipbb_meta['sw_ban_ip'] = $old_meta['sw_ban_ip'] ? $old_meta['sw_ban_ip'] : "non";
	$spipbb_meta['sw_admin_can_spam'] = $old_meta['sw_admin_can_spam'] ? $old_meta['sw_admin_can_spam'] : "non";
	$spipbb_meta['sw_modo_can_spam'] = $old_meta['sw_modo_can_spam'] ? $old_meta['sw_modo_can_spam'] : "non";
	$spipbb_meta['sw_send_pm_warning'] = $old_meta['sw_send_pm_warning'] ? $old_meta['sw_send_pm_warning'] : "non";
	$spipbb_meta['sw_warning_from_admin'] = $old_meta['sw_warning_from_admin'] ? $old_meta['sw_warning_from_admin'] : 1; // id_auteur
	$spipbb_meta['sw_warning_pm_titre'] = $old_meta['sw_warning_pm_titre'] ? $old_meta['sw_warning_pm_titre'] : _T('spipbb:sw_pm_spam_warning_titre');
	$spipbb_meta['sw_warning_pm_message'] = $old_meta['sw_warning_pm_message'] ? $old_meta['sw_warning_pm_message'] : _T('spipbb:sw_pm_spam_warning_message');

	// [fr] Nettoyage des traces [en] remove old metas
	spipbb_delete_metas();
	
	// Ecrire metas
	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($spipbb_meta));
	if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192
	
	// Redef. GLOBALS
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
	
	spipbb_log('END '.$GLOBALS['meta']['spipbb'],3,"init_metas");
} // spipbb_init_metas

//----------------------------------------------------------------------------
// Importe les metas de GAFOSPIP s'ils existent, retourne un tableau
//----------------------------------------------------------------------------
function spipbb_import_gafospip_metas()
{
	if (isset($GLOBALS['meta']['gaf_install'])) {
		$tbl_conf=@unserialize($GLOBALS['meta']['gaf_install']);
		$spipbb_meta=array();
		# non ! c'est bloquant lors de l'install ()
		# on le recuperera plus tard (dans admin_reconfig)
		#$spipbb_meta['id_groupe_mot']=$tbl_conf['groupe']; # num groupe
		$spipbb_meta['fixlimit']=$tbl_conf['fixlimit']; # n lignes
		$spipbb_meta['lockmaint']=$tbl_conf['lockmaint']=intval(_request('lockmaint')); # n secondes
		
		## h. 1/12/07
		# ceci repond a gafosip 0.6 -> il ne sera pas publie a priori !!
		# donc on zap ceci :
		/*
		$spipbb_meta['support_auteurs']=$tbl_conf['support_auteurs']; # extra / table / autre
		$spipbb_meta['table_support']=$tbl_conf['table_support']; # nom table generique, ex. : auteurs_profils
		#$tbl_conf['champs_gaf']; # array xx,yy,zz
		$spipbb_meta['taille_avatar_suj']=$tbl_conf['taille_avatar_suj']; # nbr pix
		$spipbb_meta['taille_avatar_cont']=$tbl_conf['taille_avatar_cont']; # nbr pix
		$spipbb_meta['taille_avatar_prof']=$tbl_conf['taille_avatar_prof']; # nbr pix
		$spipbb_meta['affiche_avatar']=$tbl_conf['affiche_avatar']; # oui/non
		$spipbb_meta['affiche_bouton_abus']=$tbl_conf['affiche_bouton_abus']; # oui/non
		$spipbb_meta['affiche_bouton_rss']=$tbl_conf['affiche_bouton_rss']; # non/un/tout
		###$tbl_conf['affiche_signature']; # oui/non

		# affichage champ 'nnn_nnn'
		#champs supplementaires auteurs
		$champs_requis = array('date_crea_spipbb','avatar','annuaire_forum','refus_suivi_thread');
		$champs_definis=array();
		foreach ($GLOBALS['champs_sap_spipbb'] as $champ => $params) {
			$champs_definis[]=$champ;
		}
		$champs_optionnels = array_diff($champs_definis,$champs_requis);
		foreach ($champs_optionnels as $champ_a_valider) {
			$spipbb_meta['affiche_'.$champ_a_valider]=$tbl_conf['affiche_'.$champ_a_valider];
		}
		*/
		# h. fin gaf 0.6
		
		return $spipbb_meta;
	}
	else return array();
} // spipbb_import_gafospip_metas


//----------------------------------------------------------------------------
// [fr] Supprimer les metas du plugin (desinstallation)
// [en] Delete plugin metas
//----------------------------------------------------------------------------
function spipbb_delete_metas()
{
	if (isset($GLOBALS['meta']['spipbb']))
	{
		include_spip('inc/meta');
		effacer_meta('spipbb');
		effacer_meta('spipbb_fromphpbb'); // requis si la migration n est pas finie
		if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192
		unset($GLOBALS['meta']['spipbb']);
		spipbb_log('OK',3,'inc/spipbb.php : delete_metas');
	}
} // spipbb_delete_metas


//----------------------------------------------------------------------------
// [fr] Initialisation des valeurs de meta du plugin aux defauts
// [en] Init plugin meta to default values
//----------------------------------------------------------------------------
function spipbb_save_metas()
{
	$GLOBALS['spipbb']['config_id_secteur'] = empty($GLOBALS['spipbb']['id_secteur']) ? 'non' : 'oui';
	$GLOBALS['spipbb']['config_groupe_mots'] = empty($GLOBALS['spipbb']['id_groupe_mot']) ? 'non' : 'oui';
	$GLOBALS['spipbb']['config_mot_cles'] = ( 
			empty($GLOBALS['spipbb']['id_mot_ferme']) or
			empty($GLOBALS['spipbb']['id_mot_annonce']) or 
			empty($GLOBALS['spipbb']['id_mot_postit']) ) ? 'non' : 'oui';
	if ( find_in_path($GLOBALS['spipbb']['squelette_groupeforum']) AND 
		find_in_path($GLOBALS['spipbb']['squelette_filforum']) )
		$GLOBALS['spipbb']['config_squelette'] = 'oui';

	include_spip('inc/meta');
	ecrire_meta('spipbb', serialize($GLOBALS['spipbb']));
	if (defined('_INC_SPIPBB_192')) ecrire_metas(); // Code 192
	$GLOBALS['spipbb'] = @unserialize($GLOBALS['meta']['spipbb']);
	spipbb_log('OK '.$GLOBALS['meta']['spipbb'],3,"spipbb_save_metas");
} // spipbb_save_metas


//----------------------------------------------------------------------------
// [fr] Met a jour les metas du plugin
// [en] Upgrade plugin metas
//----------------------------------------------------------------------------
function spipbb_upgrade_metas($installed_version='',$version_code='') {

	if($installed_version=='') {
		$installed_version=='0.0.0';
	}

	if ( version_compare(substr($installed_version,0,5),$version_code,'<' ) ) {
		spipbb_init_metas();
	} 
	// else si on fait des changements apres la 0.3.0
	
	spipbb_log('OK',3,"spipbb_upgrade_metas");
} // spipbb_upgrade_metas


?>
