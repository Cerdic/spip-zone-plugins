<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service SIL.
 *
 * @package SPIP\CODELANG\SERVICES\SIL
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS['sil_service'] = array(
	'tables'	=> array('iso639codes', 'iso639names', 'iso639macros', 'iso639retirements'),
	'fields'	=> 	array(
		'iso639codes' => array(
			'Id'           => 'code_639_3',
			'Part2B' 		=> 'code_639_2b',
			'Part2T' 		=> 'code_639_2t',
			'Part1' 		=> 'code_639_1',
			'Scope' 		=> 'scope',
			'Language_Type' => 'type',
			'Ref_Name' 		=> 'ref_name',
			'Comment' 		=> 'comment'
		),
		'iso639names' => array(
			'Id' 			=> 'code_639_3',
			'Print_Name' 	=> 'print_name',
			'Inverted_Name' => 'inverted_name'
		),
		'iso639macros' => array(
			'M_Id' 			=> 'macro_639_3',
			'I_Id' 			=> 'code_639_3',
			'I_Status' 		=> 'status'
		),
		'iso639retirements' => array(
			'Id' 			=> 'code_639_3',
			'Ref_Name' 		=> 'ref_name',
			'Ret_Reason' 	=> 'ret_reason',
			'Change_To' 	=> 'change_to',
			'Ret_Remedy' 	=> 'ret_remedy',
			'Effective' 	=> 'effective_date'
		)
	)
);

// ----------------------------------------------------------------------------
// ----------------- API du service SIL - Actions principales -----------------
// ----------------------------------------------------------------------------

/**
 * Lit le fichier des noms communs - tout règne confondu - d'une langue donnée et renvoie un tableau
 * de tous ces noms indexés par leur TSN.
 * La base de données ITIS contient souvent plusieurs traductions d'une même langue pour un taxon donné. Cette
 * fonction met à jour séquentiellement les traductions sans s'en préoccuper. De fait, c'est la dernière traduction
 * rencontrée qui sera fournie dans le tableau de sortie.
 *
 * @api
 *
 * @param string $language
 *        Langue au sens d'ITIS écrite en minuscules. Vaut `french`, `english`, `spanish` etc.
 * @param int    $sha_file
 *        Sha calculé à partir du fichier des noms communs choisi. Le sha est retourné
 *        par la fonction afin d'être stocké par le plugin.
 *
 * @return array
 *        Tableau des noms communs d'une langue donnée indexé par TSN. Le nom commun est préfixé
 *        par le tag de langue SPIP pour être utilisé simplement dans une balise `<multi>`.
 */
function sil_read_table($table, &$sha_file) {
	$records = array();
	$sha_file = false;

	if (in_array($table, $GLOBALS['sil_service']['tables'])) {
		// Ouvrir le fichier des enregistrements de la table spécifiée.
		$file = find_in_path("services/sil/${table}.tab");
		if (file_exists($file) and ($sha_file = sha1_file($file))) {
			// Lecture du fichier .tab comme un fichier texte sachant que :
			// - le délimiteur de colonne est une tabulation
			// - pas de caractère d'enclosure
			$lines = file($file);
			if ($lines) {
				$headers = array();
				$conversion = $GLOBALS['sil_service']['fields'][$table];
				foreach ($lines as $_number => $_line) {
					$values = explode("\t", trim($_line, "\r\n"));
					if ($_number == 0) {
						// Stockage des noms de colonnes car la première ligne contient toujours le header
						$headers = $values;
					} else {
						// Création de chaque enregistrement de la table
						$fields = array();
						foreach ($headers as $_cle => $_header) {
							$fields[$conversion[$_header]] = isset($values[$_cle]) ? $values[$_cle] : '';
						}
						$records[] = $fields;
					}
				}
			}
		}
	}

	return $records;
}


// ----------------------------------------------------------------
// ------------ Fonctions internes utilisées par l'API ------------
// ----------------------------------------------------------------
