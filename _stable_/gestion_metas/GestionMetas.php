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
	}
	return '
	<form id="metas" method="post">
		<fieldset>
			<legend>Metadatas</legend>
			<input type="hidden" name="GestionMetasSubmit" value="1" />
			<input type="hidden" name="exec" value="'._request('exec').'" />
			<input type="hidden" name="id_rubrique" value="'._request('id_rubrique').'" />
			<input type="hidden" name="id_article" value="'._request('id_article').'" />
			<input type="hidden" name="id_breve" value="'._request('id_breve').'" />
			<p><label for="GestionMetas_title">Title</label><br />
			<input id="GestionMetas_title" type="text" name="GestionMetasTitre" value="'.htmlspecialchars($metas['titre'], ENT_QUOTES).'" style="width: 98%"/></p>
			<p><label for="GestionMetas_description">Description</label><br />
			<input id="GestionMetas_description" type="text" name="GestionMetasDescription" value="'.htmlspecialchars($metas['description'], ENT_QUOTES).'" style="width: 98%"/></p>
			<p><label for="GestionMetas_keywords">Keywords</label><br />
			<input id="GestionMetas_keywords" type="text" name="GestionMetasKeywords" value="'.htmlspecialchars($metas['keywords'], ENT_QUOTES).'" style="width: 98%"/></p>
			<p><input type="submit" /></p>
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

?>