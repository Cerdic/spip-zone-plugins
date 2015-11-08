$.fn.formulaireMassicoterImage = function ( options ) {

    options = $.extend(true,
                       {
                           zoom: 1
                       },
                       options
                      );

    var self = this,
        slider = $('#zoom-slider').slider({
            /* SPIP ne propose pas de traitement d'image pour
               agrandir, alors pour l'instant on ne le permet pas… */
            max: 1,
            min: .01,
            value: options.zoom,
            step: .01,
            slide: function ( event, ui ) {
                $('input#champ_zoom').attr('value', ui.value).trigger('change');
            }
        }),
        img = $('.image-massicot img'),
        initialWidth = img.attr('width'),
        zoom = options.zoom,
        /* On garde en mémoire la sélection telle qu'elle serait sans
           le zoom, pour pouvoir zoomer-dézoomer perdre de la
           précision à cause d'erreurs d'arrondi. */
        selection_nozoom = {},
        selection_actuelle = ($('input[name=x1]').attr('value') !== '') ?
            {
                x1: parseInt($('input[name=x1]').attr('value'),10),
                x2: parseInt($('input[name=x2]').attr('value'),10),
                y1: parseInt($('input[name=y1]').attr('value'),10),
                y2: parseInt($('input[name=y2]').attr('value'),10)
            } :
            {
                x1: 0,
                x2: parseInt(img.attr('width'),10),
                y1: 0,
                y2: parseInt(img.attr('height'),10)
            },
        imgAreaSelector = img.imgAreaSelect({
            instance: true,
            handles: true,
            show: true,
            onSelectEnd: maj_formulaire,
            onSelectChange: function () {
                selection_nozoom = {};
            },
            x1: selection_actuelle.x1,
            x2: selection_actuelle.x2,
            y1: selection_actuelle.y1,
            y2: selection_actuelle.y2,
        });

    /* On initialise le formulaire. On ne le fait pas en php parce que
       c'est plus facile de trouver les dimensions de l'image en js… */
    maj_formulaire(img, selection_actuelle);

    /* la valeur du zoom agit sur l'image */
    $('input#champ_zoom').change(function (e) {
        var new_zoom = $(this).attr('value');

        maj_image(new_zoom);
        maj_selection(new_zoom, zoom);
        zoom = new_zoom;
    });
    /* initialiser le zoom */
    $('input#champ_zoom').trigger('change');

    /* Bouton de réinitialisation */
    $('#formulaire_massicoter_image_reset').click(function (e) {

        $('#zoom-slider').slider('option', 'value', 1);
        $('input#champ_zoom').attr('value', 1);
        maj_image(1);

        imgAreaSelector.setSelection(0,0,img.width(),img.height());
        imgAreaSelector.update();

        maj_formulaire(img, {x1:0, y1:0, x2:img.width(), y2:img.height()});
        selection_nozoom = {};

        e.preventDefault();
        return false;
    });


    /* Mise à jour du formulaire */
    function maj_formulaire (img, selection) {
        $('input[name=x1]').attr('value', selection.x1);
        $('input[name=x2]').attr('value', selection.x2);
        $('input[name=y1]').attr('value', selection.y1);
        $('input[name=y2]').attr('value', selection.y2);

        $('.dimensions').html((selection.x2 - selection.x1) + ' x ' + (selection.y2 - selection.y1));
    }

    /* Une fonction qui agrandi ou rapetisse l'image */
    function maj_image (zoom) {

        img
            .css('width', zoom * initialWidth + 'px')
            .css('height', 'auto')
            .css('margin-left', '-' + (Math.max((zoom*initialWidth - 780),0) / 2) + 'px' );
    }

    /* Une fonction pour mettre à jour la sélection lorsqu'on zoom */
    function maj_selection (new_zoom, zoom) {

        var nouvelle_selection = {};

        if ( ! selection_nozoom.hasOwnProperty('x1')) {
            selection_actuelle = imgAreaSelector.getSelection();
            selection_nozoom = {
                x1: selection_actuelle.x1 / zoom,
                x2: selection_actuelle.x2 / zoom,
                y1: selection_actuelle.y1 / zoom,
                y2: selection_actuelle.y2 / zoom,
            }
        }

        nouvelle_selection.x1 = Math.round(selection_nozoom.x1 * new_zoom);
        nouvelle_selection.x2 = Math.round(selection_nozoom.x2 * new_zoom);
        nouvelle_selection.y1 = Math.round(selection_nozoom.y1 * new_zoom);
        nouvelle_selection.y2 = Math.round(selection_nozoom.y2 * new_zoom);

        nouvelle_selection.x1 = Math.max(0, nouvelle_selection.x1);
        nouvelle_selection.y1 = Math.max(0, nouvelle_selection.y1);
        nouvelle_selection.x2 = Math.min(nouvelle_selection.x2, img.width());
        nouvelle_selection.y2 = Math.min(nouvelle_selection.y2, img.height());

        imgAreaSelector.setSelection(
            nouvelle_selection.x1,
            nouvelle_selection.y1,
            nouvelle_selection.x2,
            nouvelle_selection.y2
        );
        imgAreaSelector.update();

        maj_formulaire(img, nouvelle_selection);
        selection_actuelle = nouvelle_selection;
    }
}
