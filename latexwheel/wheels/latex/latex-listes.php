<?php
# not usefull as this file is include by the engine itself
# require_once 'engine/textwheel.php';
function latex_traiter_list($t){
	var_dump($t);
	return $t;	
}

function latex_liste_init($t){
	return latex_liste_item($t,'init');
}

function latex_liste_close($t){
	return latex_liste_item($t,'close');
}

function latex_liste_item($t,$quoi='item'){
	global $class_spip, $class_spip_plus;
	static $niveau;
	static $pile_li;
	static $pile_type;
	static $type;
	switch ($quoi){
		case 'init':
			$niveau = 0;
			$pile_li = array();
			$pile_type = array();
			$type = '';
			break;
		case 'close':
			// retour sur terre
			$ajout = '';
			while ($niveau > 0) {
				$ajout .= $pile_li[$niveau];
				$ajout .= $pile_type[$niveau];
				$niveau --;
			}
			$t .= $ajout;
			break;

		case 'ul':
		case 'ol':
			$nouv_type = $quoi;
			break;
		
		case 'item':
		default:
			if ($l=strlen($t[2])) {$profond=$l;$nouv_type='itemize';}
			elseif ($l=strlen($t[3])) {$profond=$l;$nouv_type='enumerate';}

			if ($profond > 0) {
				$ajout='';

				// changement de type de liste au meme niveau : il faut
				// descendre un niveau plus bas, fermer ce niveau, et
				// remonter
				$change_type = ($type AND ($type <> $nouv_type) AND ($profond == $niveau)) ? 1 : 0;
				$type = $nouv_type;

				// d'abord traiter les descentes
				while ($niveau > $profond - $change_type) {
					$ajout .= $pile_li[$niveau];
					$ajout .= $pile_type[$niveau];
					if (!$change_type)
						unset ($pile_li[$niveau]);
					$niveau --;
				}

				// puis les identites (y compris en fin de descente)
				if ($niveau == $profond && !$change_type) {
					$ajout .= $pile_li[$niveau];
				}

				// puis les montees (y compris apres une descente un cran trop bas)
				while ($niveau < $profond) {
					if ($niveau == 0)
						$ajout .= "\n\n";
					elseif (!isset($pile_li[$niveau])) {
						$ajout .= "\begin/debutitemize/fin";
						$pile_li[$niveau] = "\n\end/debutitemize/fin\n";
					}
					$niveau ++;
					$ajout .= "\begin/debut$type/fin";
					$pile_type[$niveau] = "\n\end/debut$type/fin\n";
				}

				$ajout .= "\n\item ";
				$pile_li[$profond] = "";
			}
			else {
				$ajout = $t[1];	// puce normale ou <hr>
			}

			$t = $ajout . $t[4];
			break;
	}

	return $t;
}