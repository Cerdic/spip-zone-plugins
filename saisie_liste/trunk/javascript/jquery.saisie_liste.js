$.fn.saisieListe = function( options ) {

    this.nom = options.nom;

    var self = this,
        defaut_sortable = {
            // valeurs par défaut pour sortable
            containement: 'parent',
            cursor: 'move',
            placeholder: 'ui-state-highlight'
        };

    options = $.extend(true,
                       {
                           sortable: {
                               update: function () { return; }
                           }
                       },
                       options
                      );

    options.sortable.update = calculerFonctionUpdate(options.sortable.update);
    options.sortable = $.extend(defaut_sortable, options.sortable);

    // numéroter les li's
    this.find('> li').each(function (index, li) {
        $(li).data('index_objet', index);
    });

    // s'assurer que presser enter dans un des champs de la saisie
    // n'utilise pas un submit de la saisie liste
    this.keypress(function (e) {
        if ((e.which == 13)
          // si on a pressé enter sur un submit, on ne change rien
          && (e.target.type !== 'submit')) {
            $(this).parents('form').submit();
            return false;
        }
    });

    this.sortable(options.sortable);

    return this;

    // retourne une fonction qui commence par mettre à jour la valeur
    // du champs hidden 'nom-liste[permutations]' puis execute le
    // callback donné.
    function calculerFonctionUpdate (callback) {

        return function (event, ui) {
            self.parent().find('input[name*="permutations"]')
                .attr('value', calculerPermutation());
            if (typeof(callback === 'function')) {
                callback.call(self, event, ui);
            }
        };
    }

    // parcours les objets dans l'ordre affiché et se sert de la
    // numérotation de départ pour trouver la permutation qui a été
    // faite.
    function calculerPermutation () {
        var permutations = [];

        self.find('> li').each(function (index, li) {
            permutations.push($(li).data('index_objet'));
        });
        return permutations.join(',');
    }
};
