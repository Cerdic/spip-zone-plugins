<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Vérifié une valeur comme devant être un nom de champ extra
 * 
 * Ce champ ne doit pas être utilisé par SPIP ou un plugin,
 * et ne doit pas être un mot clé de mysql.
 * 
 * Si c'est bon, doit aussi vérifier une expression régulière donnée
 * 
 * Options :
 * - modele : chaine représentant l'expression régulière tolérée
 *
 * @param string $valeur
 *   La valeur à vérifier.
 * @param array $options
 *   Contient une chaine représentant l'expression.
 * @return string
 *   Retourne une chaine vide si c'est valide, sinon une chaine expliquant l'erreur.
 */
function verifier_nom_champ_extra_dist($valeur, $options = array()){
	include_spip('base/objets');
	include_spip('inc/iextras');
	include_spip('inc/saisies');

	$erreur = '';

	$table = $options['table'];
	$valeur = strtolower($valeur); 

	// Champs extras (interface) / Saisies gérent déjà l'unicité des champs extras 
	// déclarés dans une table : on ne peut créer 2 champs extras de même nom.
	// Ici on vérifie en plus que ce champ n'existe pas hors de champs extras.
	$tables_spip = lister_tables_objets_sql($table);
	$champs_declares = array_keys($tables_spip['field']);
	$champs_declares = array_filter($champs_declares, 'strtolower'); // precaution

	$champs_iextras = iextras_champs_extras_definis($table);
	$champs_iextras = array_keys(saisies_lister_avec_sql($champs_iextras));
	$champs_iextras = array_filter($champs_iextras, 'strtolower'); // precaution

	// les champs utilisés en dehors de champs extras, sont la différence
	$champs_utilises = array_diff($champs_declares, $champs_iextras);

	if (in_array($valeur, $champs_utilises)) {
		$erreur = _T('iextras:erreur_nom_champ_utilise');
	}

	// vérifier que le champ n'est pas un mot clé sql
	if (!$erreur) {
		if (in_array(strtoupper($valeur), iextras_sql_reserved_keywords())) {
			$erreur = _T('iextras:erreur_nom_champ_mysql_keyword');
		}
	}

	// vérifier que le champ est bien formaté  (expression régulière)
	if (!$erreur) {
		$verifier = charger_fonction('verifier', 'inc');
		$options += array('modele' => '/^[\w]+$/');
		$erreur = $verifier($valeur, 'regex', $options);
	}

	return $erreur;
}



function iextras_sql_reserved_keywords() {
	return array ( 
		'ACCESSIBLE', 'ADD', 'ALL', 'ALTER', 'ANALYZE', 'AND', 'AS', 'ASC', 'ASENSITIVE', 

		'BEFORE', 'BETWEEN', 'BIGINT', 'BINARY', 'BLOB', 'BOTH', 'BY', 

		'CALL', 'CASCADE', 'CASE', 'CHANGE', 'CHAR', 'CHARACTER', 'CHECK', 'COLLATE', 'COLUMN', 
		'CONDITION', 'CONSTRAINT', 'CONTINUE', 'CONVERT', 'CREATE', 'CROSS', 
		'CURRENT_DATE', 'CURRENT_TIME', 'CURRENT_TIMESTAMP', 'CURRENT_USER', 'CURSOR', 

		'DATABASE', 'DATABASES', 'DAY_HOUR', 'DAY_MICROSECOND', 'DAY_MINUTE', 
		'DAY_SECOND', 'DEC', 'DECIMAL', 'DECLARE', 'DEFAULT', 'DELAYED', 'DELETE', 'DESC', 'DESCRIBE', 
		'DETERMINISTIC', 'DISTINCT', 'DISTINCTROW', 'DIV', 'DOUBLE', 'DROP', 'DUAL',

		'EACH', 'ELSE', 'ELSEIF', 'ENCLOSED', 'ESCAPED', 'EXISTS', 'EXIT', 'EXPLAIN', 

		'FALSE', 'FETCH', 'FLOAT', 'FLOAT4', 'FLOAT8', 'FOR', 'FORCE', 'FOREIGN', 'FROM', 'FULLTEXT', 

		'GENERATED[i]', 'GET', 'GRANT', 'GROUP', 

		'HAVING', 'HIGH_PRIORITY', 'HOUR_MICROSECOND', 'HOUR_MINUTE', 'HOUR_SECOND', 

		'IF', 'IGNORE', 'IN', 'INDEX', 'INFILE', 'INNER', 'INOUT', 'INSENSITIVE', 'INSERT', 
		'INT', 'INT1', 'INT2', 'INT3', 'INT4', 'INT8', 'INTEGER', 'INTERVAL', 'INTO', 
		'IO_AFTER_GTIDS', 'IO_BEFORE_GTIDS', 'IS', 'ITERATE', 

		'JOIN', 

		'KEY', 'KEYS', 'KILL', 

		'LEADING', 'LEAVE', 'LEFT', 'LIKE', 'LIMIT', 'LINEAR', 'LINES', 'LOAD', 
		'LOCALTIME', 'LOCALTIMESTAMP', 'LOCK', 'LONG', 'LONGBLOB', 'LONGTEXT', 'LOOP', 'LOW_PRIORITY', 

		'MASTER_BIND', 'MASTER_SSL_VERIFY_SERVER_CERT', 'MATCH', 'MAXVALUE', 
		'MEDIUMBLOB', 'MEDIUMINT', 'MEDIUMTEXT', 'MIDDLEINT', 'MINUTE_MICROSECOND', 'MINUTE_SECOND', 'MOD', 'MODIFIES', 

		'NATURAL', 'NOT', 'NO_WRITE_TO_BINLOG', 'NULL', 'NUMERIC', 

		'ON', 'OPTIMIZE', 'OPTIMIZER_COSTS[r]', 'OPTION', 'OPTIONALLY', 'OR', 'ORDER', 'OUT', 'OUTER', 'OUTFILE', 

		'PARTITION', 'PRECISION', 'PRIMARY', 'PROCEDURE', 'PURGE', 

		'RANGE', 'READ', 'READS', 'READ_WRITE', 'REAL', 'REFERENCES', 'REGEXP', 
		'RELEASE', 'RENAME', 'REPEAT', 'REPLACE', 'REQUIRE', 'RESIGNAL', 'RESTRICT', 'RETURN', 'REVOKE', 'RIGHT', 'RLIKE', 

		'SCHEMA', 'SCHEMAS', 'SECOND_MICROSECOND', 'SELECT', 'SENSITIVE', 'SEPARATOR', 'SET', 'SHOW', 'SIGNAL', 'SMALLINT', 
		'SPATIAL', 'SPECIFIC', 'SQL', 'SQLEXCEPTION', 'SQLSTATE', 'SQLWARNING', 'SQL_BIG_RESULT', 'SQL_CALC_FOUND_ROWS', 
		'SQL_SMALL_RESULT', 'SSL', 'STARTING', 'STORED[ac]', 'STRAIGHT_JOIN', 

		'TABLE', 'TERMINATED', 'THEN', 'TINYBLOB', 'TINYINT', 'TINYTEXT', 'TO', 'TRAILING', 'TRIGGER', 'TRUE', 

		'UNDO', 'UNION', 'UNIQUE', 'UNLOCK', 'UNSIGNED', 'UPDATE', 'USAGE', 'USE', 'USING', 'UTC_DATE', 'UTC_TIME', 'UTC_TIMESTAMP', 

		'VALUES', 'VARBINARY', 'VARCHAR', 'VARCHARACTER', 'VARYING', 'VIRTUAL[ae]', 

		'WHEN', 'WHERE', 'WHILE', 'WITH', 'WRITE', 

		'XOR', 

		'YEAR_MONTH', 

		'ZEROFILL',
	);
}
