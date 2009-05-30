<?php
/*****************************************************************************\
* SPIP-CARTO, Solution de partage et d'elaboration d'information 
* (Carto)Graphique sous SPIP
*
* Copyright (c) 2005-2006
*
* Stephane Laurent, Franeois-Xavier Prunayre, Pierre Giraud, Jean-Claude 
* Moissinac et tous les membres du projet SPIP-CARTO V1 (Annie Danzart - Arnaud
* Fontaine - Arnaud Saint Leger - Benoit Veler - Christine Potier - Christophe 
* Betin - Daniel Faivre - David Delon - David Jonglez - Eric Guichard - Jacques
* Chatignoux - Julien Custot - Laurent Jegou - Mathieu Gehin - Michel Briand - 
* Mose - Olivier Frerot - Philippe Fournel - Thierry Joliveau)
* 
* voir : http://www.geolibre.net/article.php3?id_article=16
*
* Ce programme est un logiciel libre distribue sous licence GNU/GPL. 
* Pour plus de details voir le fichier COPYING.txt ou leaide en ligne.
* 
e -
This program is free software ; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation ; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY ; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program (COPYING.txt) ; if not, write to
the Free Software Foundation, Inc.,
59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
or check http://www.gnu.org/copyleft/gpl.html
e -
*
\***************************************************************************/
// Ce fichier ne sera execute qu'une fois
if (defined("_ECRIRE_INC_CARTE_IMPORT")) return;
define("_ECRIRE_INC_CARTE_IMPORT", "1");
	
function avertissement_carto_import() {
	global $spip_lang_right, $spip_lang_left;
	debut_boite_info();

	echo "<div class='verdana2' align='justify'>".
			"<img src='" . _DIR_IMG_PACK . "warning.gif' alt='' width='48' height='48' />".
			"<p align='center'><B>"._T('spipcarto:import_warning')."</B></p>";
	echo "</div>";

	fin_boite_info();
	echo "<p>&nbsp;<p>";
}
	
	
	
//
// Retourner le code HTML d'utilisation de fichiers uploades a la main
// Repris de inc_document avec ajout d'un filtre sur les extensions 

function texte_upload_file($dir, $inclus = '', $extw = '') {
	$fichiers = preg_files($dir);
	$exts = array();

	while (list(, $f) = each($fichiers)) {
		$f = ereg_replace("^$dir/","",$f);
		if (ereg("\.([^.]+)$", $f, $match)) {
			$ext = strtolower($match[1]);
			
			if (!$exts[$ext]) {
				if ($ext == $extw || $extw == '') {
					if ($ext == 'jpeg') $ext = 'jpg';
					$req = "SELECT extension FROM spip_types_documents WHERE extension='$ext'";
					if ($inclus) $req .= " AND inclus='$inclus'";
					if (@spip_fetch_array(spip_query($req))) $exts[$ext] = 'oui';
					else $exts[$ext] = 'non';
				}
			}
			
			$ledossier = substr($f, 0, strrpos($f,"/"));
			if (strlen($ledossier) > 0) $ledossier = "$ledossier";
			$lefichier = substr($f, strrpos($f, "/"), strlen($f));
			
			if ($ledossier != $ledossier_prec) {
				$texte_upload .= "\n<option value=\"$ledossier\" style='font-weight: bold;'>Tout le dossier $ledossier</option>";
			}
			
			$ledossier_prec = $ledossier;
			
			if ($exts[$ext] == 'oui') $texte_upload .= "\n<option value=\"$f\">&nbsp; &nbsp; &nbsp; &nbsp; $lefichier</option>";
		}
	}
	return $texte_upload;
}	
	
		
class csvUtil {
    /*
     * Constructor
     */
     function csvUtil($file, $separator) {
            $this->file = $file;
            $this->separator = $separator;
            $this->readArray();
    }

	/*
	 * readArray(): reads $this->file with $this->separator
	 *           and stores its content in an array
	 */
    function readArray() {
            $handle = fopen ($this->file, "r");
            $i = 0;
            do {
                    $this->buffer[$i] = fgets($handle);
                    $this->buffer[$i] = explode($this->separator, $this->buffer[$i]);
                    $i++;
            } while (!feof ($handle));
            fclose ($handle);
    }
	/*
	 *getField($row, $col): find and return content of a give position
	 */
    function getField($row, $col) {
            $retval = $this->buffer[$row][$col];
            return $retval;
    }

    /*
     *search($col, $expression): search for a value in given column
     *returns an array with found rows
     */
    function search($col, $expression) {
            $i = 0;
            $j = 0;
            do {
                    if (@eregi($expression,$this->buffer[$i][$col])) {
                            $retval[$j] = $i;
                            $j++;
                    }
                    $i++;
            } while ($this->buffer[$i][0]);

            return $retval;
    }

	/*
	 * numRows(): returns number of rows
	 */
    function numRows() {
            $retval = count($this->buffer);
            return $retval;
    }
	/*
	 *  numCols():  returns number of cols
	 */
    function numCols() {
            $retval = count($this->buffer[0]);
            return $retval;
    }
}
?>