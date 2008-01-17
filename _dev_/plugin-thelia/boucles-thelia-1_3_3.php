<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/
?>
<?php

	
	/* Gestion des boucles */
	include_once("classes/Rubrique.class.php");
	include_once("classes/Rubriquedesc.class.php");
	include_once("classes/Client.class.php");
	include_once("classes/Dossier.class.php");
	include_once("classes/Dossierdesc.class.php");
	include_once("classes/Contenu.class.php");
	include_once("classes/Contenudesc.class.php");
	include_once("classes/Produit.class.php");
	include_once("classes/Produitdesc.class.php");
	include_once("classes/Modules.class.php");
	include_once("classes/Adresse.class.php");
	include_once("classes/Commande.class.php");
	include_once("classes/Venteprod.class.php");
	include_once("classes/Statutdesc.class.php");
	include_once("classes/Image.class.php");
	include_once("classes/Imagedesc.class.php");
	include_once("classes/Document.class.php");
	include_once("classes/Documentdesc.class.php");
	include_once("classes/Accessoire.class.php");
	include_once("classes/Pays.class.php");
	include_once("classes/Paysdesc.class.php");
	include_once("classes/Zone.class.php");
	include_once("classes/Caracteristique.class.php");
	include_once("classes/Rubcaracteristique.class.php");
	include_once("classes/Caracval.class.php");
	include_once("classes/Caracdisp.class.php");
	include_once("classes/Devise.class.php");
	include_once("classes/Rubdeclinaison.class.php");
	include_once("classes/Declinaison.class.php");
	include_once("classes/Declinaisondesc.class.php");
	include_once("classes/Declidisp.class.php");
	include_once("classes/Declidispdesc.class.php");
	include_once("classes/Exdecprod.class.php");
	include_once("classes/Contenuassoc.class.php");
	include_once("classes/Stock.class.php");
	include_once("classes/Perso.class.php");

	include_once("fonctions/divers.php");
	include_once("lib/magpierss/rss_fetch.inc");
			
	/* Gestion des boucles de type Rubrique*/
	function boucleRubrique($texte, $args){
		global $id_rubrique;
		// r�cup�ration des arguments
		$id = lireTag($args, "id");
		$parent = lireTag($args, "parent");
		$courante = lireTag($args, "courante");
		$pasvide = lireTag($args, "pasvide");
		$ligne = lireTag($args, "ligne");
		$classement = lireTag($args, "classement");
		$aleatoire = lireTag($args, "aleatoire");
		$exclusion = lireTag($args, "exclusion");
		$deb = lireTag($args, "deb");
		$num = lireTag($args, "num");
		
		$res="";
		$search="";
		$limit="";
		
		if(!$deb) $deb=0;
		
		$rubrique = new Rubrique();
		$rubriquedesc = new Rubriquedesc();
		
		// preparation de la requete
		
		if($ligne == "") $ligne="1";
		
		$search.=" and $rubrique->table.ligne=\"$ligne\"";
		
		if($id!="")  $search.=" and $rubrique->table.id in ($id)";
		if($parent!="") $search.=" and $rubrique->table.parent=\"$parent\"";
		if($courante == "1") $search .=" and $rubrique->table.id='$id_rubrique'";
		else if($courante == "0") $search .=" and $rubrique->table.id!='$id_rubrique'";
		if($num!="") $limit .= " limit $deb,$num";
		if($exclusion!="") $search .= " and $rubrique->table.id not in($exclusion)";
		
		$search .= " and lang=" . $_SESSION['navig']->lang;
		
		if($aleatoire) $order = "order by "  . " RAND()";
		else if($classement == "alpha") $order = "order by $rubriquedesc->table.titre";
		else if($classement == "alphainv") $order = "order by $rubriquedesc->table.titre desc";
		else $order = "order by $rubrique->table.classement";

				
		$query = "select $rubrique->table.id from $rubrique->table,$rubriquedesc->table where $rubrique->table.id=$rubriquedesc->table.rubrique $search $order $limit";
		$resul = mysql_query($query, $rubrique->link);
	
		$compt = 1;

		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";
		
		while( $row = mysql_fetch_object($resul)){
				
			$rubrique->charger($row->id);
			
			if($pasvide != ""){
						$rec = arbreBoucle($rubrique->id);
						if(substr($rec, strlen($rec)-1) == ",") $rec = substr($rec, 0, strlen($rec)-1);
						if($rec) $virg=",";
						else $virg="";
						
				$tmprod = new Produit();
				$query4 = "select count(*) as nbres from $tmprod->table where rubrique in('" . $rubrique->id . "'$virg$rec) and ligne='1'";
				$resul4 = mysql_query($query4, $tmprod->link);
				if(!mysql_result($resul4, 0, "nbres")) continue;
			
			}
		
			$rubriquedesc->charger($rubrique->id, $_SESSION['navig']->lang);
			
			$query3 = "select * from $rubrique->table where 1 and parent=\"$rubrique->id\"";
			$resul3 = mysql_query($query3, $rubrique->link);	
			if($resul3) $nbenfant = mysql_numrows($resul3);

			$temp = str_replace("#TITRE", "$rubriquedesc->titre", $texte);
			$temp = str_replace("#STRIPTITRE", strip_tags($rubriquedesc->titre), $temp);	
			$temp = str_replace("#CHAPO", "$rubriquedesc->chapo", $temp);
			$temp = str_replace("#STRIPCHAPO", strip_tags($rubriquedesc->chapo), $temp);	
			$temp = str_replace("#DESCRIPTION", "$rubriquedesc->description", $temp);
			$temp = str_replace("#PARENT", "$rubrique->parent", $temp);
			$temp = str_replace("#ID", "$rubrique->id", $temp);		
			$temp = str_replace("#URL", "rubrique.php?id_rubrique=" . "$rubrique->id", $temp);	
			$temp = str_replace("#REWRITEURL", rewrite_rub("$rubrique->id"), $temp);	
			$temp = str_replace("#LIEN", "$rubrique->lien", $temp);	
			$temp = str_replace("#COMPT", "$compt", $temp);		
			$temp = str_replace("#NBRES", "$nbres", $temp);
			$temp = str_replace("#NBENFANT", "$nbenfant", $temp);		
		
			
			$compt ++;
			
			if(trim($temp) !="") $res .= $temp;
			
		}

	
		return $res;
		
	
	}

	/* Gestion des boucles de type Dossier*/
	function boucleDossier($texte, $args){
	
		global $id_dossier;
		
		// r�cup�ration des arguments
		$id = lireTag($args, "id");
		$parent = lireTag($args, "parent");
		$deb = lireTag($args, "deb");
		$num = lireTag($args, "num");
		$courant = lireTag($args, "courant");
		$ligne = lireTag($args, "ligne");
		$aleatoire = lireTag($args, "aleatoire");
		$exclusion = lireTag($args, "exclusion");	
		
		$search="";
		$res="";
		$limit="";
		
		if(!$deb) $deb=0;
		
		if($ligne == "") $ligne="1";
		
		// preparation de la requete
		$search .=" and ligne='$ligne'";
		if($id!="")  $search.=" and id=\"$id\"";
		if($parent!="") $search.=" and parent=\"$parent\"";
		if($courant == "1") $search .=" and id='$id_dossier'";
		else if($courant == "0") $search .=" and id!='$id_dossier'";
		if($num!="") $limit .= " limit $deb,$num";
		if($exclusion!="") $search .= " and id not in($exclusion)";
		
		$dossier = new Dossier();
		
		if($aleatoire) $order = "order by "  . " RAND()";
		else $order = "order by classement";
		
		$query = "select * from $dossier->table where 1 $search $order $limit";
		$resul = mysql_query($query, $dossier->link);
	
		$dossierdesc = new Dossierdesc();
		
		$compt = 1;

		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";
		
		while( $row = mysql_fetch_object($resul)){
		
			$dossierdesc->charger($row->id, $_SESSION['navig']->lang);
			
			$query3 = "select * from $dossier->table where 1 and parent=\"$row->id\"";
			$resul3 = mysql_query($query3, $dossier->link);	
			if($resul3) $nbenfant = mysql_numrows($resul3);

			$temp = str_replace("#TITRE", "$dossierdesc->titre", $texte);
			$temp = str_replace("#STRIPTITRE", strip_tags($dossierdesc->titre), $temp);	
			$temp = str_replace("#CHAPO", "$dossierdesc->chapo", $temp);
			$temp = str_replace("#STRIPCHAPO", strip_tags($dossierdesc->chapo), $temp);	
			$temp = str_replace("#DESCRIPTION", "$dossierdesc->description", $temp);
			$temp = str_replace("#PARENT", "$row->parent", $temp);
			$temp = str_replace("#ID", "$row->id", $temp);		
			$temp = str_replace("#URL", "dossier.php?id_dossier=" . "$row->id", $temp);
			$temp = str_replace("#REWRITEURL", rewrite_dos("$row->id"), $temp);	
			$temp = str_replace("#LIEN", "$row->lien", $temp);	
			$temp = str_replace("#COMPT", "$compt", $temp);		
			$temp = str_replace("#NBRES", "$nbres", $temp);
			$temp = str_replace("#NBENFANT", "$nbenfant", $temp);		
		
			
			$compt ++;
			
			if(trim($temp) !="") $res .= $temp;
			
		}
	

	
		return $res;
		
	
	}	
	
	function boucleImage($texte, $args){

		// r�cup�ration des arguments
		$produit = lireTag($args, "produit");
		$id = lireTag($args, "id");
		$num = lireTag($args, "num");
		$nb = lireTag($args, "nb");
		$debut = lireTag($args, "debut");
		$rubrique = lireTag($args, "rubrique");
		$largeur = lireTag($args, "largeur");
		$hauteur = lireTag($args, "hauteur");
		$dossier = lireTag($args, "dossier");
		$contenu = lireTag($args, "contenu");
		$opacite = lireTag($args, "opacite");
		$noiretblanc = lireTag($args, "noiretblanc");
		$miroir = lireTag($args, "miroir");
		$aleatoire = lireTag($args, "aleatoire");
		$exclusion = lireTag($args, "exclusion");	
		
		$search="";
		$res="";
		$limit="";
		
		if($aleatoire) $order = "order by "  . " RAND()";
		else $order=" order by classement";	
		
		if($id != "") $search .= " and id=\"$id\"";
		if($produit != "") $search .= " and produit=\"$produit\"";
		if($rubrique != "") $search .= " and rubrique=\"$rubrique\"";
		if($dossier != "") $search .= " and dossier=\"$dossier\"";
		if($contenu != "") $search .= " and contenu=\"$contenu\"";
		if($exclusion!="") $search .= " and id not in($exclusion)";
		
		$image = new Image();
		$imagedesc = new Imagedesc();

		if($debut !="") $debut--;
		else $debut=0;

        $query = "select * from $image->table where 1 $search";
        $resul = mysql_query($query, $image->link);
        $nbres = mysql_numrows($resul);
        if($debut!="" && $num=="") $num=$nbres;
                		
		if($debut!="" || $num!="") $limit .= " limit $debut,$num";
		
		if($nb!="") { $nb--; $limit .= " limit $nb,1"; }

		$query = "select * from $image->table where 1 $search $order $limit";
		$resul = mysql_query($query, $image->link);
	
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";

		$pr = new Produit();
		$prdesc = new Produitdesc();
		$rudesc = new Rubriquedesc();
		
		$compt=1;
		
		while( $row = mysql_fetch_object($resul)){
			$image->charger($row->id);
			$imagedesc->charger($image->id);
			$temp = $texte;
			
			$temp = str_replace("#FGRANDE", "#FICHIER", $temp);
			$temp = str_replace("#FPETITE", "#FICHIER", $temp);
			$temp = str_replace("#GRANDE", "#IMAGE", $temp);
			$temp = str_replace("#PETITE", "#IMAGE", $temp);
			
			if($image->produit != 0){
					$pr->charger_id($image->produit);
					$prdesc->charger($image->produit);
					$temp = str_replace("#PRODTITRE", $prdesc->titre, $temp);
					$temp = str_replace("#PRODUIT", $image->produit, $temp);
					$temp = str_replace("#PRODREF", $pr->ref, $temp);
					$temp = str_replace("#RUBRIQUE", $pr->rubrique, $temp);
					
					
					if(!$largeur && !$hauteur) 
						$temp = str_replace("#IMAGE", "client/gfx/photos/produit/" . $image->fichier, $temp);
					else $temp = str_replace("#IMAGE",  "fonctions/redimlive.php?nomorig=../client/gfx/photos/produit/" . $image->fichier . "&amp;width=$largeur&amp;height=$hauteur" . "&amp;opacite=" . $opacite . "&amp;nb=" . "$noiretblanc" . "&amp;miroir=" . "$miroir", $temp);
					
					$temp = str_replace("#FICHIER",  "client/gfx/photos/produit/" . $image->fichier, $temp);

			}
			
			else if($image->rubrique != 0){
				
				$rudesc->charger($image->rubrique);
				$temp = str_replace("#RUBRIQUE", $image->rubrique, $temp);
				$temp = str_replace("#RUBTITRE", $rudesc->titre, $temp);
			
					if(!$largeur && !$hauteur) 
						$temp = str_replace("#IMAGE", "client/gfx/photos/rubrique/" . $image->fichier, $temp);
					else $temp = str_replace("#IMAGE",  "fonctions/redimlive.php?nomorig=../client/gfx/photos/rubrique/" . $image->fichier . "&amp;width=$largeur&amp;height=$hauteur" . "&amp;opacite=" . $opacite . "&amp;nb=" . "$noiretblanc" . "&amp;miroir=" . "$miroir", $temp);
				
					$temp = str_replace("#FICHIER",  "client/gfx/photos/rubrique/" . $image->fichier, $temp);

			}
	
			else if($image->dossier != 0){
				
				$rudesc->charger($image->dossier);
				$temp = str_replace("#RUBRIQUE", $image->dossier, $temp);
				$temp = str_replace("#RUBTITRE", $rudesc->titre, $temp);
			
					if(!$largeur && !$hauteur) 
						$temp = str_replace("#IMAGE", "client/gfx/photos/dossier/" . $image->fichier, $temp);
					else $temp = str_replace("#IMAGE",  "fonctions/redimlive.php?nomorig=../client/gfx/photos/dossier/" . $image->fichier . "&amp;width=$largeur&amp;height=$hauteur" . "&amp;opacite=" . $opacite . "&amp;nb=" . "$noiretblanc" . "&amp;miroir=" . "$miroir", $temp);
					
					$temp = str_replace("#FICHIER",  "client/gfx/photos/dossier/" . $image->fichier, $temp);

			}	
	
			else if($image->contenu != 0){
			
					$prdesc->charger($image->contenu);
					$temp = str_replace("#PRODTITRE", $prdesc->titre, $temp);
					$temp = str_replace("#PRODUIT", $image->contenu, $temp);
					
					if(!$largeur && !$hauteur) 
						$temp = str_replace("#IMAGE", "client/gfx/photos/contenu/" . $image->fichier, $temp);
					else $temp = str_replace("#IMAGE",  "fonctions/redimlive.php?nomorig=../client/gfx/photos/contenu/" . $image->fichier . "&amp;width=$largeur&amp;height=$hauteur". "&amp;opacite=" . $opacite . "&amp;nb=" . "$noiretblanc" . "&amp;miroir=" . "$miroir", $temp);
					
						$temp = str_replace("#FICHIER",  "client/gfx/photos/contenu/" . $image->fichier, $temp);

			}	
	
				$temp = str_replace("#ID",  $image->id, $temp);	
				$temp = str_replace("#TITRE",  $imagedesc->titre, $temp);	
				$temp = str_replace("#CHAPO",  $imagedesc->chapo, $temp);	
				$temp = str_replace("#DESCRIPTION",  $imagedesc->description, $temp);	
				$temp = str_replace("#COMPT", "$compt", $temp);
				
			$compt++;
				
			$res .= $temp;
		}



		
		return $res;
	
	}

	/* Gestion des boucles de type Client*/
	function boucleClient($texte, $args){
		// r�cup�ration des arguments
		$id = lireTag($args, "id");
		$ref = lireTag($args, "ref");
		$raison = lireTag($args, "raison");
		$nom = lireTag($args, "nom");
		$cpostal = lireTag($args, "cpostal");
		$ville = lireTag($args, "ville");
		$pays = lireTag($args, "pays");
		$parrain = lireTag($args, "parrain");
		$revendeur = lireTag($args, "revendeur");

		
		$search="";
		$res="";
		
		// preparation de la requete
		if($id!="")  $search.=" and id=\"$id\"";
		if($ref!="")  $search.=" and ref=\"$ref\"";
		if($raison!="")  $search.=" and raison=\"$raison\"";
		if($nom!="")  $search.=" and nom=\"$nom\"";
		if($cpostal!="")  $search.=" and cpostal=\"$cpostal\"";
		if($ville!="")  $search.=" and ville=\"$ville\"";
		if($pays!="")  $search.=" and pays=\"$pays\"";
		if($parrain!="")  $search.=" and parrain=\"$parrain\"";
		if($revendeur!="")  $search.=" and type=\"$revendeur\"";
		
		$client = new Client();
		$order = "order by nom";
		
		$query = "select * from $client->table where 1 $search $order";
		$resul = mysql_query($query, $client->link);
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";
		
		while( $row = mysql_fetch_object($resul)){
	
				$temp = str_replace("#ID", "$row->id", $texte);		
				$temp = str_replace("#REF", "$row->ref", $temp);		
				$temp = str_replace("#RAISON", "$row->raison", $temp);		
				$temp = str_replace("#ENTREPRISE", "$row->entreprise", $temp);
				$temp = str_replace("#SIRET", "$row->siret", $temp);					
				$temp = str_replace("#NOM", "$row->nom", $temp);					
				$temp = str_replace("#PRENOM", "$row->prenom", $temp);					
				$temp = str_replace("#TELFIXE", "$row->telfixe", $temp);	
				$temp = str_replace("#TELPORT", "$row->telport", $temp);					
				$temp = str_replace("#EMAIL", "$row->email", $temp);					
				$temp = str_replace("#ADRESSE1", "$row->adresse1", $temp);					
				$temp = str_replace("#ADRESSE2", "$row->adresse2", $temp);					
				$temp = str_replace("#ADRESSE3", "$row->adresse3", $temp);					
				$temp = str_replace("#CPOSTAL", "$row->cpostal", $temp);					
				$temp = str_replace("#VILLE", "$row->ville", $temp);					
				$temp = str_replace("#PAYS", "$row->pays", $temp);					
				$temp = str_replace("#PARRAIN", "$row->parrain", $temp);					
				$temp = str_replace("#TYPE", "$row->type", $temp);					
				$temp = str_replace("#POURCENTAGE", "$row->pourcentage", $temp);					

			
			$res .= $temp;
			
		}
	

	
		return $res;
		
	
	}
	
	function boucleDevise($texte, $args){

		// r�cup�ration des arguments
		$produit = lireTag($args, "produit");
		$id = lireTag($args, "id");
		$somme = lireTag($args, "somme");
	
		$search="";
		$devise="";
		$limit="";
		$res="";
		
		if($somme == "") $somme=0;
		
		$prod = new Produit();
		$prod->charger_id($produit);

		if($devise) $search .= " and devise=\"$devise\"";
		
		$devise = new Devise();

		$query = "select * from $devise->table where 1 $search $limit";
 		
		$resul = mysql_query($query, $devise->link);
	
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";
			
		while( $row = mysql_fetch_object($resul)){
			$devise->charger($row->id);
			$prix = round($prod->prix * $devise->taux, 2);
			$prix2 = round($prod->prix2 * $devise->taux, 2);
			$convert = round($somme * $devise->taux, 2);
			$total = round( $_SESSION['navig']->panier->total() * $devise->taux, 2);
		
			$prix = number_format($prix, 2, ".", ""); 
			$prix2 = number_format($prix2, 2, ".", ""); 
			$total = number_format($total, 2, ".", ""); 
			$convert = number_format($convert, 2, ".", ""); 
		
			$temp = str_replace("#PRIX2",  "$prix2", $texte);	
			$temp = str_replace("#PRIX", "$prix", $temp);
			$temp = str_replace("#TOTAL", "$total", $temp);
			$temp = str_replace("#CONVERT", "$convert", $temp);
			$temp = str_replace("#NOM",  "$devise->nom", $temp);	
			$temp = str_replace("#CODE",  "$devise->code", $temp);	
			$temp = str_replace("#TAUX", "$devise->taux", $temp);

			$res .= $temp;
		}

		return $res;
	
	}

	function boucleDocument($texte, $args){

		// r�cup�ration des arguments
		$produit = lireTag($args, "produit");
		$rubrique = lireTag($args, "rubrique");
		$nb = lireTag($args, "nb");
		$debut = lireTag($args, "debut");
		$num = lireTag($args, "num");
		$dossier = lireTag($args, "dossier");
		$contenu = lireTag($args, "contenu");
		$exclusion = lireTag($args, "exclusion");	
		
		$search="";
		$order="";
		$limit="";
		$res="";
			
		if($produit) $search .= " and produit=\"$produit\"";
		if($rubrique != "") $search .= " and rubrique=\"$rubrique\"";
		if($dossier != "") $search .= " and dossier=\"$dossier\"";
		if($contenu != "") $search .= " and contenu=\"$contenu\"";
		if($exclusion!="") $search .= " and id not in($exclusion)";
						
		$document = new Document();
		$documentdesc = new Documentdesc();

		if($debut !="") $debut--;
		else $debut=0;

        $query = "select * from $document->table where 1 $search";
        $resul = mysql_query($query, $document->link);
        $nbres = mysql_numrows($resul);
        if($debut!="" && $num=="") $num=$nbres;
                		
		if($num!="") $limit .= " limit $debut,$num";
		if($nb!="") { $nb--; $limit .= " limit $nb,1"; }

		$query = "select * from $document->table where 1 $search $order $limit";
 		
		$resul = mysql_query($query, $document->link);
	
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";

		while( $row = mysql_fetch_object($resul)){
			$document->charger($row->id);
			$documentdesc->charger($document->id);
			$temp = str_replace("#TITRE", "$documentdesc->titre", $texte);
			$temp = str_replace("#CHAPO", "$documentdesc->chapo", $temp);
			$temp = str_replace("#DESCRIPTION", "$documentdesc->description", $temp);
			$temp = str_replace("#FICHIER", "client/document/" . $document->fichier, $temp);

			$res .= $temp;
		}
	

		
		return $res;
	
	}

	function boucleAccessoire($texte, $args){

		// r�cup�ration des arguments
		$produit = lireTag($args, "produit");
		$deb = lireTag($args, "deb");
		$num = lireTag($args, "num");
		$aleatoire = lireTag($args, "aleatoire");
		$classement = lireTag($args, "classement");
		
		$search="";
		
		if(!$deb) $deb=0;	
		if(!$num) $num = "999999999";
		
		if($produit) $search .= " and produit=\"$produit\"";
		$limit .= " limit $deb,$num";

		if($classement == "manuel") $order = "order by classement";		
		else if($aleatoire) $order = "order by "  . " RAND()";		
		
		
		$accessoire = new Accessoire();

		$query = "select * from $accessoire->table where 1 $search $order $limit";
		$resul = mysql_query($query, $accessoire->link);
	
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";

		while( $row = mysql_fetch_object($resul)){
			$accessoire->charger($row->id);
			$temp = str_replace("#ACCESSOIRE", "$accessoire->accessoire", $texte);

			$res .= $temp;
		}

		return $res;
	
	}
	
	function boucleProduit($texte, $args, $type=0){
			global $page_thelia, $totbloc, $ref, $pagesess;
			
			// r�cup�ration des arguments
			$rubrique = lireTag($args, "rubrique");
			$deb = lireTag($args, "deb");
			$num = lireTag($args, "num");
			$passage = lireTag($args, "passage");
			$ligne = lireTag($args, "ligne");
			$bloc = lireTag($args, "bloc");
			$nouveaute = lireTag($args, "nouveaute");
			$promo = lireTag($args, "promo");
			$reappro = lireTag($args, "reappro");
			$refp = lireTag($args, "ref");
			$id = lireTag($args, "id");
			$garantie = lireTag($args, "garantie");
			$motcle = lireTag($args, "motcle");
			$classement = lireTag($args, "classement");
			$aleatoire = lireTag($args, "aleatoire");
			$prixmin = lireTag($args, "prixmin");
			$prixmax = lireTag($args, "prixmax");
			$caracteristique = lireTag($args, "caracteristique");
			$caracdisp = lireTag($args, "caracdisp");
			$caracval = lireTag($args, "caracval");
			$typech = lireTag($args, "typech");
			$declinaison = lireTag($args, "declinaison");			
			$declidisp = lireTag($args, "declidisp");
			$declival = lireTag($args, "declival");
			$stockmini = lireTag($args, "stockmini");
			$declistockmini = lireTag($args, "declistockmini");
			$courant = lireTag($args, "courant");
			$profondeur = lireTag($args, "profondeur");		
			$exclusion = lireTag($args, "exclusion");	
			$poids = lireTag($args, "poids");
			$stockvide = lireTag($args, "stockvide");
						
			if($bloc) $totbloc=$bloc;
			if(!$deb) $deb=0;
			
			if($page_thelia) $_SESSION['navig']->page = $page_thelia;
			if($pagesess == 1) $page_thelia =  $_SESSION['navig']->page;
			
			if(!$page_thelia ||  $page_thelia==1 ) $page_thelia=0; 
			
			if(!$totbloc) $totbloc=1;
			if($page_thelia) $deb = ($page_thelia-1)*$totbloc*$num+$deb; 
	
			// initialisation de variables
			$search = "";
			$order = "";
			$comptbloc=0;
			$limit="";
			$pourcentage="";
			$res="";
			$virg="";
			
			// preparation de la requete
			
			if($courant == "1") $search .= " and ref=\"$ref\"";
			else if($courant == "0") $search .= " and ref!=\"$ref\"";
			
			if($exclusion!="") $search .= " and id not in($exclusion)";
			
			if($rubrique!=""){
				$srub = "";
				
				if($profondeur == "") $profondeur=0;
				$tabrub = explode(",", $rubrique);
				for($compt = 0; $compt<count($tabrub); $compt++){
					$rec = arbreBoucle($tabrub[$compt], $profondeur);
					if(substr($rec, strlen($rec)-1) == ",") $rec = substr($rec, 0, strlen($rec)-1);
					if($rec) $virg=",";
					$srub .= $tabrub[$compt] . $virg . $rec . $virg;
				}
				if(substr($srub, strlen($srub)-1) == ",")
					$srub = substr($srub, 0, strlen($srub)-1);
				 $search .= " and rubrique in($srub)";
			}
			
			if($ligne == "") $ligne="1";

			$search .= " and ligne=\"$ligne\"";
			if($id!="") $search .= " and id=\"$id\"";				 
			if($nouveaute!="") $search .= " and nouveaute=\"$nouveaute\"";
			if($promo!="") $search .= " and promo=\"$promo\"";
			if($reappro!="") $search .= " and reappro=\"$reappro\"";
			if($garantie!="") $search .= " and garantie=\"$garantie\"";
			if($prixmin!="") $search .= " and ((prix2>=\"$prixmin\" and promo=\"1\") or (prix>=\"$prixmin\" and promo=\"0\"))";
			if($prixmax!="") $search .= " and ((prix2<=\"$prixmax\" and promo=\"1\") or (prix<=\"$prixmax\" and promo=\"0\"))";
			if($poids!="") $search .= " and poids<=\"$poids\"";
			if($stockmini!="") $search .= " and stock>=\"$stockmini\"";

			if (""!=$stockvide) {
				if (0 < $stockvide) { $search .= " and stock<=\"0\""; }
				elseif (0 >= $stockvide) { $search .= " and stock>\"0\""; }
			}
									
			if($refp!="") $search .= " and ref=\"$refp\"";

			if($bloc == "-1") $bloc = "999999999";
			if($bloc!="" && $num!="") $limit .= " limit $deb,$bloc";
			else if($num!="") $limit .= " limit $deb,$num";
			
			if($classement == "prixmin") $order = "order by "  . " prix";
			else if($classement == "prixmax") $order = "order by "  . " prix desc";
			else if($classement == "rubrique") $order = "order by "  . " rubrique";
			else if($aleatoire) $order = "order by "  . " RAND()";
			else if($classement == "manuel") $order = "order by classement";
			else if($classement == "inverse") $order = "order by classement desc";
			else if($classement == "date") $order = "order by datemodif desc";
			else $order = "order by classement";
			
		
			
			
			/* Demande de caracteristiques */
			if($caracdisp != ""){
			
			if(! strstr($caracteristique, "-")) $caracteristique .= "-";
			if(! strstr($caracdisp, "-")) $caracdisp .= "-";
			
			$lcaracteristique = explode("-", $caracteristique);
			$lcaracdisp = explode("-", $caracdisp);
			
			$i = 0;

			$tcaracval = new Caracval();

			while($i<count($lcaracteristique)-1){
				$caracteristique = $lcaracteristique[$i];
				$caracdisp = $lcaracdisp[$i];
				if($caracdisp == "*")
					$query = "select * from $tcaracval->table where caracteristique='$caracteristique' and caracdisp<>''";
				else if($caracdisp == "-")	$query = "select * from $tcaracval->table where caracteristique='$caracteristique' and caracdisp=''";
				else $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and caracdisp='$caracdisp'";

				$resul = mysql_query($query);
				if(!mysql_numrows($resul)) return;
				
				$liste="";
				
				while($row = mysql_fetch_object($resul))
					$liste .= "'$row->produit', ";
			
				$liste = substr($liste, 0, strlen($liste) - 2);
				
				$i++;
				
				if($liste!="") $search .= " and id in($liste)";	
				else return "";
			}

			

		}	

			if($caracval != ""){
			
			$i = 0;
			$liste="";

			$tcaracval = new Caracval();

				if($caracval == "*") $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and valeur<>''";
				else if($caracval == "-") $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and valeur=''";
				else if($typech == "like") $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and valeur like '$caracval'";
				else $query = "select * from $tcaracval->table where caracteristique='$caracteristique' and valeur ='$caracval'";

				$resul = mysql_query($query);
				
				$liste="";
				
				while($row = mysql_fetch_object($resul))
					$liste .= "'$row->produit', ";
				$liste = substr($liste, 0, strlen($liste) - 2);
				
				$i++;
			
			
			if($liste!="") $search .= " and id in($liste)";	
			else return "";
		}	


			/* Demande de declinaisons */
			if($declidisp != ""){

			if(! strstr($declinaison, "-")) $declinaison .= "-";
			if(! strstr($declidisp, "-")) $declidisp .= "-";
			if(! strstr($ldeclistockmini, "-")) $ldeclistockmini .= "-";
				
			$ldeclinaison = explode("-", $declinaison);
			$ldeclidisp = explode("-", $declidisp);
			$ldeclistockmini = explode("-", $declistockmini);
			
			$i = 0;
			$liste="";
			$exdecprod = new Exdecprod();
			$stock = new Stock();

			while($i<count($ldeclinaison)-1){

				$declinaison = $ldeclinaison[$i];
				$declidisp = $ldeclidisp[$i];
				$declistockmini = $ldeclistockmini[$i];
				
		 		$query = "select * from $exdecprod->table where declidisp='$declidisp'";
				$resul = mysql_query($query);
		
				if(mysql_numrows($resul)) 
						while($row = mysql_fetch_object($resul))
							$liste .= "'$row->produit', ";
	
				if($liste!="") {
						$liste = substr($liste, 0, strlen($liste) - 2);
						$search .= " and id not in($liste)";
				}	
		
				$liste="";
				
				if($declistockmini != ""){
					$query = "select * from $stock->table where declidisp='$declidisp' and valeur>='$declistockmini'";
					$resul = mysql_query($query);

					if(mysql_numrows($resul)) 
							while($row = mysql_fetch_object($resul))
								$liste .= "'$row->produit', ";

					if($liste!="") {
								$liste = substr($liste, 0, strlen($liste) - 2);
								$search .= " and id in($liste)";
					}
					else return "";
				}	
			
				$i++;

			}
		
		}
				
			$produit = new Produit();
			$produitdesc = new Produitdesc();
			
		
			if($motcle){
				$liste="";
				
  				$query = "select * from $produitdesc->table  LEFT JOIN $produit->table ON $produit->table.id=$produitdesc->table.produit WHERE $produit->table.ref='$motcle' or titre like '% $motcle%' or titre like '%$motcle %' OR titre='$motcle' OR chapo like '% $motcle%' OR chapo like '%$motcle %' OR description like '% $motcle%' OR description like '%$motcle %'";
			
			    $resul = mysql_query($query, $produitdesc->link);
				$nbres = mysql_numrows($resul);

			
				if(!$nbres) return "";
				
			
				while( $row = mysql_fetch_object($resul) ){
					$liste .= "'$row->produit', ";
				}
			
				$liste = substr($liste, 0, strlen($liste) - 2);
				$query = "select * from $produit->table where id in ($liste) and ligne=\"$ligne\" $limit";
				$saveReq = "select * from $produit->table where id in ($liste) and ligne=\"$ligne\"";
			}
			
		else $query = "select * from $produit->table where 1 $search $order $limit";
		$resul = mysql_query($query, $produit->link);
		$nbres = mysql_numrows($resul);
		$saveReq = "select * from $produit->table where 1 $search $order ";

		if(!$nbres) return "";
		// substitutions
		if($type) return $query;
		
		$saveReq = str_replace("*", "count(*) as totcount", $saveReq);
		$saveRes = mysql_query($saveReq);
		$countRes = mysql_result($saveRes, 0, "totcount") . " ";
	
		$compt = 0;
		
		while( $row = mysql_fetch_object($resul) ){
			
			$compt++;
			
			if($passage != "" && $comptbloc>$passage-1)
			      break;
			
			if($num>0) 
				if($comptbloc>=ceil($countRes/$num) && $bloc!="") continue;

			if($comptbloc == 0) $debcourant=0;
			else $debcourant = $num * ($comptbloc);
			$comptbloc++;
						
			$rubriquedesc = new Rubriquedesc();
			$rubriquedesc->charger($row->rubrique, $_SESSION['navig']->lang);
		
			$produitdesc->charger($row->id, $_SESSION['navig']->lang);
				
			$temp = $texte;
			
			if( $row->promo == "1" ) $temp = ereg_replace("#PROMO\[([^]]*)\]\[([^]]*)\]", "\\1", $temp);
	 		else $temp = ereg_replace("#PROMO\[([^]]*)\]\[([^]]*)\]", "\\2", $temp);
	 		
			if( $row->promo == "1" ) $pourcentage =  ceil((100 * ($row->prix - $row->prix2)/$row->prix));

			$prix = $row->prix - ($row->prix * $_SESSION['navig']->client->pourcentage / 100);
			$prix2 = $row->prix2 - ($row->prix2 * $_SESSION['navig']->client->pourcentage / 100);
			$ecotaxe = $row->ecotaxe;
			
			$pays = new Pays();
			$pays->charger($_SESSION['navig']->client->pays);
			
			$zone = new Zone();
			$zone->charger($pays->zone);
			
			if($_SESSION['navig']->client->type == "1"){
				$prix = $prix/1.196;
				$prix2 = $prix2/1.196;
				$ecotaxe = $row->ecotaxe/1.196;
				
			}
			
			$prix = round($prix, 2);
			$prix2 = round($prix2, 2);
		
			$prix = number_format($prix, 2, ".", ""); 
			$prix2 = number_format($prix2, 2, ".", ""); 
			
			if($deb != "" && !$page_thelia) $debcourant+=$deb-1;

			$temp = str_replace("#REF", "$row->ref", $temp);
			$temp = str_replace("#COMPT", "$compt", $temp);
			$temp = str_replace("#DATE", substr($row->datemodif, 0, 10), $temp);
			$temp = str_replace("#HEURE", substr($row->datemodif, 11), $temp);
			$temp = str_replace("#DEBCOURANT", "$debcourant", $temp);
			$temp = str_replace("#ID", "$row->id", $temp);		
			$temp = str_replace("#PRIX2", "$prix2", $temp);					
			$temp = str_replace("#PRIX", "$prix", $temp);	
			$temp = str_replace("#TVA", "$row->tva", $temp);	
			$temp = str_replace("#ECOTAXE", "$row->ecotaxe", $temp);	
			$temp = str_replace("#STOCK", "$row->stock", $temp);	
			$temp = str_replace("#POURCENTAGE", "$pourcentage", $temp);	
			$temp = str_replace("#RUBRIQUE", "$row->rubrique", $temp);			
			$temp = str_replace("#PERSO", "$row->perso", $temp);			
			$temp = str_replace("#APPRO", "$row->appro", $temp);			
			$temp = str_replace("#POIDS", "$row->poids", $temp);			
			$temp = str_replace("#TITRE", "$produitdesc->titre", $temp);
			$temp = str_replace("#STRIPTITRE", strip_tags($produitdesc->titre), $temp);	
			$temp = str_replace("#CHAPO", "$produitdesc->chapo", $temp);	
			$temp = str_replace("#STRIPCHAPO", strip_tags($produitdesc->chapo), $temp);	
			$temp = str_replace("#DESCRIPTION", "$produitdesc->description", $temp);
			$temp = str_replace("#STRIPDESCRIPTION", strip_tags($produitdesc->description), $temp);	
			$temp = str_replace("#URL", "produit.php?ref=" . "$row->ref" . "&amp;id_rubrique=" . "$row->rubrique", $temp);	
			$temp = str_replace("#REWRITEURL", rewrite_prod("$row->ref"), $temp);	
			$temp = str_replace("#GARANTIE", "$row->garantie", $temp);			
			$temp = str_replace("#PANIERAPPEND", "panier.php?action=" . "ajouter" . "&amp;" . "ref=" . "$row->ref" . "&amp;" . "append=1", $temp);	
			$temp = str_replace("#PANIER", "panier.php?action=" . "ajouter" . "&amp;" . "ref=" . "$row->ref" , $temp);	
			$temp = str_replace("#RUBTITRE", "$rubriquedesc->titre", $temp);
			
			
			$res .= $temp;
			
		}
	

		return $res;
	
	}

		
	function boucleContenu($texte, $args, $type=0){
			global $page_thelia, $totbloc, $id_contenu;
			
			// r�cup�ration des arguments
			$dossier = lireTag($args, "dossier");
			$ligne = lireTag($args, "ligne");
			$deb = lireTag($args, "deb");
			$num = lireTag($args, "num");
			$bloc = lireTag($args, "bloc");
			$id = lireTag($args, "id");
			$motcle = lireTag($args, "motcle");
			$classement = lireTag($args, "classement");
			$aleatoire = lireTag($args, "aleatoire");
			$produit = lireTag($args, "produit");
			$rubrique = lireTag($args, "rubrique");
			$profondeur = lireTag($args, "profondeur");		
			$courant = lireTag($args, "courant");			
			$exclusion = lireTag($args, "exclusion");	
			
			if($bloc) $totbloc=$bloc;	
			if(!$deb) $deb=0;
		
			if(!$totbloc) $totbloc=1;
			// initialisation de variables
			$search = "";
			$order = "";
			$comptbloc=0;
			$virg="";
			$limit="";
			$res="";
			
			// preparation de la requete
			if($dossier!=""){
				if($profondeur == "") $profondeur=0;
				$rec = arbreBoucle_dos($dossier, $profondeur);
				if(substr($rec, strlen($rec)-1) == ",") $rec = substr($rec, 0, strlen($rec)-1);
				if($rec) $virg=",";
				
				 $search .= " and dossier in('$dossier'$virg$rec)";
			}
			
			if($ligne == "") $ligne="1";
			
			$search .= " and ligne=\"$ligne\"";

			if($id!="") $search .= " and id=\"$id\"";				 
			if($courant == "1") $search .=" and id='$id_contenu'";
			else if($courant == "0") $search .=" and id!='$id_contenu'";
			if($exclusion!="") $search .= " and id not in($exclusion)";

			if($bloc == "-1") $bloc = "999999999";
			if($bloc!="" && $num!="") $limit .= " limit $deb,$bloc";
			else if($num!="") $limit .= " limit $deb,$num";
			
			$liste= "";
			
			if($rubrique != "" || $produit !=""){
				if($rubrique){
					$type = 0; 
					$objet = $rubrique;
				}
				
				else{
					 $type = 1;
					 $objet = $produit;
				}
				
				$contenuassoc = new Contenuassoc();
				$query = "select * from $contenuassoc->table where objet=\"" . $objet . "\" and type=\"" . $type . "\"";
				$resul = mysql_query($query, $contenuassoc->link);
				while($row = mysql_fetch_object($resul)) 
					$liste .= "'" . $row->contenu . "',"; 
					
					
				$liste = substr($liste, 0, strlen($liste)-1);
				if($liste != "") $search .= " and id in ($liste)";	
				else $search .= " and id in ('')";
				
				$type="";
			}

		
			if($aleatoire) $order = "order by "  . " RAND()";
			else if($classement == "manuel") $order = "order by classement";
			else if($classement == "inverse") $order = "order by classement desc";
			
			
			$contenu = new Contenu();
			$contenudesc = new Contenudesc();
			
			if($motcle){
				$liste="";
				
				$query = "select * from $contenudesc->table  LEFT JOIN $contenu->table ON $contenu->table.id=$contenudesc->table.id WHERE titre like '%$motcle%' OR chapo like '%$motcle%' OR description like '%$motcle%'";
			
			    $resul = mysql_query($query, $contenudesc->link);
				$nbres = mysql_numrows($resul);

			
				if(!$nbres) return "";
				
			
				while( $row = mysql_fetch_object($resul) ){
					$liste .= "'$row->contenu', ";
				}
			
				$liste = substr($liste, 0, strlen($liste) - 2);
				$query = "select * from $contenu->table where id in ($liste) and ligne=\"$ligne\" $limit";
				$saveReq = "select * from $contenu->table where id in ($liste) and ligne=\"$ligne\"";
			}
			
		else $query = "select * from $contenu->table where 1 $search $order $limit";
		$saveReq = "select * from $contenu->table where 1 $search";
		
		$resul = mysql_query($query, $contenu->link);
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";
		// substitutions
		if($type) return $query;

		$saveReq = str_replace("*", "count(*) as totcount", $saveReq);
		$saveRes = mysql_query($saveReq);
		$countRes = mysql_result($saveRes, 0, "totcount") . " ";
		
		while( $row = mysql_fetch_object($resul) ){
	
			if($num>0) 
				if($comptbloc>=ceil($countRes/$num) && $bloc!="") continue;
				
			if($comptbloc == 0) $debcourant=0;
			else $debcourant = $num * ($comptbloc);
			$comptbloc++;
			
			$dossierdesc = new Dossierdesc();
			$dossierdesc->charger($row->dossier, $_SESSION['navig']->lang);
		
			$contenudesc->charger($row->id, $_SESSION['navig']->lang);
				
			$temp = $texte;
			
			$temp = str_replace("#DATE", substr($row->datemodif, 0, 10), $temp);
			$temp = str_replace("#HEURE", substr($row->datemodif, 11), $temp);
			$temp = str_replace("#DEBCOURANT", "$debcourant", $temp);
			$temp = str_replace("#ID", "$row->id", $temp);		
			$temp = str_replace("#DOSSIER", "$row->dossier", $temp);			
			$temp = str_replace("#TITRE", "$contenudesc->titre", $temp);
			$temp = str_replace("#STRIPTITRE", strip_tags($contenudesc->titre), $temp);	
			$temp = str_replace("#CHAPO", "$contenudesc->chapo", $temp);	
			$temp = str_replace("#STRIPCHAPO", strip_tags($contenudesc->chapo), $temp);	
			$temp = str_replace("#DESCRIPTION", "$contenudesc->description", $temp);
			$temp = str_replace("#STRIPDESCRIPTION", strip_tags($contenudesc->description), $temp);	
			$temp = str_replace("#URL", "contenu.php?id_contenu=" . "$row->id", $temp);	
			$temp = str_replace("#REWRITEURL", rewrite_cont("$row->id"), $temp);			
			$temp = str_replace("#DOSTITRE", "$dossierdesc->titre", $temp);
			$temp = str_replace("#PRODUIT", "$produit", $temp);
			$temp = str_replace("#RUBRIQUE", "$rubrique", $temp);			
			
			$res .= $temp;
			
		}
	
	
		return $res;
	
	}


	function boucleContenuassoc($texte, $args){
        $objet = lireTag($args, "objet");
        $typeobj = lireTag($args, "typeobj");
        $contenu = lireTag($args, "contenu");
        $classement = lireTag($args, "contenu");
        $num = lireTag($args, "num");
      	$deb = lireTag($args, "deb");
		
		if(!$deb) $deb=0;
		
		$search = "";

		if($objet != "")
        	$search .= " and objet=\"$objet\"";

		if($typeobj != "")
        	$search .= " and type=\"$typeobj\"";

		if($contenu != "")
        	$search .= " and contenu=\"$contenu\"";

		$order="";
		$limit="";
		
		if($num!="") $limit .= " limit $deb,$num";
		
		if($classement == "manuel")
			$order = "order by classement";
		
		$contenuassoc = new Contenuassoc();
		$query = "select * from $contenuassoc->table where 1 $search $limit";
		$resul = mysql_query($query, $contenuassoc->link);
		
		if(! mysql_numrows($resul))
			return "";
			
		while($row = mysql_fetch_object($resul)){
              $temp = str_replace("#OBJET", $row->objet, $texte);
              $temp = str_replace("#TYPE", $row->type, $temp);
              $temp = str_replace("#CONTENU", $row->contenu, $temp);

              $res .= $temp;

        }

              return $res;
		
	}
	
	function bouclePage($texte, $args){
			global $page_thelia, $id_rubrique;
			
			// r�cup�ration des arguments
			
			$num = lireTag($args, "num");
			$courante = lireTag($args, "courante");
			$pagecourante = lireTag($args, "pagecourante");
			$typeaff = lireTag($args, "typeaff");
			$max = lireTag($args, "max");
			$affmin = lireTag($args, "affmin");
            $avance = lireTag($args, "avance");
			
			$i="";
			
			if( $page_thelia<=0) $page_thelia=1;
			$bpage=$page_thelia;
			$res="";
				
				$produit = new Produit();
				
				 $query = boucleProduit($texte, str_replace("num", "null", $args), 1);

				if($query != ""){ 
					$pos = strpos($query, "limit");
					if($pos>0) $query = substr($query, 0, $pos);
	
					$resul = mysql_query($query, $produit->link);
					$nbres = mysql_numrows($resul);
				}
				
				else $nbres = 0;

				$page_thelia = $bpage;
				
				$nbpage = ceil($nbres/$num);
				if($page_thelia+1>$nbpage) $pagesuiv=$page_thelia;
				else $pagesuiv=$page_thelia+1;
				
				if($page_thelia-1<=0) $pageprec=1;
				else $pageprec=$page_thelia-1;				


				if($nbpage<$affmin) return;
				if($nbpage == 1) return;
				
				if($typeaff == 1){
					if(!$max) $max=$nbpage+1;
					if($page_thelia && $max && $page_thelia>$max) $i=ceil(($page_thelia)/$max)*$max-$max+1;	
				
					if($i == 0) $i=1;
				
					$fin = $i+$max;	


					
					
					for( ; $i<$nbpage+1 && $i<$fin; $i++ ){
					
						$temp = str_replace("#PAGE_NUM", "$i", $texte);		
						$temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
						$temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
						$temp = str_replace("#RUBRIQUE", "$id_rubrique", $temp);
				
						if($pagecourante && $pagecourante == $i){		

							if($courante =="1" && $page_thelia == $i ) $res .= $temp;	
							else if($courante == "0" && $page_thelia != $i ) $res .= $temp;	
							else if($courante == "") $res .= $temp;
						}	
						
						else if(!$pagecourante) $res .= $temp;								
					}
				
				}
				
                else if($typeaff == "0" && ($avance == "precedente" && $pageprec != $page_thelia)){

                        $temp = str_replace("#PAGE_NUM", "$page_thelia", $texte);
                        $temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
                        $temp = str_replace("#RUBRIQUE", "$id_rubrique", $temp);
                        $res .= $temp;
                }

                else if($typeaff == "0" && ($avance == "suivante" && $pagesuiv != $page_thelia)){

                        $temp = str_replace("#PAGE_NUM", "$page_thelia", $texte);
                        $temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
                        $temp = str_replace("#RUBRIQUE", "$id_rubrique", $temp);
                        $res .= $temp;
                }

                else if($typeaff == "0" && $avance == ""){

                        $temp = str_replace("#PAGE_NUM", "$page_thelia", $texte);
                        $temp = str_replace("#PAGE_SUIV", "$pagesuiv", $temp);
                        $temp = str_replace("#PAGE_PREC", "$pageprec", $temp);
                        $temp = str_replace("#RUBRIQUE", "$id_rubrique", $temp);
                        $res .= $temp;
                }					
			
		
				return $res;
			
			
	}
	

	function bouclePanier($texte, $args){

		$deb = lireTag($args, "deb");
		$fin = lireTag($args, "fin");
		$dernier = lireTag($args, "dernier");
		
		if(!$deb) $deb=0;
		if(!$fin) $fin=$_SESSION['navig']->panier->nbart;
		if($dernier == 1) 
			$deb = $_SESSION['navig']->panier->nbart - 1;
				
		$total = 0;
		$res="";
		
		if(! $_SESSION['navig']->panier->nbart) return;
		
		for($i=$deb; $i<$fin; $i++){
			$plus = $_SESSION['navig']->panier->tabarticle[$i]->quantite+1;
			$moins = $_SESSION['navig']->panier->tabarticle[$i]->quantite-1;
			
			if($moins == 0) $moins++;
			
			$quantite =  $_SESSION['navig']->panier->tabarticle[$i]->quantite;
			if( ! $_SESSION['navig']->panier->tabarticle[$i]->produit->promo)
				$prix = $_SESSION['navig']->panier->tabarticle[$i]->produit->prix - ($_SESSION['navig']->panier->tabarticle[$i]->produit->prix * $_SESSION['navig']->client->pourcentage / 100);
			else $prix = $_SESSION['navig']->panier->tabarticle[$i]->produit->prix2 - ($_SESSION['navig']->panier->tabarticle[$i]->produit->prix2 * $_SESSION['navig']->client->pourcentage / 100);	
			
			$total=round($prix*$quantite, 2);
			$prix = round($prix, 2);
			
			$port = port();
			$totcmdport = $total + $port;
			
			$totsansport = $_SESSION['navig']->panier->total();

			$pays = new Pays();
			$pays->charger($_SESSION['navig']->client->pays);
			
			$zone = new Zone();
			$zone->charger($pays->zone);
						
			if($_SESSION['navig']->client->type) {
				$prix = round($prix/1.196, 2);
				$total = round($total/1.196, 2);
				$port = round($port/1.196, 2);
				$totcmdport = round($totcmdport/1.196, 2);
				$totsansport = round($totsansport/1.196, 2);
			}
			
			$produitdesc = new Produitdesc();
			$produitdesc->charger($_SESSION['navig']->panier->tabarticle[$i]->produit->id,  $_SESSION['navig']->lang);

			$declidisp = new Declidisp();
			$declidispdesc = new Declidispdesc();
			$declinaison = new Declinaison();
			$declinaisondesc = new Declinaisondesc();
			
			$dectexte = "";
			$decval = "";
			
			
		    for($compt = 0; $compt<count($_SESSION['navig']->panier->tabarticle[$i]->perso); $compt++){
				$tperso = $_SESSION['navig']->panier->tabarticle[$i]->perso[$compt];
				$declinaison->charger($tperso->declinaison);
				// recup valeur declidisp ou string
				if($declinaison->isDeclidisp($tperso->declinaison)){
					$declidisp->charger($tperso->valeur);
					$declidispdesc->charger($declidisp->id);
					$decval .= $declidispdesc->titre . " ";
				}
				
				else $decval .= $tperso->valeur . " ";
				
				// recup declinaison associee
				$declinaisondesc->charger($tperso->declinaison);
				
				$dectexte .= $declinaisondesc->titre . " " . $declidispdesc->titre . " ";
				
				
				
				
			}	
		
			$prix = number_format($prix, 2, ".", ""); 
			$total = number_format($total, 2, ".", ""); 
			$totcmdport = number_format($totcmdport, 2); 
			$port = number_format($port, 2, ".", ""); 

			$temp = str_replace("#REF", $_SESSION['navig']->panier->tabarticle[$i]->produit->ref, $texte);
			$temp = str_replace("#TITRE", $produitdesc->titre, $temp);
			$temp = str_replace("#QUANTITE", "$quantite", $temp);
			$temp = str_replace("#PRODUIT", $produitdesc->produit, $temp);
			$temp = str_replace("#PRIXU", "$prix", $temp);
			$temp = str_replace("#TOTAL", "$total", $temp);			
			$temp = str_replace("#ID", $_SESSION['navig']->panier->tabarticle[$i]->produit->id, $temp);
			$temp = str_replace("#ARTICLE", "$i", $temp);
			$temp = str_replace("#PLUSURL", "panier.php?action=" . "modifier" . "&amp;" . "article=" . $i . "&amp;" . "quantite=" . $plus, $temp);			
			$temp = str_replace("#MOINSURL", "panier.php?action=" . "modifier" . "&amp;" . "article=" . $i . "&amp;" . "quantite=" . $moins, $temp);
			$temp = str_replace("#SUPPRURL", "panier.php?action=" . "supprimer" . "&amp;" . "article=" . $i, $temp);			
			$temp = str_replace("#PRODURL", "produit.php?ref=".$_SESSION['navig']->panier->tabarticle[$i]->produit->ref, $temp);		
			$temp = str_replace("#TOTSANSPORT", "$totsansport", $temp);
			$temp = str_replace("#PORT", "$port", $temp);
			$temp = str_replace("#TOTPORT", "$totcmdport", $temp);
			$temp = str_replace("#DECTEXTE", "$dectexte", $temp);
			$temp = str_replace("#DECVAL", "$decval", $temp);

			$res .= $temp;
		}
		
		return $res;
	
	}
	
		
	function boucleQuantite($texte, $args){
		// r�cup�ration des arguments

		$res="";
	
		$article = lireTag($args, "article");
		$max = lireTag($args, "max");
		
		$prodtemp = new Produit();
		$prodtemp->charger($_SESSION['navig']->panier->tabarticle[$article]->produit->ref);

		if($max == "") 
			$max = 10;
			
		$j = 0;
		
		for($i=1; $i<=$max; $i++){
			if($i==$_SESSION['navig']->panier->tabarticle[$article]->quantite) $selected="selected=\"selected\"";
			else $selected="";
		
			$temp = str_replace("#NUM", "$i", $texte);
			$temp = str_replace("#SELECTED", $selected, $temp);

			$res.="$temp"; 
		}
		
	
		return $res;
	
	}
		
	function boucleChemin($texte, $args){
		global $id_rubrique;

		// r�cup�ration des arguments

		$rubrique = lireTag($args, "rubrique");		
		$profondeur = lireTag($args, "profondeur");		
		$niveau = lireTag($args, "niveau");		
		
		if($rubrique=="") return "";

		$res="";
		
		$trubrique = new Rubrique();
		$trubrique->charger($rubrique);
		$trubriquedesc = new Rubriquedesc();
		
		$i =  0;
		
        if(! $trubrique->parent)
                return "";

        $rubtab = "";
        $tmp = new Rubrique();
        $tmp->charger($trubrique->parent);
        $rubtab[$i] = new Rubrique();
        $rubtab[$i++] = $tmp;

        while($tmp->parent != 0) {
                $tmp = new Rubrique();
                $tmp->charger($rubtab[$i-1]->parent);

                $rubtab[$i] = new Rubrique();
                $rubtab[$i++] = $tmp;
        }

        $compt = 0;
        
        for($i=count($rubtab)-1; $i>=0; $i--){
                        if($profondeur != "" && $compt==$profondeur) break;
                        if($niveau != "" && $niveau != $compt +1 ) { $compt++; continue; }          
                        $trubriquedesc->charger($rubtab[$i]->id, $_SESSION['navig']->lang);
                        $temp = str_replace("#ID", $rubtab[$i]->id, $texte);
                        $temp = str_replace("#TITRE", "$trubriquedesc->titre", $temp);
                        $temp = str_replace("#URL", "rubrique.php?id_rubrique=" . $rubtab[$i]->id, $temp);

                        $compt++;
                        
                        $res .= $temp;
        }
	
		return $res;
	
	}	

	
	function bouclePaiement($texte, $args){

		$res="";
		
		$id = lireTag($args, "id");		
		$nom = lireTag($args, "nom");		
		$exclusion = lireTag($args, "exclusion");		

		$search ="";
	
		// preparation de la requete
		if($id!="")  $search.=" and id=\"$id\"";
		if($nom!="")  $search.=" and nom=\"$nom\"";
		
		if($exclusion!=""){
			$liste="";
			$tabexcl = explode(",", $exclusion);
			for($i=0;$i<count($tabexcl);$i++)
				$liste .= "'" . $tabexcl[$i] . "'" . ",";
			if(substr($liste, strlen($liste)-1) == ",")
				$liste = substr($liste, 0, strlen($liste)-1);
				
			$search.=" and nom not in ($liste)";
		} 
		
		
 		$modules = new Modules();
		
		$query = "select * from $modules->table where type='1' and actif='1' $search order by classement";
		$resul = mysql_query($query, $modules->link);
		$nbres = mysql_numrows($resul);
	
		if(!$nbres) return "";
		

		while( $row = mysql_fetch_object($resul)){

			$modules = new Modules();
			$modules->charger_id($row->id);
			
			$nom = $modules->nom;
			$nom[0] = strtoupper($nom[0]);

			include_once("client/plugins/" . $modules->nom . "/$nom.class.php");
			$tmpobj = new $nom();
			
			$titre = $tmpobj->getTitre();
			$chapo = $tmpobj->getChapo();
			$description = $tmpobj->getDescription();
										
			$temp = str_replace("#ID", "$row->id", $texte);
			$temp = str_replace("#URLPAYER", "commande.php?action=paiement&amp;type_paiement=" . $row->id, $temp);
			$temp = str_replace("#LOGO", "client/plugins/" . "$row->nom" . "/logo.jpg", $temp);
			$temp = str_replace("#TITRE", $titre, $temp);
			$temp = str_replace("#CHAPO", $chapo, $temp);
			$temp = str_replace("#DESCRIPTION", $description, $temp);		
			$res .= $temp;
		}
	

		return $res;
	
	}	

	function bouclePays($texte, $args){


		$id = lireTag($args, "id");		
		$zone = lireTag($args, "zone");	 
		$zdefinie = lireTag($args, "zdefinie");
        $classement = lireTag($args, "classement");
        $select = lireTag($args, "select");
        $default = lireTag($args, "default");


		$search ="";
		$res="";
		
		// preparation de la requete
		if($id!="")  $search.=" and id=\"$id\"";
		if($zone!="")  $search.=" and zone=\"$zone\"";
		if($zdefinie!="") $search.=" and zone!=\"-1\"";
	
		if($_SESSION['navig']->lang == "") $lang=1; else $lang=$_SESSION['navig']->lang ;
		
		$pays = new Pays();
		$paysdesc = new Paysdesc();
	
		$query = "select * from $pays->table where 1 $search";
		$resul = mysql_query($query, $pays->link);

		$liste=""; 
		while( $row = mysql_fetch_object($resul))					
			 $liste .= "'$row->id', ";
			
		$liste = substr($liste, 0, strlen($liste) - 2);
	
		if(!$liste) $liste="''";
		
        $query = "select * from $paysdesc->table where pays in ($liste) and lang='$lang' order by titre";

		$resul = mysql_query($query, $paysdesc->link);
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";

		while( $row = mysql_fetch_object($resul)){
			$paysdesc->charger_id($row->id);
			$pays->charger($paysdesc->pays);
			$temp = str_replace("#ID", "$row->pays", $texte);
			$temp = str_replace("#TITRE", "$paysdesc->titre", $temp);
			$temp = str_replace("#CHAPO", "$paysdesc->chapo", $temp);
			$temp = str_replace("#DESCRIPTION", "$paysdesc->description", $temp);	
			if(($_SESSION['navig']->formcli->pays == $row->pays || $_SESSION['navig']->client->pays == $row->pays) && $select=="") 	
				$temp = str_replace("#SELECTED", "selected=\"selected\"", $temp);
			if($select !="" && $select == $row->pays) $temp = str_replace("#SELECTED", "selected=\"selected\"", $temp);	
			else $temp = str_replace("#SELECTED", "", $temp);
			if($default == "1" && $pays->default == "1") $temp = str_replace("#DEFAULT", "selected=\"selected\"", $temp);	
			else $temp = str_replace("#DEFAULT", "", $temp);
			$res .= $temp;
		}
	

		return $res;
	
	}	

	function boucleCaracteristique($texte, $args){

		global $caracteristique;
		
		$id = lireTag($args, "id");		
		$rubrique = lireTag($args, "rubrique");		
		$affiche = lireTag($args, "affiche");		
		$produit = lireTag($args, "produit");	
		$courante = lireTag($args, "courante");	
				
		$search ="";
		$res="";
		
		// preparation de la requete
		 
		if($produit!=""){
			$tprod = new Produit();
			$tprod->charger_id($produit);
			$rubrique = $tprod->rubrique;
		}

		if($rubrique!="")  $search.=" and rubrique=\"$rubrique\"";
		if($id!="")  $search.=" and id=\"$id\"";
		
		
		$rubcaracteristique = new Rubcaracteristique();
		$tmpcaracteristique = new Caracteristique();
		$tmpcaracteristiquedesc = new Caracteristiquedesc();
		
		
		$query = "select DISTINCT(caracteristique) from $rubcaracteristique->table where 1 $search";
		if($id != "") $query = "select * from $tmpcaracteristique->table where 1 $search";
		$resul = mysql_query($query, $rubcaracteristique->link);
	
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";

		while( $row = mysql_fetch_object($resul)){

			if($courante == "1" && ($id  != $caracteristique && ! strstr($caracteristique, $id . "-")))
			   continue;

			else if($courante == "0" && ($id  == $caracteristique || strstr($caracteristique, $id . "-")))
				 continue;
							
			if($id != "") $tmpcaracteristiquedesc->charger($row->id, $_SESSION['navig']->lang);
			else $tmpcaracteristiquedesc->charger($row->caracteristique, $_SESSION['navig']->lang);
			if($id != "") $temp = str_replace("#ID", "$row->id", $texte);
			else $temp = str_replace("#ID", "$row->caracteristique", $texte);

			$tmpcaracteristique->charger($tmpcaracteristiquedesc->caracteristique);
			
			if($tmpcaracteristique->affiche == "0" && $affiche == "1") continue;

			$temp = str_replace("#TITRE", "$tmpcaracteristiquedesc->titre", $temp);
			$temp = str_replace("#CHAPO", "$tmpcaracteristiquedesc->chapo", $temp);
			$temp = str_replace("#DESCRIPTION", "$tmpcaracteristiquedesc->description", $temp);		
			$temp = str_replace("#PRODUIT", "$produit", $temp);	
			
			$res .= $temp;
		}

		return $res;
	
	}	

	function boucleCaracdisp($texte, $args){

		global $caracdisp;
		
		$caracteristique = lireTag($args, "caracteristique");		
		$etcaracteristique = lireTag($args, "etcaracteristique");		
		$etcaracdisp = lireTag($args, "etcaracdisp");	
		$stockmini = lireTag($args, "stockmini");
		$courante = lireTag($args, "courante");
		
		$id = lireTag($args, "caracdisp");
		if($id == "")
			$id = lireTag($args, "id");
			
		$classement = lireTag($args, "classement");
		
		$idsave = $id;
		$liste="";
		$tabliste[0]="";		
		$res="";
		
		$caracteristiquesave = $caracteristique;
		
		if( (ereg( "^$caracteristique-", $etcaracteristique)) ||(ereg( "-$caracteristique-", $etcaracteristique)) ) $deja="1";
		else $deja="0";
		
		
		$search ="";
		
		// preparation de la requete
		if($caracteristique!="")  $search.=" and caracteristique=\"$caracteristique\"";
		if($id !="") $search.=" and id=\"$id\"";
		if($classement == "alpha") $order="order by titre";
		else if($classement == "alphainv") $order="order by titre desc";
		
		$tcaracdisp = new Caracdisp();
		$tcaracdispdesc = new Caracdispdesc();
		
		
		$query = "select * from $tcaracdisp->table where 1 $search";
		$resul = mysql_query($query, $tcaracdisp->link);

		$i=0;
				
		while($row = mysql_fetch_object($resul)){
				$liste .= "'" . $row->id . "',";
				$tabliste[$i++] = $row->id;
		}
			
		$liste = substr($liste, 0, strlen($liste) - 1);	

						
							
		if($classement != ""){
			$liste2="";
			$query = "select * from $tcaracdispdesc->table where caracdisp in ($liste) and lang='" . $_SESSION['navig']->lang . "' $order";
			$resul = mysql_query($query, $tcaracdispdesc->link);
					
		
		
			$i=0;
			
			while($row = mysql_fetch_object($resul)){
				$liste2 .= "'" . $row->caracdisp . "',";
				$tabliste2[$i++] = $row->caracdisp;
			}
			$liste2 = substr($liste2, 0, strlen($liste2) - 1);

		}
		
	
		if($classement != "" && isset($tabliste2)) $tabliste = $tabliste2;
		
		for($i=0; $i<count($tabliste); $i++){
			
			if($courante == "1" && ($id  != $caracdisp && ! strstr($caracdisp, "-" . $id )))
			   continue;
			
			else if($courante == "0" && ($id  == $caracdisp || strstr($caracdisp, "-" . $id)))
				 continue;
				
            if($stockmini != ""){
                  $caracvalch = new Caracval();
                  $querych = "select count(*) as nb from $caracvalch->table where caracdisp='" . $tabliste[$i] . "'";
                  $resulch = mysql_query($querych, $caracvalch->link);
                  if(mysql_result($resulch, 0, "nb")<$stockmini) continue;
            }
			$tcaracdispdesc->charger_caracdisp($tabliste[$i], $_SESSION['navig']->lang);
			$tcaracdisp->charger($tabliste[$i]);
			
			if(!$deja) $id=$tabliste[$i]."-"; else $id="";
			if(!$deja) $caracteristique=$tcaracdisp->caracteristique."-"; else $caracteristique ="";
			
			if($caracteristique == "$tcaracdisp->caracteristique" . "-" && $caracdisp == $tabliste[$i] . "-") 
				$selected = "selected=\"selected\""; else $selected = "";
				
			$temp = str_replace("#ID", $tcaracdisp->id, $texte);
			$temp = str_replace("#CARACTERISTIQUE", $tcaracdisp->caracteristique, $temp);			
			$temp = str_replace("#IDC", $id . $etcaracdisp, $temp);
			$temp = str_replace("#CARACTERISTIQUEC", $caracteristique . $etcaracteristique, $temp);
			$temp = str_replace("#TITRE", "$tcaracdispdesc->titre", $temp);
			$temp = str_replace("#SELECTED", "$selected", $temp);
			
			$res .= $temp;
		}
	
		
		return $res;
	
	
	}	
	
	function boucleCaracval($texte, $args){
		$produit = lireTag($args, "produit");
		$caracteristique = lireTag($args, "caracteristique");		
		$valeur = lireTag($args, "valeur");		

		if($produit == "" || $caracteristique == "") return "";
		
		if(substr($valeur, 0, 1) == "!") {
			$different=1;
			$valeur = substr($valeur, 1);
		}
		else $different=0;

		$search ="";
		$res="";
		
		// preparation de la requete
		$search.=" and caracteristique=\"$caracteristique\"";
		$search.=" and produit=\"$produit\"";
		
		$caracval = new Caracval();
		$prodtemp = new Produit();
		
		$query = "select * from $caracval->table where 1 $search";
		$resul = mysql_query($query, $caracval->link);

		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";

	
		while( $row = mysql_fetch_object($resul)){

			$temp = str_replace("#ID", $row->id, $texte);
				$temp = str_replace("#CARACDISP", $row->caracdisp, $temp);
				if($row->caracdisp != 0){
					$caracdispdesc = new Caracdispdesc();
					$caracdispdesc->charger_caracdisp($row->caracdisp, $_SESSION['navig']->lang);
					if($valeur != "" && (($different == 0 && $caracdispdesc->caracdisp != $valeur) || ($different == 1 && $caracdispdesc->caracdisp == $valeur))) continue;
					$temp = str_replace("#VALEUR", $caracdispdesc->titre, $temp);
					
				}
				
				else {
					if($valeur != "" && (($different == 0 && $row->valeur != $valeur) || ($different == 1 && $row->valeur == $valeur))) continue;
					if( $row->valeur=="") continue;
					$temp = str_replace("#VALEUR", $row->valeur, $temp);
				}
			
			$prodtemp->charger_id($produit);
			$temp = str_replace("#RUBRIQUE", $prodtemp->rubrique, $temp);
			
			$caractemp = new Caracteristiquedesc();
			$caractemp ->charger($row->caracteristique,  $_SESSION['navig']->lang);
		
			$temp = str_replace("#TITRECARAC", $caractemp->titre, $temp);
			
				
			$res .= $temp;
		}
	
	
		return $res;
	
	}		
			
	function boucleAdresse($texte, $args){
	
		$adresse = new Adresse();
	

		// r�cup�ration des arguments

		$adresse_id = lireTag($args, "adresse");		
		$client_id = lireTag($args, "client");
		$defaut = lireTag($args, "defaut");
		
		
		$search ="";
		$res="";
		
		$raison[1] = "Mme";
		$raison[2] = "Mlle";
		$raison[3] = "M";
				
		// preparation de la requete
		if($adresse_id!="")  $search.=" and id=\"$adresse_id\"";
		if($client_id!="")  $search.=" and client=\"$client_id\"";
		
		if($defaut =="1" && $adresse_id != "0")
			return "";
		
		else if($defaut =="0" && $adresse_id == "0")
			return "";
			
		if($adresse_id != "0" ) {
			$query = "select * from $adresse->table where 1 $search";
			$resul = mysql_query($query, $adresse->link);
	
			$nbres = mysql_numrows($resul);
			if(!$nbres) return "";
			

			while( $row = mysql_fetch_object($resul)){
			
                if($row->raison == 1) $raison1f="selected=\"selected\"";
                else $raison1f="";

                if($row->raison == 2) $raison2f="selected=\"selected\"";
                else $raison2f="";

                if($row->raison == 3) $raison3f="selected=\"selected\"";
                else $raison3f="";			
			
			
				$temp = str_replace("#ID", "$row->id", $texte);
				$temp = str_replace("#PRENOM", "$row->prenom", $temp);
				$temp = str_replace("#NOM", "$row->nom", $temp);
     		    $temp = str_replace("#RAISON1F", "$raison1f", $temp);
       		    $temp = str_replace("#RAISON2F", "$raison2f", $temp);
       		    $temp = str_replace("#RAISON3F", "$raison3f", $temp);				
				$temp = str_replace("#RAISON", $raison[$row->raison], $temp);
				$temp = str_replace("#LIBELLE", "$row->libelle", $temp);
				$temp = str_replace("#ADRESSE1", "$row->adresse1", $temp);
				$temp = str_replace("#ADRESSE2", "$row->adresse2", $temp);
				$temp = str_replace("#ADRESSE3", "$row->adresse3", $temp);
				$temp = str_replace("#CPOSTAL", "$row->cpostal", $temp);
				$temp = str_replace("#PAYS", "$row->pays", $temp);
				$temp = str_replace("#VILLE", "$row->ville", $temp);
				$temp = str_replace("#TEL", "$row->tel", $temp);
				$temp = str_replace("#SUPPRURL", "livraison_adresse.php?action=supprimerlivraison&amp;id=$row->id", $temp);
				$temp = str_replace("#URL", "commande.php?action=modadresse&amp;adresse=$row->id", $temp);

				$res .= $temp;
			}
	
		
		}
		
		else {
		
		$raison[1] = "Mme";
		$raison[2] = "Mlle";
		$raison[3] = "M";

                if($_SESSION['navig']->client->raison == 1) $raison1f="selected=\"selected\"";
                else $raison1f="";

                if($_SESSION['navig']->client->raison == 2) $raison2f="selected=\"selected\"";
                else $raison2f="";

                if($_SESSION['navig']->client->raison == 3) $raison3f="selected=\"selected\"";
                else $raison3f="";

        $temp = str_replace("#RAISON1F", "$raison1f", $texte);
        $temp = str_replace("#RAISON2F", "$raison2f", $temp);
        $temp = str_replace("#RAISON3F", "$raison3f", $temp);
		
		$temp = str_replace("#ID", $_SESSION['navig']->client->id, $temp);
		$temp = str_replace("#LIBELLE", "", $temp);
		$temp = str_replace("#RAISON", $raison[$_SESSION['navig']->client->raison], $temp);
		$temp = str_replace("#NOM", $_SESSION['navig']->client->nom, $temp);
		$temp = str_replace("#PRENOM", $_SESSION['navig']->client->prenom, $temp);
		$temp = str_replace("#ADRESSE1", $_SESSION['navig']->client->adresse1, $temp);
		$temp = str_replace("#ADRESSE2", $_SESSION['navig']->client->adresse2, $temp);
		$temp = str_replace("#ADRESSE3", $_SESSION['navig']->client->adresse3, $temp);
		$temp = str_replace("#CPOSTAL", $_SESSION['navig']->client->cpostal, $temp);
		$temp = str_replace("#VILLE", strtoupper($_SESSION['navig']->client->ville), $temp);
		$temp = str_replace("#PAYS", strtoupper($_SESSION['navig']->client->pays), $temp);
		$temp = str_replace("#EMAIL", $_SESSION['navig']->client->email, $temp);
		$temp = str_replace("#TELFIXE", $_SESSION['navig']->client->telfixe, $temp);
		$temp = str_replace("#TELPORT", $_SESSION['navig']->client->telport, $temp);		
		
		$res .= $temp;
		
		}
		
		return $res;
	
	}		
	

	function boucleCommande($texte, $args){
	
		$commande = new Commande();
	
	
		// r�cup�ration des arguments
		$commande_id = lireTag($args, "id");		
		$commande_ref = lireTag($args, "ref");		
		$client_id = lireTag($args, "client");
		$statut = lireTag($args, "statut");
		$classement = lireTag($args, "classement");
		
		$search ="";
		$order="";
		$res="";
		
		// preparation de la requete
		if($commande_id!="")  $search.=" and id=\"$commande_id\"";		
		if($commande_ref!="")  $search.=" and ref=\"$commande_ref\"";
		if($client_id!="")  $search.=" and client=\"$client_id\"";
		if($statut!="" && $statut!="paye")  $search.=" and statut=\"$statut\"";
		else if($statut=="paye")  $search.=" and statut>\"1\"";

		if($classement == "inverse")
			$order = "order by date";
		else $order = "order by date desc";
	
		$query = "select * from $commande->table where 1 $search $order";
		$resul = mysql_query($query, $commande->link);
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";
		
		
		$statutdesc = new Statutdesc();
		$venteprod = new Venteprod();
		
		while( $row = mysql_fetch_object($resul)){
		  	
		  	$jour = substr($row->date, 8, 2);
  			$mois = substr($row->date, 5, 2);
  			$annee = substr($row->date, 0, 4);
  		
  			$heure = substr($row->date, 11, 2);
  			$minute = substr($row->date, 14, 2);
  			$seconde = substr($row->date, 17, 2);

			$jour_livraison = substr($row->datelivraison, 8, 2);
			$mois_livraison = substr($row->datelivraison, 5, 2);
			$annee_livraison = substr($row->datelivraison, 0, 4);
	
			$datelivraison = $jour_livraison . "/" . $mois_livraison . "/" . $annee_livraison;
	
			  		
  			$query2 = "SELECT sum(prixu*quantite) as total FROM $venteprod->table where commande='$row->id'"; 
  			$resul2 = mysql_query($query2, $venteprod->link);
  			$total = round(mysql_result($resul2, 0, "total"), 2);
  			$total = round($total - $row->remise, 2);

			$port = $row->port;
			$totcmdport = $row->port + $total;
			 	  	
		  	$statutdesc->charger($row->statut, $_SESSION['navig']->lang);

			$temp = str_replace("#ID", "$row->id", $texte);
			$temp = str_replace("#ADRESSE", "$row->adresse", $temp);
			$temp = str_replace("#DATELIVRAISON", "$datelivraison", $temp);
			$temp = str_replace("#DATE", $jour . "/" . $mois . "/" . $annee, $temp);
			$temp = str_replace("#REF", "$row->ref", $temp);
			$temp = str_replace("#LIVRAISON", "$row->livraison", $temp);
			$temp = str_replace("#FACTURE", "$row->facture", $temp);
			$temp = str_replace("#PAIEMENT", "$row->paiement", $temp);
			$temp = str_replace("#REMISE", "$row->remise", $temp);
			$temp = str_replace("#STATUT", "$statutdesc->titre", $temp);
			$temp = str_replace("#TOTALCMD", "$total", $temp);
			$temp = str_replace("#PORT", "$port", $temp);
			$temp = str_replace("#TOTCMDPORT", "$totcmdport", $temp);
			$temp = str_replace("#COLIS", "$row->colis", $temp);
			$temp = str_replace("#FICHIER", "client/pdf/visudoc.php?ref=" . $row->ref, $temp);

			$res .= $temp;
		}
	


		return $res;
	
	}	
	
	function boucleVenteprod($texte, $args){	
	
		// r�cup�ration des arguments
		$commande_id = lireTag($args, "commande");		
		$produit = lireTag($args, "produit");
		$stat = lireTag($args, "stat");
		
		$search ="";
		$res="";
		
		// preparation de la requete
		if($commande_id!="")  $search.=" and commande=\"$commande_id\"";		
		if($produit!="")  $search.=" and ref=\"$produit\"";		
	
		$venteprod = new Venteprod();

		$query = "select * from $venteprod->table where 1 $search";
		$resul = mysql_query($query, $venteprod->link);

		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";
		
		
		while( $row = mysql_fetch_object($resul)){
			
			$prixu = number_format($row->prixu, 2, ".", "");
			$totalprod = $row->prixu * $row->quantite;
			$totalprod = number_format($totalprod, 2, ".", "");
			
			$query2 = "select count(*) as nbvente from $venteprod->table where ref=\"" . $row->ref . "\"";
			$resul2 = mysql_query($query2, $venteprod->link);
			$nbvente = mysql_result($resul2, 0, "nbvente");
			
			$temp = str_replace("#ID", "$row->id", $texte);
			$temp = str_replace("#COMMANDE", "$row->commande", $temp);
			$temp = str_replace("#REF", "$row->ref", $temp);
			$temp = str_replace("#TITRE", "$row->titre", $temp);
			$temp = str_replace("#CHAPO", "$row->chapo", $temp);
			$temp = str_replace("#DESCRIPTION", "$row->description", $temp);
			$temp = str_replace("#QUANTITE", "$row->quantite", $temp);
			$temp = str_replace("#PRIXU", "$row->prixu", $temp);
			$temp = str_replace("#TVA", "$row->tva", $temp);	
			$temp = str_replace("#TOTALPROD", "$totalprod", $temp);

			$res .= $temp;
		}
	


		return $res;
	
	}	

	function boucleTransport($texte, $args){	

		// r�cup�ration des arguments

		$id = lireTag($args, "id");		

	
		$res="";
		
		$modules = new Modules();
	
		$query = "select * from $modules->table where type='2' and actif='1' order by classement";

		$resul = mysql_query($query, $modules->link);
		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";

		$pays = new Pays();
		
		if($_SESSION['navig']->adresse != "" && $_SESSION['navig']->adresse != 0){
			$adr = new Adresse();
			$adr->charger($_SESSION['navig']->adresse);
			$pays->charger($adr->pays);
		}	
			
		else 
			$pays->charger($_SESSION['navig']->client->pays);

		$transzone = new Transzone();
		
		   while( $row = mysql_fetch_object($resul)){
		
		  	 if( ! $transzone->charger($row->id, $pays->zone)) continue;
	
			$modules = new Modules();
			$modules->charger_id($row->id);
			
			$nom = $modules->nom;
			$nom[0] = strtoupper($nom[0]);

			include_once("client/plugins/" . $modules->nom . "/$nom.class.php");
			$tmpobj = new $nom();
			
			$port = round(port($row->id), 2);
			$titre = $tmpobj->getTitre();
			$chapo = $tmpobj->getChapo();
			$description = $tmpobj->getDescription();
	
			$port = number_format($port, 2, ".", ""); 
			
			$temp = str_replace("#TITRE", "$titre", $texte);
			$temp = str_replace("#CHAPO", "$chapo", $temp);
			$temp = str_replace("#DESCRIPTION", "$description", $temp);
			$temp = str_replace("#URLCMD", "spip.php?page=commande&thelia_action=transport&amp;id=" . $row->id, $temp);
			$temp = str_replace("#ID", "$row->id", $temp);	
			$temp = str_replace("#PORT", "$port", $temp);
			$res .= $temp;
			
		}
	
	
			return $res;

	}	


        function boucleRSS($texte, $args){

		$cache = new Cache();
		$cache->vider("RSS", "%");
			
		@ini_set('default_socket_timeout', 5);
                
		// r�cup�ration des arguments
                $url = lireTag($args, "url");
                $nb = lireTag($args, "nb");
				$deb = lireTag($args, "deb");
				
		if($url == "") return;

		$i=0;
		$compt=0;
                $rss = @fetch_rss( $url );
		if(!$rss) return "";

                $chantitle = $rss->channel['title'];
		$chanlink = $rss->channel['link'];
		
                $items = array_slice($rss->items, 0);
				
                foreach ($items as $item) {
                   if($compt<$deb) {$compt++; continue;}
                  
                    $title = strip_tags($item['title']);
                 	$description = strip_tags($item['description']);
                    $author = $item['dc']['creator'];
                    $link = $item['link']; 
					$dateh = $item['dc']['date'];
			$jour = substr($dateh, 8,2);
			$mois = substr($dateh, 5, 2);
			$annee = substr($dateh, 0, 4);

			$heure = substr($dateh, 11, 2);
			$minute = substr($dateh, 14, 2);
			$seconde = substr($dateh, 17, 2);
				
			$temp =  str_replace("#SALON", "$chantitle", $texte);
			$temp = str_replace("#WEB", "$chanlink", $temp);			
			$temp = str_replace("#TITRE", "$title", $temp);
			$temp = str_replace("#LIEN", "$link", $temp);
			$temp = str_replace("#DESCRIPTION", "$description", $temp);
            $temp = str_replace("#AUTEUR", "$author", $temp);
			$temp = str_replace("#DATE", "$jour/$mois/$annee", $temp);
			$temp = str_replace("#HEURE", "$heure:$minute:$seconde", $temp);
			
			$i++;

			$res .= $temp;
			if($i == $nb) return $res;
                }

                return $res;

        }


	
	function boucleDeclinaison($texte, $args){

		global $declinaison;

		$id = lireTag($args, "id");		
		$rubrique = lireTag($args, "rubrique");		
		$produit = lireTag($args, "produit");		
		$courante = lireTag($args, "courante");	
		
		$search ="";
		$res="";
		
		// preparation de la requete
		if($rubrique!="")  $search.=" and rubrique=\"$rubrique\"";
		if($id!="")  $search.=" and id=\"$id\"";
			
		$rubdeclinaison = new Rubdeclinaison();
		$tmpdeclinaison = new Declinaison();
		$tmpdeclinaisondesc = new Declinaisondesc();
		
		
		$query = "select DISTINCT(declinaison) from $rubdeclinaison->table where 1 $search";
		if($id != "") $query = "select * from $tmpdeclinaison->table where 1 $search";
		$resul = mysql_query($query, $rubdeclinaison->link);

		$nbres = mysql_numrows($resul);
		if(!$nbres) return "";

		while( $row = mysql_fetch_object($resul)){
			
			if($courante == "1" && ($row->id  != $declinaison))
			   continue;
			
			else if($courante == "0" && ($row->id  == $declinaison))
			   continue;
						
			if($id != "") $tmpdeclinaisondesc->charger($row->id, $_SESSION['navig']->lang);
			else $tmpdeclinaisondesc->charger($row->declinaison, $_SESSION['navig']->lang);
			if($id != "") $temp = str_replace("#ID", "$row->id", $texte);
			else $temp = str_replace("#ID", "$row->declinaison", $texte);

			$temp = str_replace("#TITRE", "$tmpdeclinaisondesc->titre", $temp);
			$temp = str_replace("#CHAPO", "$tmpdeclinaisondesc->chapo", $temp);
			$temp = str_replace("#DESCRIPTION", "$tmpdeclinaisondesc->description", $temp);	
			$temp = str_replace("#PRODUIT", "$produit", $temp);
	
			$res .= $temp;
		}

		return $res;
	}	

	function boucleDeclidisp($texte, $args){

		global $declidisp;
		
		$declinaison = lireTag($args, "declinaison");		
		$id = lireTag($args, "id");
		$produit = lireTag($args, "produit");
		$classement = lireTag($args, "classement");
		$stockmini = lireTag($args, "stockmini");
		$courante = lireTag($args, "courante");
		
		$search ="";
		$liste="";
		$tabliste[0]="";
		$res="";
		
		// preparation de la requete
		if($declinaison!="")  $search.=" and declinaison=\"$declinaison\"";
		if($id !="") $search.=" and id=\"$id\"";
		$tdeclidisp = new Declidisp();
		$tdeclidispdesc = new Declidispdesc();
	
		$exdecprod = new Exdecprod();

		if($classement == "alpha") $order="order by titre";
		else if($classement == "alphainv") $order="order by titre desc";

		$query = "select * from $tdeclidisp->table where 1 $search";
		$resul = mysql_query($query, $tdeclidisp->link);
		
		$i=0;
				
		while($row = mysql_fetch_object($resul)){
			
				if($stockmini && $produit){
					$stock = new Stock();
					$stock->charger($row->id, $produit);
					if($stock->valeur<$stockmini) continue;
					
				}
			
				$liste .= "'" . $row->id . "',";
				$tabliste[$i++] = $row->id;
		}
			
		$liste = substr($liste, 0, strlen($liste) - 1);	

						
							
		if($classement != ""){
			$liste2="";
			if($liste != ""){
				$query = "select * from $tdeclidispdesc->table where declidisp in ($liste) and lang='" . $_SESSION['navig']->lang . "' $order";
				$resul = mysql_query($query, $tdeclidispdesc->link);
			}		
		
		
			$i=0;
			
			while($row = mysql_fetch_object($resul)){
				$liste2 .= "'" . $row->declidisp . "',";
				$tabliste2[$i++] = $row->declidisp;
			}
			$liste2 = substr($liste2, 0, strlen($liste2) - 1);

		}
		
	
		if($classement != "" && isset($tabliste2)) $tabliste = $tabliste2;
		
	
		for($i=0; $i<count($tabliste); $i++){
		
			if($courante == "1" && ($tabliste[$i] . "-"  != $declidisp))
			   continue;
			
			else if($courante == "0" && ($tabliste[$i] ."-"  == $declidisp))
			   continue;
				
			if($exdecprod->charger($produit, $tabliste[$i])) continue;		
			
			$tdeclidisp = new Declidisp();
			$tdeclidisp->charger($tabliste[$i]); 
			
			$tdeclidispdesc = new Declidispdesc();
			$tdeclidispdesc->charger_declidisp($tabliste[$i], $_SESSION['navig']->lang);
			if(! $tdeclidispdesc->titre) $tdeclidispdesc->charger_declidisp($tabliste[$i]);
			$temp = str_replace("#ID", $tdeclidispdesc->declidisp, $texte);
			$temp = str_replace("#DECLINAISON", $tdeclidisp->declinaison, $temp);
			$temp = str_replace("#TITRE", "$tdeclidispdesc->titre", $temp);
			$temp = str_replace("#PRODUIT", "$produit", $temp);

			$res .= $temp;
		}
	
	
		return $res;
	
	
	}	

	function boucleStock($texte, $args){

	
		$declidisp = lireTag($args, "declidisp");
		$produit = lireTag($args, "produit");
		
		if($declidisp == "" || $produit == "") return "";
		
		$stock = new Stock();		
		$stock->charger($declidisp, $produit);
				
		$temp = str_replace("#ID", "$stock->id", $texte);
		$temp = str_replace("#DECLIDISP", "$declidisp", $temp);	
		$temp = str_replace("#PRODUIT", "$produit", $temp);
		$temp = str_replace("#VALEUR", "$stock->valeur", $temp);	
			
			
		$compt ++;
			
		if(trim($temp) !="") $res .= $temp;
	
		return $res;
		
	
	}

	function boucleDecval($texte, $args){

	
		$article = lireTag($args, "article");
		
		if($article == "") return "";
		
		$res = "";
		
		$declinaison = new Declinaison();
		$declinaisondesc = new Declinaisondesc();
		$declidisp = new Declidisp();
		$declidispdesc = new Declidispdesc();
		
		for($compt = 0; $compt<count($_SESSION['navig']->panier->tabarticle[$article]->perso); $compt++){
		   	$tperso = $_SESSION['navig']->panier->tabarticle[$article]->perso[$compt];
			$declinaison->charger($tperso->declinaison);
			$declinaisondesc->charger($declinaison->id, $_SESSION['navig']->lang);
			// recup valeur declidisp ou string
			if($declinaison->isDeclidisp($tperso->declinaison)){
				$declidisp->charger($tperso->valeur);
				$declidispdesc->charger_declidisp($declidisp->id, $_SESSION['navig']->lang);
				$valeur = $declidispdesc->titre;
			}
				
			else $valeur = $tperso->valeur;

			$temp = str_replace("#DECLITITRE", "$declinaisondesc->titre", $texte);
			$temp = str_replace("#VALEUR", "$valeur", $temp);	
			$temp = str_replace("#DECLIDISP", "$declidispdesc->declidisp", $temp);	
			
			$res .= $temp;				
		}		
	


		return $res;
			
	}

?>
