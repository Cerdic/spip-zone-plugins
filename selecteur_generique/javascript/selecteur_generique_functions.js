/**
 * Fichers de fonctions spécifiques complémentaires à l'autocompleteur de jquery.ui
 */

var selecteur_format = function(data){
	var parsed = [];
	var rows = data.split("\n");
	for (var i=0; i < rows.length; i++) {
		var row = $.trim(rows[i]);
		if (row) {
			row = row.split("|");
			parsed[parsed.length] = {
				data: row,
				value: row[0],
				entry: row[1],
				result: row[2]
			};
		}
	}
	return parsed;
}

function split_multiple(val){
	return val.split( /;\s*/ );
}

function extractLast( term ) {
	return split_multiple( term ).pop();
}