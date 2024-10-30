<table class="form-table">
    <tr>
        <th>
            <label for="html_form_sendinblue"><?php echo __("Landing page utilisateur existant", "infocob-tracking"); ?></label>
        </th>
        <td>
	        <?php
		        wp_editor(esc_textarea($html_form ?? ""), 'html_form_sendinblue', array(
			        'textarea_name' => 'html_form_sendinblue',
			        'textarea_rows' => '20',
			        'media_buttons' => false,
			        'teeny'         => false,
			        'tinymce'       => false,
			        'quicktags'     => [
				        'buttons' => 'infocob_tracking_submit'
			        ]
		        ));
	        ?>
        </td>
    </tr>
    <tr>
        <th>
            <label for="html_form_sendinblue_no_user"><?php echo __("Landing page utilisateur inexistant", "infocob-tracking"); ?></label>
        </th>
        <td>
	        <?php
		        wp_editor(esc_textarea($html_form_no_user ?? ""), 'html_form_sendinblue_no_user', array(
			        'textarea_name' => 'html_form_sendinblue_no_user',
			        'textarea_rows' => '20',
			        'media_buttons' => false,
			        'teeny'         => false,
			        'tinymce'       => false,
			        'quicktags'     => [
				        'buttons' => 'infocob_tracking_submit'
			        ]
		        ));
	        ?>
        </td>
    </tr>
</table>
