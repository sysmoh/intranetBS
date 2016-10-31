/**
 * Cette méthode catch les événements submit d'un formulaire pour envoyer les requetes
 * en ajax. C'est utilisé dans les modal par exemple ou on veut catcher la réponse pour
 * mettre à jour la modal plutot que de charger une nouvelle page
 */
function bindForm() {

    /*
     todo CMR de NUR cette fonction est appelée lors du chargement d'une modale et y a une gros erreur qui vient avec. Du coup, la fonction suivante (semantic_init()) ne marche pas...
     */

    $('form.ajax').submit(function (e) {
        e.preventDefault();

        postForm($(this));

        return false;
    });
}

/**
 * Envoie un formulaire en ajax
 *
 * Fait des notifications en cas d'erreur
 *
 * @param $form Un formulaire à envoyer
 */
function postForm( $form ){

    /*
     * Get all form values
     */
    var values = {};
    $.each( $form.serializeArray(), function(i, field) {
        values[field.name] = field.value;
    });

    /*
     * Throw the form values to the server!
     */
    $.ajax({
        type        : $form.attr( 'method' ),
        url         : $form.attr( 'action' ),
        data        : values,
        success     : function(data) {
            location.reload();
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alerte.send("Erreur lors de l'envoi du formulaire\nDétails : " + xhr.status + " / " + thrownError, 'error');

            return;

            // TODO: précédemment, le success pouvait avoir un "false" comme valeur de retour et avoir un nouveau
            // formulaire avec les messages d'erreur en retour. Sauf que c'est pas terroche, il faut tester ici le
            // code d'erreur et si c'est "bad arguments" afficher le form avec les erreurs (ci-dessous)

            /* Remove old modal and show new with fields errors */
            $("[id^=modal-]").remove();
            $(data).modal('show');

            /* Add error */
            alerte.send("Erreur lors de l'envoi du formulaire");
        }
    });
}
