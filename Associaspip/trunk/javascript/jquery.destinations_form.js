function addFormField() {
var id = document.getElementById("idNextDestination").value;
var DestinationSelect = $("#id_dest_1").clone().attr("id","id_dest_"+id).attr("name","id_dest["+id+"]");
var DestinationList = $("<li></li>").append(DestinationSelect);
var newRow = $("<div class='formo' id='row" + id + "'></div>")
	.append(DestinationList);
newRow.append($("<ul><li><input name='montant_dest["+id+"]' type='text' id='montant_dest_"+id+"' /><button type='button' class='destButton' onclick='addFormField(); return false;'>+</button><button type='button' class='destButton' onclick='removeFormField(\"#row" + id + "\"); return false;'>-</button></li></ul>"));

newRow.appendTo($("#divTxtDestination"));

id = (id - 1) + 2;
document.getElementById("idNextDestination").value = id;
}

function removeFormField(id) {
$(id).remove();
}
