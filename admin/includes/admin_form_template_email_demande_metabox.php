<table class="form-table">
    <tr>
        <th>
            <label for="email_demande_abonnement_template"><?php echo __("Template", "infocob-tracking"); ?></label>
        </th>
        <td>
            <select name="email_template" id="email_demande_abonnement_template" class="full-width" aria-describedby="info_email_demande_abonnement_template">
                <option value="defaut-infocob-tracking"><?php _e("Default (default)", "infocob-tracking"); ?></option>
				<?php foreach(($email_list_template ?? []) as $value) { ?>
                    <option value="<?php echo $value; ?>" <?php echo (strcasecmp($email_template ?? "", $value) === 0) ? "selected" : ""; ?>><?php echo $value; ?></option>
				<?php } ?>
            </select>
            <p class="description" id="info_email_demande_abonnement_template">
				<?php _e("Variables availables : email_from, email_subject, email_title, email_color, email_color_text_title, email_color_link, email_subtitle, email_societe, email_border_radius, subscription_link", "infocob-tracking"); ?>
            </p>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_from"><?php echo __("De", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_from" type='text' name='email_from' value='<?php echo esc_html($email_from ?? ""); ?>'>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_subject"><?php echo __("Objet", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_subject" type='text' name='email_subject' value='<?php echo esc_html($email_subject ?? ""); ?>'>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_societe"><?php echo __("Entreprise", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_societe" type='text' name='email_societe' value='<?php echo esc_html($email_societe ?? ""); ?>'>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_title"><?php echo __("Titre", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_title" type='text' name='email_title' value='<?php echo esc_html($email_title ?? ""); ?>'>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_subtitle"><?php echo __("Sous-titre", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_subtitle" type='text' name='email_subtitle' aria-describedby="info_email_subtitle" value='<?php echo esc_html($email_subtitle ?? ""); ?>'>
            <p class="description" id="info_email_subtitle">
				
            </p>
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_color" aria-describedby="info_email_color"><?php echo __("Couleur", "infocob-tracking"); ?></label>
            <p class="description" id="info_email_color">
				<?php _e("En-tête email", "infocob-tracking"); ?>
            </p>
        </th>
        <td>
            <input name="email_color" type='text' class='color-field' value="<?php echo sanitize_text_field($email_color ?? ""); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_color_text_title" aria-describedby="info_email_color_text_title"><?php echo __("Couleur", "infocob-tracking"); ?></label>
            <p class="description" id="info_email_color_text_title">
				<?php _e("Texte email", "infocob-tracking"); ?>
            </p>
        </th>
        <td>
            <input name="email_color_text_title" type='text' class='color-field' value="<?php echo sanitize_text_field($email_color_text_title ?? ""); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_color_link" aria-describedby="info_email_color_link"><?php echo __("Couleur", "infocob-tracking"); ?></label>
            <p class="description" id="info_email_color_link">
				<?php _e("Liens", "infocob-tracking"); ?>
            </p>
        </th>
        <td>
            <input name="email_color_link" type='text' class='color-field' value="<?php echo sanitize_text_field($email_color_link ?? ""); ?>">
        </td>
    </tr>
    <tr>
        <th>
            <label for="email_border_radius"><?php echo __("Border radius", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input class="full-width" id="email_border_radius" type='number' name='email_border_radius' aria-describedby="info_email_border_radius" value='<?php echo esc_html($email_border_radius ?? ""); ?>'>
        </td>
    </tr>
    <tr>
        <th><?php _e("Logo", "infocob-tracking"); ?></th>
        <td class="logo_email">
            <div class='image-preview-wrapper'>
                <img id='logo-preview' src='<?php echo wp_get_attachment_url($logo["attachment_id"] ?? ""); ?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
            </div>
            <div class="logo_actions">
                <input id="upload_logo_button" type="button" class="button" value="<?php _e('Téléverser logo', "infocob-tracking"); ?>" />
                <input type='hidden' name='logo[attachment_id]' id='logo_attachment_id' value='<?php echo $logo["attachment_id"] ?? ""; ?>'>
                <button id="remove-logo" type="button"><?php _e("Retirer logo", "infocob-tracking"); ?></button>
                <select name="logo[size]">
					<?php foreach(get_intermediate_image_sizes() as $size) { ?>
                        <option value="<?php echo $size; ?>" <?php echo (isset($logo["size"]) && strcasecmp($logo["size"], $size) === 0) ? "selected" : "" ?>><?php echo $size; ?></option>
					<?php } ?>
                </select>
            </div>
        </td>
    </tr>
</table>
