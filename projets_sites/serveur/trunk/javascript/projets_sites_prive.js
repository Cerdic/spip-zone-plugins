jQuery(document).ready(function($) {
    $('.projets_site #wysiwyg .champ.fieldset').each(function () {
        if ($(this).next().is('[class*="contenu"]') === false) {
            $(this).addClass('vide');
        }
    });
});