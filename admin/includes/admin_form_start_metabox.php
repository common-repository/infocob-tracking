<table class="form-table">
	<tr>
		<th>
			<label for="html_form_start"><?php echo __("Contenu du formulaire", "infocob-tracking"); ?></label>
		</th>
		<td>
			<?php
				wp_editor(esc_textarea($html_form ?? ""), 'html_form_start', array(
					'textarea_name' => 'html_form_start',
					'textarea_rows' => '20',
					'media_buttons' => false,
					'teeny'         => false,
					'tinymce'       => false,
					'quicktags'     => [
						'buttons' => 'infocob_tracking_submit,infocob_tracking_email,infocob_tracking_text'
					]
				));
			?>
		</td>
	</tr>
</table>
