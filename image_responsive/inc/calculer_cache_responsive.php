<?php

function image_responsive_calculer_cache() {
		$now = time();
		
		$supprimer = $_GET["supprimer"];
		
		$duree["3"] = 60*60*24*3;
		$duree["7"] = 60*60*24*7;
		$duree["15"] = 60*60*24*15;
		$duree["30"] = 60*60*24*30;
		$duree["92"] = 60*60*24*90;
		
		
	
		$base = sous_repertoire(_DIR_VAR, "cache-responsive");
		$d = dir($base);
	
		while (false !== ($entry = $d->read())) {
			$sousdir = "$base$entry";
			if (substr($entry, 0, 1) != "." &&  is_dir($sousdir)) {
			
				$dd = dir($sousdir);
				while (false !== ($f = $dd->read())) {
					if (substr($f, 0, 1) != ".") {
						$fichier = "$sousdir/$f";
						
						//echo "<li><a href='$fichier'>$fichier</a>";
						if (file_exists($fichier)) {
							$fsize = filesize($fichier);
							$fat = @fileatime($fichier);
							if ($supprimer == "tout") @unlink($fichier);
							else $taille["tout"] += $fsize;
							

							
							if ($fat) {
								$age = $now - $fat;
							
								if ($age > $duree["3"]) {
									if ($supprimer == "3") @unlink($fichier);
									else $taille["3"] += $fsize;
								}
								if ($age > $duree["7"]) {
									if ($supprimer == "7") @unlink($fichier);
									else $taille["7"] += $fsize;
								}
								if ($age > $duree["15"]) {
									if ($supprimer == "15") @unlink($fichier);
									else $taille["15"] += $fsize;
								}
								if ($age > $duree["30"]) {
									if ($supprimer == "30") @unlink($fichier);
									else $taille["30"] += $fsize;
								}
								if ($age > $duree["92"]) {
									if ($supprimer == "92") @unlink($fichier);
									else $taille["92"] += $fsize;
								}
							}
						}
					}
				}
				$dd->close();
			}
		
		}
		$d->close();

		include_spip("inc/filtres");
		
		
		if (!$supprimer) {
			echo "<table>";
			$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=tout'>supprimer</a></td>";
			echo "<tr><td><b>Toutes les images</b></td> <td> ".taille_en_octets($taille["tout"])."</td>$supp</tr>";
			
			if ($taille["3"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=3'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis 3 jours</td> <td> ".taille_en_octets($taille["3"])."</td>$supp</tr>";
			}
			if ($taille["7"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=7'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis une semaine</td> <td> ".taille_en_octets($taille["7"])."</td>$supp</tr>";
			}
			if ($taille["15"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=15'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis 15 jours</td> <td> ".taille_en_octets($taille["15"])."</td>$supp</tr>";
			}
			if ($taille["30"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=30'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis un mois</td> <td> ".taille_en_octets($taille["30"])."</td>$supp</tr>";
			}
			if ($taille["92"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=92'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis trois mois</td> <td> ".taille_en_octets($taille["92"])."</td>$supp</tr>";
			}
			echo "</table>";
		} else {
			header("Location:index.php?exec=admin_vider");
			exit;
		}
}
