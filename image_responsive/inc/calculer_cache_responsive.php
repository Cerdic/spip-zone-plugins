<?php

function image_responsive_calculer_cache() {
		$now = time();

		$supprimer = _request("supprimer");

		$ages = array(3, 7, 15, 30, 92);
		$tailles = $durees = array();
		$tailles["tout"] = 0;

		foreach ($ages as $age) {
			$durees[$age] = 60*60*24*$age;
			$tailles[$age] = 0;
		} 

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

							if ($supprimer == "tout") {
								@unlink($fichier);
							} else {
								$tailles["tout"] += $fsize;
							}

							if ($fat) {
								$age = $now - $fat;

								foreach ($durees as $jours => $duree) {
									if ($age > $duree) {
										if ($supprimer == $jours) {
											@unlink($fichier);
										} else {
											$tailles[$jours] += $fsize;
										}
									}
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
			echo "<tr><td><b>Toutes les images</b></td> <td> ".taille_en_octets($tailles["tout"])."</td>$supp</tr>";
			
			if ($tailles["3"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=3'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis 3 jours</td> <td> ".taille_en_octets($tailles["3"])."</td>$supp</tr>";
			}
			if ($tailles["7"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=7'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis une semaine</td> <td> ".taille_en_octets($tailles["7"])."</td>$supp</tr>";
			}
			if ($tailles["15"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=15'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis 15 jours</td> <td> ".taille_en_octets($tailles["15"])."</td>$supp</tr>";
			}
			if ($tailles["30"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=30'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis un mois</td> <td> ".taille_en_octets($tailles["30"])."</td>$supp</tr>";
			}
			if ($tailles["92"]) {
				$supp = "<td><a href='?exec=admin_vider&amp;action=calculer_taille_cache_responsive&amp;supprimer=92'>supprimer</a></td>";
				echo "<tr><td>Non vues depuis trois mois</td> <td> ".taille_en_octets($tailles["92"])."</td>$supp</tr>";
			}
			echo "</table>";
		} else {
			header("Location:index.php?exec=admin_vider");
			exit;
		}
}
