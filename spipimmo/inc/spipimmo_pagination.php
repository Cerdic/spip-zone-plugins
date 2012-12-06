<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	function spipimmo_pagination()
	{
		//Liste des annonces
		$resListeAnnonces=sql_select("*", "spip_annonces", "", "","`id_annonce` DESC");
		$nbAnnonces=sql_count($resListeAnnonces);

		//Nombre de page
		$nbPageTotal=ceil($nbAnnonces/_SPIPIMMO_PAGE_NBRES);

		if(isset($_GET["pg"])==false)
		{
			$nPage=1;
		}
		else
		{
			$nPage=$_GET["pg"];
		}

		if($nPage==1)
		{
			$nDebut=0;
		}
		else
		{
			$nDebut=(_SPIPIMMO_PAGE_NBRES*$nPage)-_SPIPIMMO_PAGE_NBRES;
		}

		$suiteLien="";
		if(isset($_GET["tri"]))
		{
			$tabTriLibelle["dossier"]="id_annonce";
			$tabTriLibelle["ville"]="ville_bien";
			$tabTriLibelle["type"]="type_offre";
			$tabTriLibelle["prix"]="prix_loyer";

			$tabTri=split("-", $_GET["tri"]);
			$order.=$tabTriLibelle[$tabTri[0]] . " " . strtoupper($tabTri[1]);
			$suiteLien="&tri=" . $_GET["tri"];
		}

		$limit.=$nDebut . ",10";
		$out='<table id="pagination">';
			$out.='<tr>
					<td id="pgpagination">
						' . $nPage . ' / ' . $nbPageTotal . '
					</td>
					<td id="detpagination">';

			if($nbPageTotal<=_SPIPIMMO_PAGE_NBRES)
			{
				if($nPage!=1)
				{
					$out.='<a href="?exec=spipimmo&amp;pg=' . ($nPage-1) . $suiteLien . '"> < </a> - ';
				}
				for($i=1; $i<=$nbPageTotal; $i++)
				{
					if($i==$nbPageTotal)
					{
						$out.='<a href="?exec=spipimmo&amp;pg=' . $i . $suiteLien . '">' . $i . '</a>';
					}
					else
					{
						$out.='<a href="?exec=spipimmo&amp;pg=' . $i . $suiteLien . '">' . $i . '</a> - ';
					}
				}
				if($nPage!=$nbPageTotal)
				{
					$out.=' - <a href="?exec=spipimmo&amp;pg=' . ($nPage+1) . $suiteLien . '"> > </a>';
				}
			}
			else
			{
				$nPageAvt=$nPage-5;
				$nPageAps=$nPage+5;

				if(($nPageAvt>0) and ($nPageAps<$nbPageTotal))
				{
					$out.='<a href="?exec=spipimmo&amp;pg=' . ($nPage-1) . $suiteLien . '"> < </a>';
					for($i=$nPageAvt; $i<$nPageAps; $i++)
					{
						$out.=' - <a href="?exec=spipimmo&amp;pg=' . $i . $suiteLien . '"> ' . $i . '</a>';
					}
					$out.=' - <a href="?exec=spipimmo&amp;pg=' . ($nPage+1) . $suiteLien . '"> > </a>';
				}
				else if($nPageAvt<=0)
				{
					if($nPage>1)
					{
						$out.='<a href="?exec=spipimmo&amp;pg=' . ($nPage-1) . $suiteLien . '"> < </a> - ';
					}
					for($i=1; $i<=_SPIPIMMO_PAGE_NBRES; $i++)
					{
						$out.='<a href="?exec=spipimmo&amp;pg=' . $i . $suiteLien . '"> ' . $i . '</a> - ';
					}
					$out.='<a href="?exec=spipimmo&amp;pg=' . ($nPage+1) . $suiteLien . '"> > </a>';
				}
				else if($nPageAps>=$nbPageTotal)
				{
					$out.='<a href="?exec=spipimmo&amp;pg=' . ($nPage-1) . $suiteLien . '"> < </a>';
					for($i=($nbPageTotal-_SPIPIMMO_PAGE_NBRES); $i<=$nbPageTotal; $i++)
					{
						$out.=' - <a href="?exec=spipimmo&amp;pg=' . $i . $suiteLien . '"> ' . $i . '</a>';
					}
					if($nPage<$nbPageTotal)
					{
						$out.=' - <a href="?exec=spipimmo&amp;pg=' . ($nPage+1) . $suiteLien . '"> > </a>';
					}
				}
			}
			$out.='</td>
				<td id="precpagination">
					<form id="formpage" action="javascript:accesPage(document.forms[\'formpage\'].elements[\'acpage\'].value, ' . $nbPageTotal . ', \'' . $suiteLien . '\');" method="post">
						Page : <input class="acpage" type="text" name="acpage" id="acpage" />
					</form>
				</td>
			</tr>
		</table>';

		return array($out, $order, $limit, $nPage, $nbAnnonces);
	}
?>
