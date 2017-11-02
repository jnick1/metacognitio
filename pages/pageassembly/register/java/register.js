$(document).on("click", ".register-show", function() {
    $("#controller-popup-register").show();
});

$(document).on("click", ".register-hide", function() {
    $("#controller-popup-register").hide();
});

$(document).keyup(function(e) {
    // escape key maps to keyCode `27`
    if (e.keyCode === 27 && $("#controller-popup-register").css("display") !== "none") {
        $("#controller-popup-register").hide();
    }
});