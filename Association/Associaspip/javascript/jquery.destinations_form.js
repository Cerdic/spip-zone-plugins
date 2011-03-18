function addFormField() {
var id = document.getElementById("idNextDestination").value;
var DestinationSelect = $("#destination_id1").clone().attr("id","destination_id"+id).attr("name","destination_id"+id);
var DestinationList = $("<li class='editer_destination_id"+id+"'></li>").append(DestinationSelect);
var newRow = $("<div class='formo' id='row" + id + "'></div>")
	.append(DestinationList);
newRow.append($("<li class='editer_montant_destination_id"+id+"'><input name='montant_destination_id"+id+"' type='text' id='montant_destination_id"+id+"' /></li><button type='button' class='destButton' onClick='addFormField(); return false;'>+</button><button type='button' class='destButton' onClick='removeFormField(\"#row" + id + "\"); return false;'>-</button>"));

newRow.appendTo($("#divTxtDestination"));

id = (id - 1) + 2;
document.getElementById("idNextDestination").value = id;
}

function removeFormField(id) {
$(id).remove();
}
