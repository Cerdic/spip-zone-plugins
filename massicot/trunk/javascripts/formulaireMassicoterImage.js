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
        imgAreaSelector,
        initialWidth = img.attr('width'),
        zoom = options.zoom;

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

        var selection_actuelle = {},
            nouvelle_selection = {};

        if ( ! $('input[name=x1]').attr('value')) {
            selection_actuelle = {
                x1: 0,
                x2: img.attr('width'),
                y1: 0,
                y2: img.attr('height')
            };
        } else {
            selection_actuelle = {
                x1: $('input[name=x1]').attr('value'),
                x2: $('input[name=x2]').attr('value'),
                y1: $('input[name=y1]').attr('value'),
                y2: $('input[name=y2]').attr('value')
            };
        }

        if ( ! imgAreaSelector) {

            imgAreaSelector = img.imgAreaSelect({
                instance: true,
                handles: true,
                show: true,
                onSelectEnd: maj_formulaire,
                x1: selection_actuelle.x1,
                x2: selection_actuelle.x2,
                y1: selection_actuelle.y1,
                y2: selection_actuelle.y2,
            });
            maj_formulaire(img, selection_actuelle);

        } else {

            nouvelle_selection.x1 = Math.round(selection_actuelle.x1 / zoom * new_zoom);
            nouvelle_selection.x2 = Math.round(selection_actuelle.x2 / zoom * new_zoom);
            nouvelle_selection.y1 = Math.round(selection_actuelle.y1 / zoom * new_zoom);
            nouvelle_selection.y2 = Math.round(selection_actuelle.y2 / zoom * new_zoom);

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
        }
    }

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

        e.preventDefault();
        return false;
    });

}
