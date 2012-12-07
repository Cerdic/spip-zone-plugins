<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V3
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function action_spipimmo_ajouter_document()
	{
		//r�cup�ration des arguments
		$arg=explode('-',_request('arg'));
		$idAnnonce=$arg[0];
		$page=$arg[1];

		//on cherche la bonne page � renvoyer
		switch($page)
		{
			case "ajouter":
				$redir="ajouter_document";
				break;
			case "modifier":
				$redir="modifier_annonce";
				break;
		}

		//on parcourt le r�pertoire image pour attribuer le bon numero de document
		$handle=opendir(_DIR_IMG);
		$tabNDoc=array();
		$tabNDoc[0]=0;
		while ($fichier = readdir($handle))
		{
			if(ereg("^immo" . $idAnnonce . "-[0-9]*.[a-zA-Z]*$", $fichier))
			{
				$splitTiret=split("-", $fichier);
				$splitPoint=split("\.", $splitTiret[1]);
				array_push($tabNDoc, $splitPoint[0]);
			}
		}
		closedir($handle);
		$nDoc=max($tabNDoc)+1;

		//on r�cup�re les infos sur le fichier
		$tailleFichier=$_FILES['fichier']['size'];
		$repertoireTemp=$_FILES['fichier']['tmp_name'];
		$tabFichier=split('\.', $_FILES['fichier']['name']);
		$ext=$tabFichier[1];

		//on emploi la bonne extension gr�ce � une table d�j� existante de spip
		$resSelectExt=sql_select("extension", "spip_types_documents", "extension LIKE '" . $ext . "'");
		$nbEnr=sql_count($resSelectExt);

		//si l'extension n'est pas support�... adieu le document :(
		if ($nbEnr!=1)
		{
			redirige_par_entete(_DIR_RACINE . _DIR_RESTREINT_ABS . '?exec=' . $redir . '&id=' .  $idAnnonce . '&charg=2');
		}
		else
		{
			$ligneEnr=sql_fetch($resSelectExt);

			//envoi du fichier dans le bon r�pertoire
			$move=move_uploaded_file($repertoireTemp, _DIR_IMG . "immo" . $idAnnonce . "-" . $nDoc . "." . $ligneEnr["extension"]);
			if($move)
			{
				if($ligneEnr["extension"]=="jpg" or $ligneEnr["extension"]=="png" or $ligneEnr["extension"]=="gif")
				{
					$typeFichier=1;
				}
				else
				{
					$typeFichier=0;
				}

				//si tout se passe bien, on rentre le document dans la base de donn�e
				$resInsertionDocument=sql_insertq("spip_documents_annonces",
					array
					(
						"numero_dossier"=>$idAnnonce,
						"fichier"=>_NOM_PERMANENTS_ACCESSIBLES . "immo" . $idAnnonce . "-" . $nDoc . "." . $ligneEnr["extension"],
						"taille"=>$tailleFichier,
						"type"=>$typeFichier
					));

				if($resInsertionDocument)
				{
					redirige_par_entete(_DIR_RACINE . _DIR_RESTREINT_ABS . '?exec=' . $redir . '&id=' .  $idAnnonce . '&charg=1');
				}
				else
				{
					redirige_par_entete(_DIR_RACINE . _DIR_RESTREINT_ABS . '?exec=' . $redir . '&id=' .  $idAnnonce . '&charg=4');
				}
			}
			else
			{
				redirige_par_entete(_DIR_RACINE . _DIR_RESTREINT_ABS . '?exec=' . $redir . '&id=' .  $idAnnonce . '&charg=3');;
			}
		}
	}
?>
