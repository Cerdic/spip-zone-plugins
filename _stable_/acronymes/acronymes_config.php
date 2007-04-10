<?php
function acronymes_config($flux){
	if (version_compare($GLOBALS['spip_version_code'],'1.92','<=')){
		if ($flux['args']['exec']=='sites'){
			global $spip_lang_right;
			$out = "";
			$id_syndic = $flux['args']['id_syndic'];
			$id_rubrique = _request('rubrique');
			if (($id=_request('acronymes_id_syndic'))!=NULL){
				ecrire_meta('acronymes_id_syndic',$id);
				ecrire_metas();
			}
			$acronymes_id_syndic = $GLOBALS['meta']['acronymes_id_syndic']?$GLOBALS['meta']['acronymes_id_syndic']:0;
			$out .= debut_cadre_relief('',true);
			if ($id_syndic!=$acronymes_id_syndic){
				$out .= generer_url_post_ecrire('sites', "id_syndic=$id_syndic".($id_rubrique?"&id_parent=$id_rubrique":""));
				$out .= "<input type='hidden' name='acronymes_id_syndic' value='$id_syndic' />\n";
				$out .= "<div>"._L("Choisir ce site comme base des acronymes")."\n";
				$out .= "<div align='$spip_lang_right'><input type='submit' name='Choisir' value='"._T('bouton_choisir')."' class='fondo'></div>\n";
				$out .= "</div></form>";
			}
			else{
				$out .= generer_url_post_ecrire('sites', "id_syndic=$id_syndic".($id_rubrique?"&id_parent=$id_rubrique":""));
				$out .= "<input type='hidden' name='acronymes_id_syndic' value='0' />\n";
				$out .= "<div>"._L("Ce site a &eacute;t&eacute; choisi comme base des acronymes")."<br/>\n";
				$out .= "<div align='$spip_lang_right'><input type='submit' name='Annuler' value='"._L('Annuler')."' class='fondo'></div>\n";
				$out .= "</div></form>";
			}
			$out .= fin_cadre_relief(true);
			$flux['data'].= $out;
		}
		if ($flux['args']['exec']=='naviguer'){
			global $spip_lang_right;
			$out = "";
			$id_rubrique = $flux['args']['id_rubrique'];
			if ($id_rubrique){
				if (($active=_request('acronymes_rubrique_locale_active'))!=NULL){
					ecrire_meta('acronymes_rubrique_locale_active',$active);
					ecrire_metas();
				}
				$acronymes_rubrique_locale_active = $GLOBALS['meta']['acronymes_rubrique_locale_active']?$GLOBALS['meta']['acronymes_rubrique_locale_active']:'non';
				$out .= debut_cadre_relief('',true);
				if ($acronymes_rubrique_locale_active!=$id_rubrique){
					$out .= generer_url_post_ecrire('naviguer', "id_rubrique=$id_rubrique");
					$out .= "<input type='hidden' name='acronymes_rubrique_locale_active' value='$id_rubrique' />\n";
					$out .= "<div>"._L("Utiliser &eacute;galement la rubrique courante comme base locale des acronymes")."\n";
					$out .= "<div align='$spip_lang_right'><input type='submit' name='Choisir' value='"._T('bouton_choisir')."' class='fondo'></div>\n";
					$out .= "</div></form>";
				}
				else{
					$out .= generer_url_post_ecrire('naviguer', "id_rubrique=$id_rubrique");
					$out .= "<input type='hidden' name='acronymes_rubrique_locale_active' value='non' />\n";
					$out .= "<div>"._L("La rubrique courante est utilis&eacute; comme base locale des acronymes")."<br/>\n";
					$out .= "<div align='$spip_lang_right'><input type='submit' name='Annuler' value='"._L('Annuler')."' class='fondo'></div>\n";
					$out .= "</div></form>";
				}
				$out .= fin_cadre_relief(true);
				$flux['data'].= $out;
			}
		}
	}
	return $flux;
}

?>