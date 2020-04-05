
function cog_split( val ) {
	return val.split( /,\s*/ );
}

function cog_extractLast( term ) {
	return cog_split( term ).pop();
}


function cog_commune_autocomplete(){

$("#nom_commune").autocomplete(
{
source:	function( request, response ) {
			$.getJSON( cog_url_ville, {
				term: cog_extractLast( request.term )
			}, response );
			},
search: function() {
					// custom minLength
					var term = cog_extractLast( this.value );
					if ( term.length < 2 ) {
						return false;
					}
				},
focus: function() {
				// prevent value inserted on focus
				return false;
			},

select: function( event, ui ) {
								var terms = cog_split( this.value );
								// remove the current input
								terms.pop();
								// add the selected item
								terms.push( ui.item.value );
								$(this).after('<input type="hidden" name="id_cog_commune[]" value="'+ui.item.id+'" />')
								terms.push("");
								this.value = terms.join(", ");
								return false;
								}
					});
}

