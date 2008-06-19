<?php

/******************************************************************************************/
/* GestionMetas est un outils de gestion des métadonnee title, description et keywords    */
/* pour SPIP, Copyright 2006 novactive - http://www.novactive.fr/                         */
/* Il a ete developpe par Olivier G. <o.gendrin@novactive.com>                            */
/*    pour le compte de Novactive                                                         */
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
/* en meme temps que ce programme ; si ce n'est pas le cas, zcrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/

include_spip('public/interfaces');
global $table_des_traitements;
$table_des_traitements["TEXTE"][0] = 'propre(GestionMetas_mots_strong(%s))';

function GestionMetas_affiche_gauche($flux) {
	if ((_request('exec') != 'naviguer') && (_request('exec') != 'articles') && (_request('exec') != 'breves_voir')) return;
	$flux['data'] .= formulaire_metas();
	return $flux;
}

function formulaire_metas() {

	$GestionMetasTable = $GLOBALS['table_prefix'].'_gestion_metas';
	switch (_request('exec')) {
		case 'naviguer':
			$ElementGestionMetas = 'rubrique';
			$IdElementGestionMetas = _request('id_rubrique');
			break;
		case 'articles':
			$ElementGestionMetas = 'article';
			$IdElementGestionMetas = _request('id_article');
			break;
		case 'breves_voir':
			$ElementGestionMetas = 'breve';
			$IdElementGestionMetas = _request('id_breve');
			break;
	}
	if ($ElementGestionMetas == '') return;

	//On teste l'existence de la table idoine et on la créé si elle n'existe pas.
	$query = 'SELECT * FROM '.$GestionMetasTable.' LIMIT 1';
	$res = spip_query($query);
	if ($res == '') create_GestionMetasTable($GestionMetasTable);

	// On rempli le tableau 'metas' (qui pourrait etre un objet)
	$metas = Array();
	$metas['titre'] = _request('GestionMetasTitre');
	$metas['description'] = _request('GestionMetasDescription');
	$metas['keywords'] = _request('GestionMetasKeywords');
	$metas['url'] = _request('GestionMetasUrl');

	// On recupere les informations en base, et on les utilise si l'element de metas correspondant est vide.
	$query = "SELECT * FROM ".$GestionMetasTable." WHERE id_".$ElementGestionMetas." = '".$IdElementGestionMetas."'";
	$res = spip_query($query);
	$result = spip_fetch_array($res);
	$metas['id_meta'] = $result['id_meta'];
	// Si le formulaire n'a pas ete soumis, on prend les informations de la base. Sinon on prend met a jour icelle.
	if (!_request('GestionMetasSubmit')) {
		$metas['titre'] = $result['titre'];
		$metas['description'] = $result['description'];
		$metas['keywords'] = $result['keywords'];
		
		/* Gestion de l'url propre dans spip article */
		$query = "SELECT url_propre FROM spip_".$ElementGestionMetas."s WHERE id_".$ElementGestionMetas." = '".$IdElementGestionMetas."'";
		$res = spip_query($query);
		$result = spip_fetch_array($res);
		$metas['url'] = $result['url_propre'];
		
	} else {
		if ($metas['id_meta'] != '') $query = "UPDATE ".$GestionMetasTable;
		else $query = "INSERT INTO ".$GestionMetasTable;
		$query .= " SET
					id_".$ElementGestionMetas." = '".$IdElementGestionMetas."',
					titre = ".spip_abstract_quote($metas['titre']).",
					description = ".spip_abstract_quote($metas['description']).",
					keywords = ".spip_abstract_quote($metas['keywords']);
		if ($metas['id_meta'] != '') $query .= " WHERE id_meta = ".$metas['id_meta'];
		//echo $query;
		$res = spip_query($query);
		
		/* Gestion de l'url propre dans spip article */
		/* Mais on est un peu mefiant quand au contenu de l'url, on va donc retirer tout ce qui ne doit pas etre la */
		$metas['url']=preg_replace('`([^0-9a-zA-Z_\-]+)`iS', '', $metas['url']);
		$query = "UPDATE spip_".$ElementGestionMetas."s";
		$query .= " SET	url_propre = '".$metas['url']."'";
		$query .= " WHERE id_".$ElementGestionMetas." = '".$IdElementGestionMetas."'";
		$res = spip_query($query);
		
	}




	return '
	<form id="metas" method="post">
		<fieldset>
			<legend>Metadatas <br/> ( Référencement )</legend>
			<input type="hidden" name="GestionMetasSubmit" value="1" />
			<input type="hidden" name="exec" value="'._request('exec').'" />
			<input type="hidden" name="id_rubrique" value="'._request('id_rubrique').'" />
			<input type="hidden" name="id_article" value="'._request('id_article').'" />
			<input type="hidden" name="id_breve" value="'._request('id_breve').'" />
			<p><label for="GestionMetas_title">Title</label><br />
			<input id="GestionMetas_title" type="text" name="GestionMetasTitre" value="'.htmlspecialchars($metas['titre'], ENT_QUOTES).'" style="width: 98%"/></p>
			<p><label for="GestionMetas_description">Description</label><br />
			<textarea id="GestionMetas_description" name="GestionMetasDescription" style="width: 98%" rows="7">'.htmlspecialchars($metas['description'], ENT_QUOTES).'</textarea></p>
			<p><label for="GestionMetas_keywords">Keywords</label><br />
			<textarea id="GestionMetas_keywords" type="text" name="GestionMetasKeywords" style="width: 98%" rows="7" >'.htmlspecialchars($metas['keywords'], ENT_QUOTES).'</textarea></p>
			<p><label for="GestionMetas_url">Url Propre</label><br />
			<input id="GestionMetas_url" type="text" name="GestionMetasUrl" value="'.$metas['url'].'" style="width: 98%"/></p>
			<p><input type="submit" name="valider" value="valider"/></p>
		</fieldset>
	</form>';
}

function create_GestionMetasTable($GestionMetasTable) {
	$createTableQuery ='CREATE TABLE '.$GestionMetasTable.'
						(id_meta INTEGER NOT NULL AUTO_INCREMENT,
						id_rubrique INTEGER DEFAULT NULL,
						id_article INTEGER DEFAULT NULL,
						id_breve INTEGER DEFAULT NULL,
						titre TEXT,
						description TEXT,
						keywords TEXT,
						PRIMARY KEY (id_meta)
						)';
	$resCreateTableQuery = spip_query($createTableQuery);
	if ($resCreateTableQuery != 1) {
		echo ("la table ".$GestionMetasTable." n'existe pas et sa cr&eacute;ation est impossible, le plugin GestionMetas ne peut pas fonctionner. Merci de cr&eacute;er la table manuellement ou de <a href=\"?exec=admin_plugin\">d&eacute;sactiver le plugin</a>.");
		spip_log('impossible de cr&eacute;er la table '.$GestionMetasTable, 'mysql');
		return;
	}
	echo ("la table ".$GestionMetasTable." n'existait pas et a &eacute;t&eacute; cr&eacute;&eacute;e");
	spip_log('Plugin Gestion Metas : '.$GestionMetasTable.' created', 'mysql');
}

function GestionMetas_mots_strong($flux)
{
	$recup_cfg=explode(',',lire_config('motsimportants/motsimportants'));
	if (empty($recup_cfg))
		return $flux;
	foreach ($recup_cfg as $value)
	{
		$mots_recherche[]='/\b'.trim($value).'\b/i';
	}
	$remplacer="<strong>$0</strong>";
	$flux = preg_replace($mots_recherche, $remplacer, $flux);
	return $flux;
}

function GestionMetas_ajouterOnglets($flux) {
  if($flux['args']=='configuration')
	$flux['data']['cfg_Gestion_Metas']= new Bouton(_DIR_PLUGIN_GESTIONMETAS."/tag.png",
												'Configurer Mots Importants',
												generer_url_ecrire('cfg','cfg=motsimportants'));
  return $flux;
}
?>