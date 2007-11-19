<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : spipbb_fonctions - fonctions communes         #
#  Authors : Scoty, Gurdil, Booz, Chryjs 2007 et           #
#  http://www.spip-contrib.net/Plugin-SpipBB#contributeurs #
#  Contact : chryjs!@!free!.!fr                            #
#----------------------------------------------------------#

//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

if (!function_exists('sql_query')) include_spip('inc/spipbb_192');

// ------------------------------------------------------------------------------
// generique : balise #SPIPBB{champ}, donnee brut (->formulaire)
// GAF v.0.6 - 12/10/07 
// ------------------------------------------------------------------------------
function afficher_champ_spipbb($id_auteur,$champ)
{
	$infos = spipbb_auteur_infos($id_auteur);
	if($ch=$infos[$champ]) {
		return $ch;
	}
	return;
} // afficher_champ_spipbb

// ------------------------------------------------------------------------------
// traitement back / balise signature_post
// GAF v.0.6 - 12/10/07 
// ------------------------------------------------------------------------------
function spipbb_afficher_signature_post($id_auteur)
{
	#recup de statut et extra/avatar
	$infos = spipbb_auteur_infos($id_auteur);
	if($infos['signature_post']!='') {
		return propre($infos['signature_post']);
	}
	return;
} // spipbb_afficher_signature_post

// ------------------------------------------------------------------------------
// traitement balise affiche_avatar / + fonction back
// Fonction relais Affichage Avatar
// Soit appel fonction perso :
// [ inc_afficher_avatar() dans fichier inc/afficher_avatar.php ].
// Soit fonction GAF : spipbb_afficher_avatar()
// ------------------------------------------------------------------------------
// GAF v.0.6 - 4/10/07
// ------------------------------------------------------------------------------
function afficher_avatar($id_auteur, $classe='')
{
	$f='';
	if(lire_config('spipbb/affiche_avatar')=="oui") {
		#si modeles/avatar.html present
		if($av = find_in_path("modeles/avatar.html")) {
			$f = recuperer_fond('modeles/avatar',
					array('id_auteur' => $id_auteur, 'classe' => $classe));
		}
		# si function inc_afficher_avatar(...) dans inc/afficher_avatar.php
		# sinon GAF fait son office
		else {
			$f = charger_fonction('afficher_avatar', 'inc', true);
			if(!$f) { $f='spipbb_afficher_avatar'; }
			return $f($id_auteur, $classe);
		}
	}
	return $f;
} // afficher_avatar


// ------------------------------------------------------------------------------
// spipbb_afficher_avatar (back ou front)
// ------------------------------------------------------------------------------
// GAF v.0.6 - 4/10/07
// ------------------------------------------------------------------------------
function spipbb_afficher_avatar($id, $classe='')
{
	if(!$id) { return; }
	#recup de statut et extra/avatar
	$infos = spipbb_auteur_infos($id);

	$insert = '';
	$retour = '';

	if ($classe!='') { $insert=" class=\"$classe\""; }

	if ($infos['statut']=="0minirezo" OR $infos['statut']=="1comite") {
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if($logo = $chercher_logo($id, 'id_auteur', 'on')) {
			$retour="<img".$insert." src=\"$logo[0]\" alt=\"logo\" />";
		}
	}
	elseif($infos['statut']=="6forum" AND $infos['avatar']!='') {
			$retour="<img".$insert." src=\"".$infos['avatar']."\" alt=\"avatar\" />";
	}
	return $retour;
} // spipbb_afficher_avatar


// ------------------------------------------------------------------------------
// relais
// ------------------------------------------------------------------------------
// GAF v.0.6 - 4/10/07
// ------------------------------------------------------------------------------
function afficher_signature_post($id_auteur)
{
	$f='';
	if(lire_config('spipbb/affiche_signature_post')=='oui' AND $id_auteur!=0) {
		if($av = find_in_path("modeles/signature_post.html")) {
			$f = recuperer_fond('modeles/signature_post',
					array('id_auteur' => ".$_id_auteur.",'classe' => '$_classe'));
		}
		# si function inc_afficher_signature(...) dans inc/afficher_signature.php
		# sinon GAF fait son office
		else {
			$f = charger_fonction('afficher_signature_post', 'inc', true);
			if(!$f) { $f='spipbb_afficher_signature_post'; }
			return $f($id_auteur);
		}
	}
	return $f;
} // afficher_signature_post

// ------------------------------------------------------------------------------
// Filtre : insere_texte_alerter
// ------------------------------------------------------------------------------
// Scoty 11/08/07 - GAF 0.5
// Insere texte alerte-abus dans corps message pour webmaster
// ------------------------------------------------------------------------------
function insere_texte_alerter($texte,$insere)
{
	if (!$premiere_passe = _request('valide')) {
		if(_request('alerter')=='oui') {
			$origine=explode('-',_request('orig'));
			#$insere = _T('forg:alerter_texte');
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
			#$insere = _T('forg:alerter_sujet');
			$sujet = $insere;
		}
	}
	return $sujet;
} // insere_sujet_alerter

// ------------------------------------------------------------------------------
// Lister un repert de smileys passe en arg
// ------------------------------------------------------------------------------
// d'apres BoOz spipBB
// ------------------------------------------------------------------------------
function genere_list_smileys($repert)
{
	$listimag=array();
	$listfich=opendir($repert);
	while ($fich=@readdir($listfich)) {
		if(($fich !='..') and ($fich !='.') and ($fich !='.test') and ($fich !='.svn')) { 
			$nomfich=substr($fich,0,strrpos($fich, "."));
			$listimag[$nomfich]=$repert.$fich;
		}
	}
	ksort($listimag);
	reset($listimag);
	return $listimag;
} // genere_list_smileys

// ------------------------------------------------------------------------------
// filtre smileys
// Sur base filtre smileys2 - Booz
// Recup les smileys du repert smileys/ ou mes_smileys/
// Exemple d'application : [(#TEXTE|smileys)]
// ------------------------------------------------------------------------------
// Scoty GAF v.0.6 - 30/09/07
// ------------------------------------------------------------------------------
function smileys($chaine)
{
	//$dirbase = _DIR_PLUGINS."gafospip/smileys/";
	$dirbase = _DIR_SMILEYS_SPIPBB;
	$listsmil = genere_list_smileys($dirbase);
	/*
	if(_DIR_SMILEYS_GAF!=$dirbase) {
		$listsmil = array_merge($listsmil, $listperso=genere_list_smileys(_DIR_SMILEYS_GAF));
	}
	*/
	while (list($nom,$chem) = each($listsmil)) {
		$smil_html = "<img src=\"".$chem."\" border=\"0\" title=\"".$nom."\"  alt=\"smiley\" align=\"baseline\" />";
		$chaine = str_replace(":".$nom, $smil_html , $chaine);
	}
	return $chaine;
} // smileys

// ------------------------------------------------------------------------------
// Generer le tableau de smileys,
// pour formulaires de post (back/frontoffice).
// Voir balise ci-apres
// ------------------------------------------------------------------------------
// Scoty GAF v.0.6 - 30/09/07
// ------------------------------------------------------------------------------
function tableau_smileys($cols='',$return=true)
{
	$listimag = genere_list_smileys(_DIR_SMILEYS_SPIPBB);
	// nombre de colonnes (2 par défaut) (pas trop large pour GAF ! !!)
	if($cols=='') { $cols=2; }
	$compte=0;

	$aff = "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\"><tr>\n";
	while (list($nom,$chem) = each($listimag)) { 
		$aff.= "<td valign=\"bottom\" class=\"verdana1\"><div align=\"center\">
			<a href=\"javascript:emoticon(':".$nom."')\">
			<img src=\"".$chem."\" border=\"0\" title=\"smiley - ".$nom."\" />
			</a></div></td>\n";
		
		$compte++; 
		if ($compte % $cols == 0) { $aff.= "</tr><tr>\n"; }
	}
	$aff.= "</tr></table>\n";

	if($return) { return $aff; } else { echo $aff; }
} // tableau_smileys

// ------------------------------------------------------------------------------
// GAF v.0.6 - 20/10/07
// spipbb_auteur_infos // ex gaf_auteur_infos
// infos recuperables pour auteur : $id (table)
// appel : ici, formulaire_spipbb_profil, spipbb_inscrits, spipbb_notifications
// ------------------------------------------------------------------------------
function spipbb_auteur_infos($id)
{
	$chps_sup='';
	$left_join='';
	$infos=array();
	$liste_chps = array();
	
	$type_support = lire_config('spipbb/support_auteurs');
	$table_support = lire_config('spipbb/table_support');

	foreach($GLOBALS['champs_sap_spipbb'] as $ch => $t) {
		$liste_chps[]=$ch;
	}
	
	if($type_support=="table") {
		foreach($liste_chps as $champ) {
			$chps_sup.= ", sap.".$champ;
		}
		$left_join = "LEFT JOIN spip_$table_support AS sap ON sa.id_auteur = sap.id_auteur ";
	}

	$result = sql_query("SELECT sa.id_auteur, sa.statut, sa.nom, sa.login, sa.source, sa.extra 
						$chps_sup 
						FROM spip_auteurs AS sa 
						$left_join
						WHERE sa.id_auteur=$id");
	if($row = sql_fetch($result)) {
		foreach($row as $k => $v) {
			# extraire extra
			if($k=='extra') {
				$chps_extra = ($v!=NULL) ? unserialize($v) : array();
				# on extrait champs gaf que si support : extra
				if($type_support=='extra') {
					foreach($liste_chps as $c) {
						$infos[$c]=$chps_extra[$c];
					}
				}
				# tous les autres extra
				foreach($chps_extra as $cle => $ve) {
					# recup tous extra sauf gaf
					if(!in_array($cle,$liste_chps)) {
						$infos[$cle]=$ve;
					}
				}
			}
			else {
				$infos[$k]=$v;
			}
		}
	}
	return $infos; # array: statut, nom .. avatar (et +)
} // spipbb_auteur_infos

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

/*
 *   +---------------------------------------------+
 *    Nom du Filtre : Nombre de messages 
 *   +---------------------------------------------+
 *    Date : mercredi 09 avril 2003
 *    Auteur : BoOz Email:booz@bloog.net
 *    site : http://bloog.net
 *   +---------------------------------------------+
 *    Fonctions de ce filtre :
 *    Compte le nombre de messages d'un auteur
 *     Appelez le dans vos squellette tout simplement
 *     par : [(#ID_AUTEUR|nb_messages)]
 *   +---------------------------------------------+
 *
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.uzine.net/spip_contrib/
 *
 */
function spipbb_nb_messages($id_auteur){
global $table_prefix;
	//$query = "SELECT auteur FROM ".$table_prefix."_forum WHERE id_auteur=$id_auteur";
	$nb_mess = "";
	//$result_auteurs = sql_query($query);
	$result_auteurs = sql_select('auteur','spip_forum',"id_auteur=$id_auteur");
	$nb_mess = sql_count($result_auteurs);
	return $nb_mess;
} // spipbb_nb_messages

/*
 *   +----------------------------------+
 *    Nom du Filtre :    citation
 *   +----------------------------------+
 *    BASE : ... Date : vendredi 11 novembre 2006 - Auteur :  BoOz
 *    
 *    MODIF .. SCOTY .. 29/10/06 .. -> spip 1.9.1/2 
 *   +-------------------------------------+
 *    Fonctions de ce filtre :
 *     affiche le texte à citer    
 *   +-------------------------------------+ 
 *  
 * Pour toute suggestion, remarque, proposition d'ajout
 * reportez-vous au forum de l'article :
 * http://www.spip-contrib.net/Pagination,663
*/
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

?>
