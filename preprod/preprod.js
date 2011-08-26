$(document).ready(function() {
    var bodyClasses = $("body").attr("class");
    var bcArray = bodyClasses.split(" ");
    var pageType = "neant";
    var pageComposition = "neant";
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
});

function ajouterTicket(adresse)
{
	jQuery.modalbox('/spip.php?page=preprod_ajouter_ticket&var_mode=recalcul&adresse='+encodeURIComponent(adresse));
}

function apercuTicket(id_ticket)
{
	jQuery.modalbox('/spip.php?page=preprod_voir_ticket&var_mode=recalcul&id_ticket='+id_ticket);
}

function fermerBoxTicket()
{
	jQuery.modalboxclose();
	location.reload();
}