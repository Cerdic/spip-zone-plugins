<?php
/**
* Plugin SPIP-Immo
*
* @author: CALV V3
* @author: Pierre KUHN V4
*
* Copyright (c) 2007-12
* Logiciel distribue sous licence GPL.
*
**/

	/*===========================================================================================================================================================
	Affichage numéro de dossier
	===========================================================================================================================================================*/
	function afficher_ndossier($nDossier)
	{
		if(strlen($nDossier)<_SPIPIMMO_DOSSIER_NBCAR)
		{
			for($j=0; $j<(_SPIPIMMO_DOSSIER_NBCAR-strlen($nDossier)); $j++)
			{
				$nZero.="0";
			}
		}
		return($nZero . $nDossier);
	}


	/*===========================================================================================================================================================
	Fonction pour les listes déroulantes (nombre de piece, chambres,...)
	===========================================================================================================================================================*/
	function liste_deroulante_piece($nbPieceActuel, $nbPieceTotal)
	{

		$out.='<option value=""></option>';
		for($i=0; $i<=$nbPieceTotal; $i++)
		{
			if(empty($nbPieceActuel)==false and $nbPieceActuel==$i)
			{
				$out.='<option selected="selected" value="' . $i . '">' . $i . '</option>';
			}
			else
			{
				$out.='<option value="' . $i . '">' . $i . '</option>';
			}
		}
		return($out);
	}


	/*===========================================================================================================================================================
	Fonction pour redimensionner les images
	============================================================================================================================================================*/
	function redimage($img_src, $img_dest, $dst_w, $dst_h, $alt=null, $js=0)
	{
		//Déclaration des variables
		global $img;

		//Récupération de l'extension
		$ext=substr($img_src, -3);
		$ext=strtolower($ext);

		$out=$ext;

		if (file_exists($img_src) and (($ext=="peg") || ($ext=="jpg") || ($ext=="gif") || ($ext=="png")))
		{
			// Lit les dimensions de l'image
			$size = GetImageSize($img_src);
			$src_w = $size[0];
			$src_h = $size[1];

			// Teste les dimensions tenant dans la zone
			$test_h = round(($dst_w / $src_w) * $src_h);
			$test_w = round(($dst_h / $src_h) * $src_w);

			// Si Height final non précisé (0)
			if(!$dst_h)
			{
				$dst_h = $test_h;
			}

			// Sinon si Width final non précisé (0)
			else if(!$dst_w)
			{
				$dst_w = $test_w;
			}

			// Sinon teste quel redimensionnement tient dans la zone
			else if($test_h>$dst_h)
			{
				$dst_w = $test_w;
			}
			else
			{
				$dst_h = $test_h;
			}

			//On teste si l'image destinataire éxiste déjà
			if(!file_exists($img_dest))
			{
				// Crée une image vierge aux bonnes dimensions
				$dst_im = ImageCreateTrueColor($dst_w,$dst_h);

				// Copie dedans l'image initiale redimensionnée
				switch($ext)
				{
					case "jpg" :
					case "peg" :
						$src_im = ImageCreateFromJpeg($img_src);
						ImageCopyResampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
						ImageJpeg($dst_im,$img_dest);
						break;

					case "png" :
						$src_im = ImageCreateFromPng($img_src);
						ImageCopyResampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
						ImagePng($dst_im,$img_dest);
						break;

					case "gif" :
						$src_im = ImageCreateFromGif($img_src);
						ImageCopyResampled($dst_im,$src_im,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
						ImageGif($dst_im,$img_dest);
						break;

					default :
						$img_dest="../prive/images/logo_spip.jpg";

				}

				//Destruction des tampons
				ImageDestroy($src_im);
			}

		}
		else
		{
			$img_dest = "../prive/images/logo_spip.jpg";
			$widthFinale=$dst_w;
			$heigthFinale=$dst_h;
		}

		//javascript pour ouvrir l'image dans une nouvelle fenêtre
		$jsScript='onclick="window.open(\'' . $img_src . '\', \'image\');" style="cursor:pointer;"';

		//on met le javascript , oui ou non?
		if($js==1)
		{
			$out='<img src="' . $img_dest . '" ' . $widthFinale . $heigthFinale . ' alt="' . $alt . '" ' . $jsScript . ' />';
		}
		else
		{
			$out='<img src="' . $img_dest . '" ' . $widthFinale . $heigthFinale . ' alt="' . $alt . '" />';
		}
		return $out;
	}


	/*===========================================================================================================================================================
	Fonction pour l'url rewriting des annonces
	============================================================================================================================================================*/
	function lien_annonce_propre($texte)
	{
		$texte=substr($texte, 9);
		$tabLien=split("&", $texte);
		$id_annonce=substr($tabLien[1], 11);
		$type=substr($tabLien[2], 5);
		$ville=substr($tabLien[3], 6);
		return $id_annonce . "-" . $type . "-" . $ville;
	}

?>
