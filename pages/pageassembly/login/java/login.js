$(document).on("click", ".login-show", function() {
    $("#controller-popup-login").show();
});

$(document).on("click", ".login-hide", function() {
    $("#controller-popup-login").hide();
});

$(document).keyup(function(e) {
    // escape key maps to keyCode `27`
    if (e.keyCode === 27 && $("#controller-popup-login").css("display") !== "none") {
        $("#controller-popup-login").hide();
    }
});