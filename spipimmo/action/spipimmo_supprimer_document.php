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

	function action_spipimmo_supprimer_document()
	{
		//récupération des arguments
		$arg=explode('-',_request('arg'));
		$idAnnonce=$arg[0];
		$page=$arg[1];

		//on cherche la bonne page à renvoyer
		switch($page)
		{
			case "ajouter":
				$redir="ajouter_document";
				break;
			case "modifier":
				$redir="modifier_annonce";
				break;
		}

		$handle=opendir(_DIR_IMG);
		while($fichier = readdir($handle))
		{
			if(ereg("^immo" . $idAnnonce . "-" . "[0-9]*.[a-zA-Z]*$", $fichier))
			{
				$tabFichier=split('\.', $fichier);
				if(isset($_POST["suppr_" . $tabFichier[0]])==true)
				{
					if(file_exists(_DIR_IMG . $fichier))
					{
						$suppressionSource=unlink(_DIR_IMG . $fichier);
					}

					if(file_exists(_SPIPIMMO_REP_VIGNETTES . $fichier))
					{
						$suppressionVignette=unlink(_SPIPIMMO_REP_VIGNETTES . $fichier);
					}

					if($suppressionSource==true)
					{
						$resSupr=sql_delete("spip_documents_annonces", "`fichier` LIKE '%" . $fichier . "'");
					}
				}
			}
		}
		closedir($handle);

		redirige_par_entete(_DIR_RACINE . _DIR_RESTREINT_ABS . '?exec=' . $redir . '&id=' . $idAnnonce);

	}
?>
