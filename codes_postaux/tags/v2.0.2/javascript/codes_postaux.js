
function codes_postaux_split( val ) {
	return val.split( /,\s*/ );
}

function codes_postaux_extractLast( term ) {
	return codes_postaux_split( term ).pop();
}


function codes_postaux_autocomplete(objet){

$(objet).after('<input type="hidden" id="id_code_postal" name="id_code_postal" value="" />')
$(objet).autocomplete(
{
source:	function( request, response ) {
			$.getJSON( code_postal_url, {
				term: codes_postaux_extractLast( request.term )
			}, response );
			},
search: function() {
					// custom minLength
					var term = codes_postaux_extractLast( this.value );
					if ( term.length < 2 ) {
						return false;
					}
				},
focus: function( event, ui ) {
			console.log(ui)
				 $( "#id_code_postal" ).val( ui.item.id );
				return false;
			},

select: function( event, ui ) {
								console.log(ui)
								$('#id_code_postal').val(ui.item.id)
								this.value = ui.item.label;
								return false;
								}
					})
	.data("autocomplete")._renderItem = function (ul, item) {
    return $("<li>")
        .data("item.autocomplete", item)
        .append("<a>" + item.label + " " + item.ville + "</a>")
        .appendTo(ul);
   		}
					
				
					

}

