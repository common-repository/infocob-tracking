<input type="hidden" name="post_id" id="post_id" value="<?php echo get_the_ID(); ?>">

<table class="form-table">
    <tr>
        <th>
            <label for="backward_page"><?php echo __("Page - Liste d'abonnements", "infocob-tracking"); ?></label>
        </th>
        <td>
            <select class="full-width" id="backward_page" name="backward_page">
                <option value=""><?php echo __("Aucune page", "infocob-tracking"); ?></option>
				<?php foreach(($pages_with_shortcode_end ?? []) as $page) : ?>
                    <option value="<?php echo $page->ID ?>" <?php if($page->ID == ($backward_page ?? "")) : ?>selected<?php endif; ?>><?php echo $page->post_title ?></option>
				<?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>
            <label for="redirect_page_email_sent"><?php echo __("Page - Email envoyé", "infocob-tracking"); ?></label>
        </th>
        <td>
            <select class="full-width" id="redirect_page_email_sent" name="redirect_page_email_sent">
                <option value=""><?php echo __("Aucune page", "infocob-tracking"); ?></option>
				<?php foreach(($wp_pages_list ?? []) as $page) : ?>
                    <option value="<?php echo $page->ID ?>" <?php if($page->ID == ($redirect_page_email_sent ?? "")) : ?>selected<?php endif; ?>><?php echo $page->post_title ?></option>
				<?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>
            <label for="redirect_page_subscription_confirm"><?php echo __("Page - Abonnements modifiés", "infocob-tracking"); ?></label>
        </th>
        <td>
            <select class="full-width" id="redirect_page_subscription_confirm" name="redirect_page_subscription_confirm">
                <option value=""><?php echo __("Aucune page", "infocob-tracking"); ?></option>
				<?php foreach(($wp_pages_list ?? []) as $page) : ?>
                    <option value="<?php echo $page->ID ?>" <?php if($page->ID == ($redirect_page_subscription_confirm ?? "")) : ?>selected<?php endif; ?>><?php echo $page->post_title ?></option>
				<?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>
            <label for="shortcode_form_start"><?php echo __("Shortcode formulaire - Landing page", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input type="text" name="shortcode_form_start" id="shortcode_form_start" aria-describedby="info_shortcode_start" class="infocob_tracking_copy full-width" value="<?php echo esc_html($shortcode_form_start ?? ""); ?>" readonly>
            <p class="description" id="info_shortcode_start">
                <?php echo $info_shortcode_start ?? ""; ?>
            </p>
        </td>
    </tr>
    <tr>
        <th>
            <label for="shortcode_form_end"><?php echo __("Shortcode formulaire - Liste d'abonnements", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input type="text" name="shortcode_form_end" id="shortcode_form_end" aria-describedby="info_shortcode_end" class="infocob_tracking_copy full-width" value="<?php echo esc_html($shortcode_form_end ?? ""); ?>" readonly>
            <p class="description" id="info_shortcode_end">
		        <?php echo $info_shortcode_end ?? ""; ?>
            </p>
        </td>
    </tr>
    <tr>
        <th>
            <label for="shortcode_form_sendinblue"><?php echo __("Shortcode formulaire - Landing page Sendinblue", "infocob-tracking"); ?></label>
        </th>
        <td>
            <input type="text" name="shortcode_form_sendinblue" id="shortcode_form_sendinblue" aria-describedby="info_shortcode_sendinblue" class="infocob_tracking_copy full-width" value="<?php echo esc_html($shortcode_form_sendinblue ?? ""); ?>" readonly>
            <p class="description" id="info_shortcode_sendinblue">
		        <?php echo $info_shortcode_sendinblue ?? ""; ?>
            </p>
        </td>
    </tr>
    <tr>
        <th>
            <label for="redirect_html_form_register"><?php echo __("Page formulaire d'inscription (Infocob Forms)", "infocob-tracking"); ?></label>
        </th>
        <td>
            <select class="full-width" id="redirect_html_form_register" name="redirect_html_form_register" aria-describedby="info_form_register">
                <option value=""><?php echo __("Aucune page", "infocob-tracking"); ?></option>
				<?php foreach(($cf7_forms_pages ?? []) as $page) : ?>
                    <option value="<?php echo $page->ID ?>" <?php if($page->ID == ($redirect_html_form_register ?? "")) : ?>selected<?php endif; ?>><?php echo $page->post_title ?></option>
				<?php endforeach; ?>
            </select>
            <p class="description" id="info_form_register">
		        <?php echo $info_form_register ?? ""; ?>
            </p>
        </td>
    </tr>
    <tr>
        <th>
            <label for="type_form"><?php echo __("Type", "infocob-tracking"); ?></label>
        </th>
        <td>
            <select class="full-width" name="type_form" id="type_form" value="<?php echo esc_html($type_form ?? ""); ?>">
                <option value="auto" <?php if(strcasecmp(esc_html($type_form ?? ""), "auto") === 0) {
					echo "selected";
				} ?>>Auto
                </option>
                <option value="unsubscribe" <?php if(strcasecmp(esc_html($type_form ?? ""), "unsubscribe") === 0) {
					echo "selected";
				} ?>>Désabonnement
                </option>
                <option value="subscribe" <?php if(strcasecmp(esc_html($type_form ?? ""), "subscribe") === 0) {
					echo "selected";
				} ?>>Abonnement
                </option>
            </select>
        </td>
    </tr>
    <tr>
        <th>
            <label for="groups_form"><?php echo __("Groupements", "infocob-tracking"); ?></label>
        </th>
        <td>
            <select class="full-width" id="groups_form" name="groups_form[]" multiple>
				<?php foreach(($groups_list ?? []) as $group) : ?>
					<?php if(!empty(trim($group))) : ?>
                        <option value="<?php echo sanitize_text_field($group); ?>" <?php if(in_array(sanitize_text_field($group), ($groups_form ?? []))) : ?>selected<?php endif; ?>><?php echo sanitize_text_field($group); ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
            </select>
        </td>
    </tr>
</table>
