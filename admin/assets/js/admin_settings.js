jQuery(document).ready(function($) {
    var btnGenerateSecret = $("#generate_token_secret");
    var tokenSecret = $("#token_secret");

    if(tokenSecret.val() === "") {
        setTokenSecret();
    }
    $(btnGenerateSecret).on("click", setTokenSecret);

    function setTokenSecret() {
        let secret = generateSecret(64);
        tokenSecret.val(secret);
    }

    function generateSecret(length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for(var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    $('.color-field').wpColorPicker();
});
