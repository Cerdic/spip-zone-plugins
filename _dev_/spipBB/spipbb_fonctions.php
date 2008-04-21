<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : spipbb_fonctions - fonctions communes         #
#  Authors : Scoty, Gurdil, Booz, Chryjs 2007 et           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

# requis
if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
include_spip('inc/traiter_imagerie');
include_spip('inc/spipbb_notifications');
include_spip('inc/spipbb_inc_formpost'); // tout_de_selectionner

// ------------------------------------------------------------------------------
// Filtre : insere_texte_alerter
// Scoty 11/08/07 - GAF 0.5
// Insere texte alerte-abus dans corps message pour webmaster
// ------------------------------------------------------------------------------
function insere_texte_alerter($texte,$insere)
{
	if (!$premiere_passe = _request('valide')) {
		if(_request('alerter')=='oui') {
			$origine=explode('-',_request('orig'));
			#$insere = _T('spipbb:alerter_texte');
			$lien_forum = generer_url_public('voirsujet',"id_forum=".$origine[0]."#forum".$origine[1],true);
			$texte = $insere."\n".$lien_forum."\n\n";
		}
	}
	return $texte;
} // insere_texte_alerter

// ------------------------------------------------------------------------------
//	Filtre : insere_sujet_alerter
//	Scoty 11/08/07 - GAF 0.5
//	Insere texte alerte-abus dans sujet message pour webmaster
// ------------------------------------------------------------------------------
function insere_sujet_alerter($sujet,$insere)
{
	if (!$premiere_passe = _request('valide')) {
		if(_request('alerter')=='oui') {
			#$insere = _T('spipbb:alerter_sujet');
			$sujet = $insere;
		}
	}
	return $sujet;
} // insere_sujet_alerter


// ------------------------------------------------------------------------------
// filtre :
// explode() !!
// scoty 26/10/07 - GAF v.0.6
// ------------------------------------------------------------------------------
function chaine2array($chaine,$sep='')
{
	$chaine=trim($chaine);
	if(!$sep) $sep = ',';
	if($chaine=='') { $chaine=array(); }
	else { $chaine = explode($sep,$chaine); }
	return $chaine;
} // chaine2array


// ------------------------------------------------------------------------------
//	Filtre : spipbb_maintenance ex gaf_maintenance
//	scoty 26/09/07 - GAF v.0.6
//	Sur balise id_article.
//	Signaler une maintenance (donc ferme temporaire)
// ------------------------------------------------------------------------------
function spipbb_maintenance($id_article)
{
	if ($ds = @opendir(_DIR_SESSIONS)) {
		while (($file = @readdir($ds)) !== false) {
			if (preg_match('/^gafart_([0-9]+)-([0-9]+)\.lck$/', $file, $match)) {
				if($match[1] == $id_article) { return "1"; }
			}
		}
	}
} // spipbb_maintenance



//+---------------------------------------------+
//Filtre : Nombre de messages
//base : BoOz Email:booz@bloog.net
//Compte le nombre de messages d'un auteur
//Appel dans squellette : [(#ID_AUTEUR|spipbb_nb_messages)]
//+---------------------------------------------+
## h.
# a renommer plus simple ! => |nombre_post ??
##
function spipbb_nb_messages($id_auteur){
	if (empty($id_auteur)) return ;
	$nb_mess = "";
	if ( is_array($GLOBALS['spipbb'])
		AND $GLOBALS['spipbb']['configure']=='oui'
		AND $GLOBALS['spipbb']['id_secteur']>0 )
		$result_auteurs = sql_select('auteur',
							array('spip_forum','spip_rubriques'), // FROM
							array("id_auteur=$id_auteur",
									"spip_forum.id_rubrique=spip_rubriques.id_rubrique",
									"( spip_rubriques.id_rubrique=".$GLOBALS['spipbb']['id_secteur']." OR spip_rubriques.id_secteur=".$GLOBALS['spipbb']['id_secteur']." )"
									) //WHERE
							);
	else $result_auteurs = sql_select('auteur','spip_forum',"id_auteur=$id_auteur");
	$nb_mess = sql_count($result_auteurs);
	return $nb_mess;
} // spipbb_nb_messages



// Calcule le nombre de messages par auteur et les classes par ordre decroissant
function spipbb_nb_messages_groupe($id_bidon){
	$aut_nb = array();
	$result_auteurs = sql_select('sf.id_auteur, sa.nom AS auteur, COUNT(sa.nom) AS total','spip_forum AS sf, spip_auteurs AS sa',
							"sf.statut='publie' AND sf.id_auteur>0 AND sf.id_auteur=sa.id_auteur AND sa.statut!='5poubelle' " , // WHERE
							"sf.id_auteur", // GROUPBY
							array("total desc"), // ORDERBY
							"10" // LIMIT
							);
	$compte = 0;
	if (!isset($GLOBALS['spipbb'])) $GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']); // lire_config ?
	while ($row = sql_fetch($result_auteurs) AND $compte++<10) {
		# 1/12/07 fct spipbb_auteur_infos() change de nom :
		$infos = spipbb_donnees_auteur($row['id_auteur']);
		if ( ( isset($infos['annuaire_forum'])  AND $infos['annuaire_forum']!='non')
				OR
				( $GLOBALS['spipbb']['affiche_membre_defaut']=='oui' AND (!isset($infos['annuaire_forum']) OR  !$infos['annuaire_forum'] ) ) )
		 {
			// Peut apparaitre dans la liste
			$aut_nb[]=$row['auteur']."(".$row['total'].")";
		}
	}

	return join(", ",$aut_nb) ;
} // spipbb_nb_messages_groupe

//+----------------------------------+
//Filtre :  citation
//Base : BoOz
//Modif scoty  29/10/06 .. -> spip 1.9.1/2
//Affiche le texte à citer
//+-------------------------------------+
function barre_forum_citer($texte, $lan, $rows, $cols, $lang='')
{
	if (!$premiere_passe = rawurldecode(_request('retour_forum'))) {
		if(_request('citer')=='oui'){
			$id_citation = _request('id_forum') ;
			//$query = "SELECT auteur, texte FROM spip_forum WHERE id_forum=$id_citation";
			//$result = sql_query($query);
			//$row = sql_fetch($result);
			$row = sql_fetsel('auteur,texte','spip_forum',"id_forum=$id_citation");
			$aut_cite=$row['auteur'];
			$text_cite=$row['texte'];
			//ajout de la citation
			$texte="{{ $aut_cite $lan }}\n<quote>\n$text_cite</quote>\n";
		}
	}
	return barre_textarea($texte, $rows, $cols, $lang);
} // barre_forum_citer

// ------------------------------------------------------------------------------
// chryjs : 11/1/8
// fait un join sur un tableau compose de key="nomauteur" value ="nbposts"
// utilise $filtre pour faire ses remplacements ligne par ligne
// ------------------------------------------------------------------------------
function spipbb_join_membre($liste_cnt=array(),$filtre="%NOM% [%TOTAL%]<br />"){
	reset($liste_cnt);
	$res = "";
	while (list($nom,$nb)=each($liste_cnt)) {
		$res .= str_replace(array("%NOM%","%TOTAL%"),array($nom,$nb." "._T('spipbb:message_s')),$filtre)."\n";
	}
	return $res;
} // spipbb_join_membre

// ------------------------------------------------------------------------------
// chryjs : 12/1/8
// Identifie si un (id_)auteur est moderateur de l'article == forum passé en paramètre
// Retourne 'oui' si modo, 'non' dans les autres cas
// Attention en 1.9.2 , $id_auteur _doit_ etre un int sinon -> pas autorise
// ------------------------------------------------------------------------------
function is_modo($id_auteur=0,$id_article=0) {
	if (!function_exists('autoriser')) include_spip('inc/autoriser'); // 1.9.2 surtout
	if (autoriser('modifier','article',$id_article,intval($id_auteur))) return 'oui';
	else return 'non';
} //

?>
