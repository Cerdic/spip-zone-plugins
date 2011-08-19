$(document).ready(function() {
    var bodyClasses = $("body").attr("class");
    var bcArray = bodyClasses.split(" ");
    var pageType = "n&eacute;ant";
    var pageSommaire = "n&eacute;ant";
    pageType = bcArray[0].replace(/page_/,"");
    if (2==bcArray.length)
    {
        var aClasse2 = bcArray[1].split("_");
        aClasse2.shift();
        pageSommaire = aClasse2.join("_");
    }
    $("#page").before( "<div id=\'preprod\' class=\'jour\'></div>" );
    $("#preprod").html("Type de page : "+pageType+"<br />Composition : "+pageSommaire+"<br />Ajouter un ticket...");
});
