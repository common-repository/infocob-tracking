<table class="form-table">
	<tr>
		<th>
			<label for="html_form_end"><?php echo __("Contenu du formulaire", "infocob-tracking"); ?></label>
		</th>
		<td>
			<?php
				wp_editor(esc_textarea($html_form ?? ""), 'html_form_end', array(
					'textarea_name' => 'html_form_end',
					'textarea_rows' => '20',
					'media_buttons' => false,
					'teeny'         => false,
					'tinymce'       => false,
					'quicktags'     => [
						'buttons' => 'infocob_tracking_submit,infocob_tracking_email,infocob_tracking_text,infocob_tracking_groupements'
					]
				));
			?>
		</td>
	</tr>
</table>
