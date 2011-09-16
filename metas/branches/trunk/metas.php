<?php
function metas_formulaire_affiche ($ElementGestionMetas, $IdElementGestionMetas)
{
	include_spip("inc/presentation");

	$GestionMetasTable = 'spip_metas';

	if ($ElementGestionMetas == '') return;

	// On recupere les informations en base des metas.
	$select = array('m.id_meta','titre','description','keywords');
	$from = array('spip_metas m','spip_metas_liens ml');
	$where = array('m.id_meta = ml.id_meta','id_objet = '.$IdElementGestionMetas,'objet = "'.$ElementGestionMetas.'"');
	$result = sql_fetsel($select, $from, $where);
	$metas['id_meta'] = $result['id_meta'];
	if ($result)
		$result['descriptif'] = $result['description']; // pas super bô mais descriptif ne convenait pas vraiment pour le meta description...

	if (!$result) {
		$result = array();
		$result['titre'] = '';
		$result['descriptif'] = '';
		$result['id_meta'] = '';
		$result['keywords'] = '';
	}

	// Si le formulaire n'a pas ete soumis, on prend les informations de la base. Sinon on prend met a jour icelle.
	if (!_request('GestionMetasSubmit')) {
		$metas['titre'] = $result['titre'];
		$metas['description'] = $result['descriptif'];
		$metas['keywords'] = $result['keywords'];
	} else {
		$metas['titre'] = _request('GestionMetasTitre');
		$metas['description'] = _request('GestionMetasDescription');
		$metas['keywords'] = _request('GestionMetasKeywords');

		if ($metas['id_meta']){
			// On est dans un update des données
			sql_updateq('spip_metas', array('titre' => $metas['titre'],'description' => $metas['description'],'keywords' => $metas['keywords']),'id_meta = '.$metas['id_meta']);
		} else {
			// Nouvelle entrée dans la base méta et dans la table lien
			$metas['id_meta'] = sql_insertq('spip_metas', array('titre' => $metas['titre'],'description' => $metas['description'],'keywords' => $metas['keywords']));
			sql_insertq('spip_metas_liens', array('id_meta' => $metas['id_meta'],'id_objet' => $IdElementGestionMetas,'objet' => $ElementGestionMetas));
		}
	}

	$bouton = bouton_block_depliable(_T('metas:config_metas_page'),false,"metas_form");
	$retour = debut_cadre_enfonce(_DIR_PLUGIN_METAS.'/images/metas-24.png',true,'',$bouton);

	if ($metas['id_meta'])
	{
		$retour .= '
		<div class="cadre cadre-liste"><table width="100%" cellpadding="2" cellspacing="0" border="0">
			'.(($metas['titre']) ? '<tr class="tr_liste">
				<td><span><strong>'._T('metas:title').'</strong></span></td>
				<td class="arial2">'.$metas['titre'].'</td>
			</tr>' : '')
			.(($metas['description']) ? '<tr class="tr_liste">
				<td><span><strong>'._T('metas:meta_description').'</strong></span></td>
				<td class="arial2">'.$metas['description'].'</td>
			</tr>' : '')
			.(($metas['keywords']) ? '<tr class="tr_liste">
				<td><span><strong>'._T('metas:meta_keywords').'</strong></span></td>
				<td class="arial2">'.$metas['keywords'].'</td>
			</tr>' : '').'
		</table></div>';
	}
	if (autoriser('editermetas',$ElementGestionMetas,$IdElementGestionMetas))
		$retour .= '
		<div id="metas_form" style="display:none;">
			<form onsubmit="vars=$(\'#metas_donnes\').serialize();$(\'#pave_metas\').load(\'?exec=metas_interface&id_objet='.$IdElementGestionMetas.'&objet='.$ElementGestionMetas.'&\'+vars);return false;" action="index.php" method="get" id="metas_donnes">
				<input type="hidden" name="GestionMetasSubmit" value="1" />
				<p>
					<label for="GestionMetas_title"><b>'._T('metas:title').'</b></label>
					<br /><span class="explication" style="color:#444;">'._T('metas:title_explication').'</span><br />
					<input id="GestionMetas_title" type="text" name="GestionMetasTitre" value="'.htmlspecialchars($metas['titre'], ENT_QUOTES).'" class="fondl" style="width: 98%" />
				</p>
				<p>
					<label for="GestionMetas_description"><b>'._T('metas:meta_description').'</b></label>
					<br /><span class="explication" style="color:#444;">'._T('metas:meta_description_explication').'</span><br />
					<textarea id="GestionMetas_description" name="GestionMetasDescription" cols=\"40\" rows="4" class="fondl" style="width: 98%">'.htmlspecialchars($metas['description'], ENT_QUOTES).'</textarea>
				</p>
				<p>
					<label for="GestionMetas_keywords"><b>'._T('metas:meta_keywords').'</b></label>
					<br /><span class="explication" style="color:#444;">'._T('metas:meta_keywords_explication').'</span><br />
					<textarea id="GestionMetas_keywords" name="GestionMetasKeywords" cols=\"40\" rows="4" class="fondl" style="width: 98%">'.htmlspecialchars($metas['keywords'], ENT_QUOTES).'</textarea>
				</p>
				<p style="text-align: right;"><input type="submit" name="valider" value="'._T('metas:valider').'" class="fondl" /></p>
			</form>
		</div>';

	$retour .= fin_cadre_enfonce(true);

	return $retour;
}

function metas_formulaire ($vars = "")
{
	$exec = $vars["args"]["exec"];
	if ($vars["args"]["id_rubrique"] and $exec=='naviguer') {
		$objet = 'rubrique';
		$id_objet = $vars["args"]["id_rubrique"];
	}
	if ($vars["args"]["id_article"] and $exec=='articles') {
		$objet = 'article';
		$id_objet = $vars["args"]["id_article"];
	}
	if ($vars["args"]["id_breve"] and $exec=='breves_voir') {
		$objet = 'breve';
		$id_objet = $vars["args"]["id_breve"];
	}
	$data =	$vars["data"];

	if ($id_objet > 0) {
		$ret .= "<div id='pave_metas'>";
		$ret .=  metas_formulaire_affiche($objet, $id_objet);
		$ret .= "</div>";
	}

	$data .= $ret;
	$vars["data"] = $data;
	return $vars;
}

// Permet de mettre en strong des mots "importants" définis (référencement)
function metas_mots_strong($flux)
{
	static $mots_recherche = null;
	// passons vite si rien a faire
	if (!strlen($GLOBALS['meta']['spip_metas_mots_importants'])) return $flux;

	if (is_null($mots_recherche)){
		$recup_cfg=explode(',',$GLOBALS['meta']['spip_metas_mots_importants']);
		if (empty($recup_cfg[0]))
			return $flux;
		foreach ($recup_cfg as $value)
		{
			$mots_recherche[]='/(^'.trim($value).'\b|\s'.trim($value).'\b)/im';
		}
	}
	if (count($mots_recherche)){
		$remplacer="<strong>$0</strong>";
		$flux = preg_replace($mots_recherche, $remplacer, $flux);
	}
	return $flux;
}

function balise_METAS_TITLE($p) {
	$p->code = "\$GLOBALS['meta']['spip_metas_title']";
	return $p;
}
function balise_METAS_DESCRIPTION($p) {
	$p->code = "\$GLOBALS['meta']['spip_metas_description']";
	return $p;
}
function balise_METAS_KEYWORDS($p) {
	$p->code = "\$GLOBALS['meta']['spip_metas_keywords']";
	return $p;
}
?>