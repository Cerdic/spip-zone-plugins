function addFormField() {
var id = document.getElementById("idNextDestination").value;
var DestinationList = $("#destination_id1").clone().attr("id","destination_id"+id).attr("name","destination_id"+id);
var newRow = $("<p class='formo' id='row" + id + "'></p>")
	.append(DestinationList);
newRow.append($("<input name='montant_destination_id"+id+"' type='text' id='montant_destination_id"+id+"' /><button type='button' class='destButton' onClick='addFormField(); return false;'>+</button><button type='button' class='destButton' onClick='removeFormField(\"#row" + id + "\"); return false;'>-</button><p>"));

newRow.appendTo($("#divTxtDestination"));

id = (id - 1) + 2;
document.getElementById("idNextDestination").value = id;
}

function removeFormField(id) {
$(id).remove();
}
