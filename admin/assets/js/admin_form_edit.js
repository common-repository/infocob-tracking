jQuery(document).ready(function($) {
    $('.color-field').wpColorPicker();
    
    $('#backward_page').multipleSelect({
        filter: true,
        showClear: true,
        displayDelimiter: ' | ',
        onClear: function() {
            $('#backward_page').multipleSelect('check', "");
        }
    });

    $('#redirect_page_email_sent').multipleSelect({
        filter: true,
        showClear: true,
        displayDelimiter: ' | ',
        onClear: function() {
            $('#redirect_page_email_sent').multipleSelect('check', "");
        }
    });

    $('#redirect_page_subscription_confirm').multipleSelect({
        filter: true,
        showClear: true,
        displayDelimiter: ' | ',
        onClear: function() {
            $('#redirect_page_subscription_confirm').multipleSelect('check', "");
        }
    });

    $('#type_form').multipleSelect({
        filter: true,
        displayDelimiter: ' | '
    });

    $('#groups_form').multipleSelect({
        filter: true,
        displayDelimiter: ' | '
    });

    $(".infocob_tracking_copy").on("click", function() {
        $(this).select();
        document.execCommand('copy');
    });
    
    /*
        Media Wordpress
     */
    
    // Uploading files
    var file_frame;
    var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
    var set_to_post_id = $("#logo_attachment_id").val(); // Set this
    
    $('#upload_logo_button').on('click', function(event) {
        
        event.preventDefault();
        
        // If the media frame already exists, reopen it.
        if(file_frame) {
            // Set the post ID to what we want
            file_frame.uploader.uploader.param('post_id', set_to_post_id);
            // Open frame
            file_frame.open();
            return;
        } else {
            // Set the wp.media post id so the uploader grabs the ID we want when initialised
            wp.media.model.settings.post.id = set_to_post_id;
        }
        
        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Choisissez un logo à utiliser',
            button: {
                text: 'Utiliser ce logo',
            },
            multiple: false	// Set to true to allow multiple files to be selected
        });
        
        // When an image is selected, run a callback.
        file_frame.on('select', function() {
            // We set multiple to false so only get one image from the uploader
            attachment = file_frame.state().get('selection').first().toJSON();
            
            // Do something with attachment.id and/or attachment.url here
            $('#logo-preview').attr('src', attachment.url).css('width', 'auto');
            $('#logo_attachment_id').val(attachment.id);
            
            // Restore the main post ID
            wp.media.model.settings.post.id = wp_media_post_id;
        });
        
        // Finally, open the modal
        file_frame.open();
    });
    
    // Restore the main ID when the add media button is pressed
    $('a.add_media').on("click", function() {
        wp.media.model.settings.post.id = wp_media_post_id;
    });
    
    $("#remove-logo").on("click", function() {
        $("#logo-preview").prop("src", "");
        $("#logo_attachment_id").val("");
    });
});
