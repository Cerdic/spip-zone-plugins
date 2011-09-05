$(document).ready(function() {
    var bodyClasses = $("body").attr("class");
	if (''!=bodyClasses)
	{
		var bcArray = bodyClasses.split(" ");
		var pageType = "neant";
		var pageComposition = "dist";
		pageType = bcArray[0].replace(/page_/,"");
		if (2==bcArray.length)
		{
			var aClasse2 = bcArray[1].split("_");
			aClasse2.shift();
			pageComposition = aClasse2.join("_");
		}
		$("#pageType").text(pageType);
		$("#pageComposition").text(pageComposition);
		$("#preprod .fermer").click(function(){$("#preprodContent").slideToggle();});
	}
});

function ouvrirTicket(obj, id_ticket)
{
	if (null==id_ticket)
		id_ticket = 0;
	var adresse = obj.title;
	var contexte = obj.value;
	var cible = '/spip.php?page=preprod_ticket_edit&adresse='+encodeURIComponent(adresse)+'&contexte='+encodeURIComponent(contexte)+'&id_ticket='+id_ticket;
	jQuery.modalbox(cible);
}

function fermerBoxTicket()
{
	jQuery.modalboxclose();
	location.reload();
}