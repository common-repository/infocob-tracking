jQuery(document).ready(function($) {

    QTags.addButton(
        'infocob_tracking_submit',
        'submit',
        '[submit id="" class="" value="Envoyer"]',
        '',
        '',
        '',
        '',
        'html_form_start'
    );

    QTags.addButton(
        'infocob_tracking_submit',
        'submit',
        '[submit id="" class="" value="Envoyer"]',
        '',
        '',
        '',
        '',
        'html_form_end'
    );

    QTags.addButton(
        'infocob_tracking_submit',
        'submit',
        '[submit id="" class="" value="Envoyer"]',
        '',
        '',
        '',
        '',
        'html_form_sendinblue'
    );

    QTags.addButton(
        'infocob_tracking_submit',
        'submit',
        '[submit id="" class="" value="Envoyer"]',
        '',
        '',
        '',
        '',
        'html_form_sendinblue_no_user'
    );

    QTags.addButton(
        'infocob_tracking_email',
        'email',
        '[email id="" class="" value="" placeholder="" required="true"]',
        '',
        '',
        '',
        '',
        'html_form_start'
    );

    QTags.addButton(
        'infocob_tracking_groupements',
        'groupements',
        '[groupements class=""]',
        '',
        '',
        '',
        '',
        'html_form_end'
    );

    /**
     * Template mail
     */
    QTags.addButton(
        'infocob_tracking_template_email_subject',
        'Objet',
        '{% subject %}',
        '',
        '',
        '',
        '',
        'email_template'
    );

    QTags.addButton(
        'infocob_tracking_template_email_subscription_link',
        'Lien souscription',
        '{% subscription_link %}',
        '',
        '',
        '',
        '',
        'email_template'
    );

    QTags.addButton(
        'infocob_tracking_template_email',
        'Email utilisateur',
        '{% email %}',
        '',
        '',
        '',
        '',
        'email_template'
    );
});
