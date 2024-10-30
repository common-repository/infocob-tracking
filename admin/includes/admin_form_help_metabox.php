<div class="container-metabox">
    <h1><?php _e("Liens Sendinblue disponibles"); ?></h1>
    <p>
	<?php
        if(empty($shortcode_pages)) {
            echo "Aucun lien disponible, ajouter un shortcode Sendinblue à une page pour voir le lien apparaître";
        }
		foreach(($shortcode_pages ?? []) as $shortcode_page) { ?>
            <a href="<?php echo $shortcode_page->permalink; ?>" readonly><?php echo $shortcode_page->permalink; ?></a>
		<?php } ?>
    </p>
    
    <h1>Formulaire - Landing page</h1>
    <p>
        Ce formulaire permet à un utilisateur de s'abonner / désabonner d'une mailing liste.<br />
        Les shortcodes <span class="text blue bold">'email'</span> et <span class="text blue bold">'submit'</span> sont
        <span class="text red bold">obligatoires</span> pour un bon fonctionnement.<br />
        Lors de la soumission du formulaire, un email contenant un lien vers la page des abonnements (voir Formulaire Landing page) est envoyé à l'utilisateur afin que celui-ci change ses préférences d'abonnements (selon la configuration),<br />
        tandis que l'internaute est redirigé vers la page de confirmation d'envoi d'email (voir "Page de retour d'email").
    </p>
    
    <h1>Formulaire - Liste d'abonnements</h1>
    <p>
        Ce formulaire présente la liste des abonnements à l'utilisateur pour que celui-ci change ses préférences d'abonnements emailing.<br />
        Pour y afficher la liste des abonnements, un lien d'abonnement / désabonnement doit être utilisé.<br />
        Le shortcode <span class="text blue bold">'groupements'</span> affichera la liste des abonnements et est
        <span class="text red bold">obligatoire</span> tout comme le shortcode
        <span class="text blue bold">'submit'</span>.
    </p>
    
    <h1>Formulaire - Landing page Sendinblue</h1>
    <p>
        Ce formulaire à la même fonction que le formulaire landing page à l'exception où la demande de changement des préférences d'abonnements / désabonnement<br />
        ne provient pas du site wordpress mais directement d'un lien envoyé par une campagne emailing sendinblue.<br />
        Lors de l'envoi du lien par Sendinblue il faudra donc mettre le lien vers la page contenant le shortcode du formulaire de réception Sendinblue.<br />
        Le shortcode <span class="text blue bold">'submit'</span> est <span class="text red bold">obligatoire</span>.
    </p>
</div>
