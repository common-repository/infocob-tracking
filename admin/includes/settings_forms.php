<form action="options.php" method="post">
	<?php
		settings_fields('infocob_tracking');
		do_settings_sections('infocob_tracking');
		submit_button();
	?>
</form>
