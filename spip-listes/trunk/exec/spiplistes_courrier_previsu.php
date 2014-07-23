<?php
/**
 * @version Original From SPIP-Listes-V :: Id: spiplistes_listes_forcer_abonnement.php paladin@quesaco.org
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$

/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');
include_spip('inc/spiplistes_api_globales');

// adapte de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com

/**
 * Affiche previsu d'un courrier
 * - en plein ecran si demande
 * - sinon pour import iframe
 * - format html ou texte seul, si demande
 * 
 * Utilise par courrier_gerer et courrier_edit
 * 
 * CP-20080322 : 
 * - ce script devrait plutot etre en action/ au lieu d'exec/ ?
 * - charset en previsu plein ecran texte seul : Mozilla affiche parfois en iso ? parfois respecte UTF-8 !
 * CP-20071011
 * */

function exec_spiplistes_courrier_previsu () {

	global $meta;

	include_spip('base/abstract_sql');
	include_spip('inc/presentation');
	include_spip('inc/distant');
	include_spip('inc/date');
	include_spip('inc/urls');
	include_spip('inc/meta');
	include_spip('inc/filtres');
	include_spip('inc/lang');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_courrier');
	//include_spip('inc/spiplistes_api_abstract_sql'); // obsolete
	include_spip('public/assembler');
	
	spiplistes_debug_log ('ACTION: exec_spiplistes_courrier_previsu()');

	static $eol = PHP_EOL;
	
	$int_values = array(
		'id_rubrique', 'id_mot', 'id_courrier', 'id_liste'
	);
	$str_values = array(
		'lang'
		, 'jour', 'heure'
		, 'avec_intro', 'message_intro'
		, 'avec_patron', 'patron', 'patron_pos'
		, 'avec_sommaire'
		, 'titre', 'message', 'pied_patron'
		, 'Confirmer', 'date'
		, 'lire_base', 'format', 'plein_ecran'
		, 'date_sommaire'
		, 'oeil_html', 'oeil_texte'
	);
	foreach(array_merge($str_values, $int_values) as $key) {
		$$key = _request($key);
		// méfiance sur jQuery 1.4.4 qui semble
		// ne plus transmettre les vars qu'en 1.4.2
		// @todo: a creuser, verifier les autres vars ajax transmises
		//spiplistes_debug_log('PREVISU: '.$key.': '.$$key);
	}
	foreach($int_values as $key) {
		$$key = intval($$key);
	}

	list($jour,$mois,$annee) = explode('/',$jour);
	list($heure,$minute) = explode(':',$heure);

	$date = format_mysql_date($annee,$mois,$jour,$heure,$minute);
	
	$charset = $meta['charset'];

	$contexte = array(
			'id_courrier' => $id_courrier
			, 'lang' => $lang
			);
	
	list($lien_html, $lien_texte) = spiplistes_courriers_assembler_patron (
		_SPIPLISTES_PATRONS_TETE_DIR . spiplistes_pref_lire('lien_patron'),
		$contexte,
		!((spiplistes_pref_lire('opt_lien_en_tete_courrier') == 'oui') && $id_courrier)
		);
	
	list($pied_html, $pied_texte) = spiplistes_pied_page_assembler_patron ($id_liste,
												$lang,
												$pied_patron);
	
	$texte_intro = $texte_patron =
		$tampon_html = $tampon_texte =
		$sommaire_html = '';
	
	if(spiplistes_pref_lire('opt_ajout_tampon_editeur') == 'oui') {
		list($tampon_html, $tampon_texte) = spiplistes_tampon_assembler_patron();
	}
	
	if($lire_base) {
		/**
		 * Prendre le courrier enregistre dans la base
		 */
		$sql_select = 'texte,titre' . (($format=='texte') ? ',message_texte' : '');
		if(
			$id_courrier 
			&& ($row = sql_fetsel($sql_select, "spip_courriers", "id_courrier=".sql_quote($id_courrier), "", "", 1))
		) {
			foreach(explode(",", $sql_select) as $key) {
				$$key = propre($row[$key]);
			}
			
			//if($plein_ecran) {
			
				$texte_html = ''
					. $lien_html
					. $texte
					. $pied_html
					. $tampon_html
					;
					
				if($format=="texte") {
				
					header("Content-Type: text/plain; charset=$charset");
					
					// forcer IE a afficher en ligne. 
					header("Content-Disposition: inline; filename=spiplistes-previsu.txt");

					$message_texte = 
						empty($message_texte) 
						? spiplistes_courrier_version_texte($texte_html) 
						: spiplistes_courrier_version_texte($lien_texte)
							. spiplistes_courrier_version_texte($message_texte)
							. $pied_texte
							. spiplistes_courrier_version_texte($tampon_texte)
						;
					echo($message_texte);
					exit(0);
				}
				// else 
				$texte_html = ''
					. '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">'.$eol
					. (($lang) ? '<html lang="'.$lang.'" dir="ltr">'.$eol : '')
					. '<head>'.$eol
					. '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'">'.$eol
					. '<meta http-equiv="Pragma" content="no-cache">'.$eol
					. '<title>'.textebrut($titre).'</title>'.$eol
					. '</head>'.$eol
					. '<body style="text-align:center">'.$eol
					. '<div style="margin:0 auto">'.$eol
					. $texte_html
					. '</div>'.$eol
					. '</body>'.$eol
					. '</html>'.$eol
					;
				ajax_retour($texte_html);
				exit(0);
			//} // end if plein_ecran
		}
		else {
			echo(_T('spiplistes:erreur_courrier_introuvable'));
		}
	}
	/**
	 * Si nouveau courrier (pas dans la base), generer un apercu
	 */
	else {
		
		spiplistes_debug_log('ACTION: generate preview');

		$intro_html = $intro_texte = 
			$sommaire_html = $sommaire_texte = '';
		
		if($avec_intro == 'oui') {
			$ii = propre($message_intro);
			$intro_html = '<div>'.$ii.'</div>'.$eol;
			$intro_texte = spiplistes_courrier_version_texte($ii).$eol.$eol;
		} 

		if($avec_patron == 'oui') {
			// generer le contenu (editeur)

			include_spip('public/assembler');	
			$contexte_template = array(
				'date' => trim ($date)
				, 'id_rubrique' => $id_rubrique
				, 'id_mot' => $id_mot
				, 'patron' => $patron
				, 'lang' => $lang
				, 'sujet' => propre($titre)
				, 'message' => $message
			);
			
			$titre_html = _T('spiplistes:lettre_info').' '.$nomsite;
			$titre_texte = spiplistes_courrier_version_texte($titre_html) . $eol;

			list($message_html, $message_texte) = spiplistes_courriers_assembler_patron (
				_SPIPLISTES_PATRONS_DIR . $patron
				, $contexte_template);
				
		} // end if($avec_patron == 'oui')

		else {
			$titre_html = propre($titre);
			$message_html = propre($message);
			$titre_texte = spiplistes_courrier_version_texte($titre_html) . $eol;
			$message_texte = spiplistes_courrier_version_texte($message_html) . $eol;
		}
		
		if($avec_sommaire == 'oui') {

			if($id_rubrique > 0) {

				$sql_where = array('id_rubrique='.sql_quote($id_rubrique)
					, "statut=".sql_quote('publie'));
				
				if($date_sommaire == 'oui') {
					$sql_where[] = "date >= " . sql_quote($date);
				}
				if($sql_result = sql_select("titre,id_article"
					, "spip_articles"
					, $sql_where
					)) {
					while($row = sql_fetch($sql_result)) {
						$url =generer_url_entite($row['id_article'], 'article');
						$ii = typo($row['titre']);
						$sommaire_html .= "<li> <a href='" . $url . "'>" . $ii . '</a></li>'.$eol;
						$sommaire_texte .= " - " . textebrut($ii) . "\n   " . $url . $eol;
					}
				}
			}
		
			if($id_mot > 0) {
				if($sql_result = sql_select("a.titre,a.id_article"
					, "spip_articles AS a LEFT JOIN spip_mots_articles AS m ON a.id_article=m.id_article"
					, array(
						"a.statut=".sql_quote('publie')
						, "m.id_mot=".sql_quote($id_mot)
						, "a.date >= " . sql_quote($sql_date)
						)
					)) {
					while($row = sql_fetch($sql_result)) {
						$ii = typo($row['titre']);
						$url = generer_url_entite($row['id_article'], 'article');
						$sommaire_html .= "<li> <a href='" . $url . "'> " . $ii . "</a></li>\n";
						$sommaire_texte .= " - " . textebrut($ii) . "\n   " . $url . $eol;
					}
				}
			}
			
			if(!empty($sommaire_html)) {
				$sommaire_html = "<ul>" . $sommaire_html . "</ul>\n";
				$message_html = 
					($patron_pos == "avant")
					? $message_html . $sommaire_html
					: $sommaire_html . $message_html
					;
				$message_texte = 
					($patron_pos == "avant")
					? $message_texte . $eol . $sommaire_texte
					: $sommaire_texte . $eol . $message_texte
					;
			}
		
		} // end if($avec_sommaire == 'oui')


		$form_action = ($id_courrier) 
			? generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER, 'id_courrier='.$id_courrier)
			: generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER)
			;
		
		$message_html = spiplistes_liens_absolus ($intro_html . $message_html);
		$message_texte = spiplistes_liens_absolus ($intro_texte . $message_texte);
		
		spiplistes_debug_log('ACTION: generate page');

		/**
		 * Le pied fait partie du courrier
		 */
		$message_html .= $pied_html;
		$message_texte .= $pied_texte;
		
		$page_result = $eol.$eol
			// boite courrier au format html
			. debut_cadre_couleur('', true).$eol
			. '<form id="choppe_patron-1" action="'.$form_action.'"
					method="post" name="choppe_patron-1">'.$eol
			. '<div id="previsu-html" class="switch-previsu">'.$eol
			. _T('spiplistes:version_html') 
				. ' / ' . '<a href="javascript:jQuery(this).switch_previsu()">' 
				. _T('spiplistes:version_texte') . '</a>'.$eol
			. '<div class="previsu-content">'.$eol
			. $message_html
			. $tampon_html
			. '</div>'.$eol
			. '</div>'.$eol // fin id='previsu-html
			. '<div id="previsu-texte" class="switch-previsu" style="display:none;">'.$eol
			. '<a href="javascript:jQuery(this).switch_previsu()">'
				. _T('spiplistes:version_html') . '</a>'.$eol
				. ' / ' 
				. _T('spiplistes:version_texte') 
			. '<div class="previsu-content">'.$eol
			. '<pre>'
			. $message_texte
			. $tampon_texte
			. '</pre>'.$eol
			. '</div>'.$eol
			. '</div>'.$eol // fin id='previsu-texte
			. '<p style="text-align:right;margin-bottom:0;">'.$eol
			. '<input type="hidden" name="modifier_message" value="oui" />'.$eol
			.	(
					($id_courrier)
					?	"<input type='hidden' name='id_courrier' value='$id_courrier' />\n"
					:	"<input type='hidden' name='new' value='oui' />\n"
				)
			. '<input type="hidden" name="titre" value="'.htmlspecialchars($titre).'">'.$eol
			. '<input type="hidden" name="message" value="'.htmlspecialchars($message_html).'">'.$eol
			. '<input type="hidden" name="message_texte" value="'.htmlspecialchars($message_texte).'">'.$eol
			. '<input type="hidden" name="date" value="'.$date.'">'.$eol
			. '<input type="submit" name="btn_courrier_valider"
				value="'._T('bouton_valider').'" class="fondo" /></p>'.$eol
			. '</form>'.$eol
			. fin_cadre_couleur(true)
			. '<br />'.$eol
			;
		echo($page_result);
	}
	exit(0);
}	

// http://doc.spip.org/@format_mysql_date
function format_mysql_date($annee=0, $mois=0, $jour=0, $h=0, $m=0, $s=0) {
	$annee = sprintf("%04s",$annee);
	$mois = sprintf("%02s",$mois);

	if ($annee == "0000") $mois = 0;
	if ($mois == "00") $jour = 0;

	return sprintf("%04u",$annee) . '-' . sprintf("%02u",$mois) . '-'
		. sprintf("%02u",$jour) . ' ' . sprintf("%02u",$h) . ':'
		. sprintf("%02u",$m) . ':' . sprintf("%02u",$s);
}


/******************************************************************************************/
/* SPIP-listes est un systeme de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU  */
/* pour plus de details.                                                                  */
/*                                                                                        */
/* Vous devez avoir recu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
