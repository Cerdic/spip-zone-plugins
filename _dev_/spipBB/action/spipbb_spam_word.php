<?php
#----------------------------------------------------------#
#  Plugin  : spipbb - Licence : GPL                        #
#  File    : action/spipbb_spam_word - modifie un mot spam #
#  Authors : chryjs, 2007                                  #
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

// * [fr] Acces restreint, plugin pour SPIP * //
// * [en] Restricted access, SPIP plugin * //

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/spipbb');

// ------------------------------------------------------------------------------
// [fr] Verification et declenchement de l'operation
// ------------------------------------------------------------------------------
function action_spipbb_spam_word()
{
	global $spip_lang_left, $spipbb_fromphpbb, $dir_lang;
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	list($id_spam_word, $statut) = preg_split('/\W/', $arg);

	$id_spam_word = intval($id_spam_word);
	$id_rubrique = intval(_request('id_rubrique'));

	$redirige = urldecode(_request('redirect'));
	if (!empty($id_rubrique)) $redirige = parametre_url($redirige, 'id_rubrique', $id_rubrique, '&') ;

	switch ($statut) {
	case 'del' :
		$result = sql_select("*", "spip_spam_words", "id_spam_word=$id_spam_word");
		if (!($row = sql_fetch($result)))
			return;
		sql_delete('spip_spam_words',"id_spam_word=$id_spam_word");
		break;
//	case 'edit' :
//		break;
	case 'insert' :
		// [fr] Import a partir de la zone textarea
		$list_sw = trim(corriger_caracteres(_request('sw_mass_add')));
		if ($list_sw) {
			$list = preg_split("#[^A-Za-z-]#", $list_sw );
			for ($i = 0; $i < count($list); $i++) {
				$word = trim($list[$i]);
				if (empty($word)) { continue; }
				spipbb_ajoute_spam_word($word);
			}
		}
		// [fr] Import a partir d'une URL
		if ( $list_url = trim(_request('sw_url_add')) ) {
			include_spip("inc/distant");
			$list_csv_raw = recuperer_page($list_url,true);
			$list_csv = preg_split("/\r\n|\n\r|\n|\r|\s| |\t/",$list_csv_raw);
			reset($list_csv);
			while (list($key,$val)=each($list_csv)) {
				$val=preg_replace("/[\t| |\s]#.*/","",$val); // remove comments
				$val=preg_replace("/^#.*$/","",$val); // remove comments line
				if (!empty($val)) {
					if ($val) {
						spipbb_ajoute_spam_word($val);
					}
				}
			} // while
		}
		break;
	}

	redirige_par_entete($redirige);
} // action_spipbb_spam_word

// ------------------------------------------------------------------------------
// [fr] Insere avec controle sur sa pre-existence un mot de spam
// [en] Insert, if new, spam_word
// ------------------------------------------------------------------------------
function spipbb_ajoute_spam_word($spam_word)
{
	//$spam_word = str_replace("\'", "''", $spam_word);
	$spam_word=addslashes($spam_word);
	$row=sql_fetsel('id_spam_word','spip_spam_words',"spam_word='$spam_word'");
	if (!$row)
		@sql_insertq('spip_spam_words',array( 'spam_word'=>$spam_word ) );
} // spipbb_ajoute_spam_word

?>