(function(jQuery) {
  "use strict";
/* =================================
===         SCROLL TOP       ====
=================================== */
jQuery(".ta_upscr").hide(); 
function taupr() {
  jQuery(window).on('scroll', function() {
    if (jQuery(this).scrollTop() > 500) {
        jQuery('.ta_upscr').fadeIn();
    } else {
      jQuery('.ta_upscr').fadeOut();
    }
  });   
  jQuery('a.ta_upscr').on('click', function()  {
    jQuery('body,html').animate({
      scrollTop: 0
    }, 800);
    return false;
  });
}    
taupr();
})(jQuery);