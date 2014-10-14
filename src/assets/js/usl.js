var USL;

!function(a) {
    USL = {
        init: function() {},
        toggle_visibility: function(a) {
            var b = document.getElementById(a);
            b.style.display = "block" == b.style.display ? "none" : "block";
        }
    }, a(function() {
        USL.init();
    });
}(jQuery);