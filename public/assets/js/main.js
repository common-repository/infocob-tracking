jQuery(document).ready(function($) {
    $(".infocob-tracking-checkall-groupements").on("load, change", function() {
        $(this).parent().parent().find("input[type=checkbox]").prop("checked", $(this).prop("checked"));
    });

    $(".infocob-tracking-checkall-groupements").parent().parent().find("input[type=checkbox]:not([class=infocob-tracking-checkall-groupements])").on("load, change", infocob_tracking_checkall);

    function infocob_tracking_checkall() {
        let checkAllParent = $(this).parent().parent().parent().find(".infocob-tracking-checkall-groupements");
        let allChilds = $(checkAllParent).parent().parent().find("input[type=checkbox]:not([class=infocob-tracking-checkall-groupements])");
        let allChildsChecked = $(checkAllParent).parent().parent().find("input[type=checkbox]:checked");

        if($(this).prop("checked")) {
            $(checkAllParent).prop("checked", ($(allChildsChecked).length === $(allChilds).length));
        } else {
            $(checkAllParent).prop("checked", false);
        }
    }

    let loader = $("div.infocob_tracking_loader");
    if(loader && $(loader).parent()) {
        $(loader).parent().on("submit", function() {
            toggleLoader(true);
        });
    }

    function toggleLoader(state = null) {
        let loader = $("div.infocob_tracking_loader");
        if(loader.is(':visible') || state === false) {
            loader.removeClass('loading');
            $("body").css("overflow", "inherit");
        } else if(loader.is(":hidden") || state === true) {
            $("body").css("overflow", "hidden");
            loader.addClass('loading');
        }
    }

});
