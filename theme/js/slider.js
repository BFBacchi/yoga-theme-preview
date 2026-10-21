jQuery(document).ready(function ($) {

  $("#yoga-slider").owlCarousel({
	animateOut: 'slideOutDown',
        animateIn: 'flipInX',
        navigation: true, // Show next and prev buttons
        slideSpeed: 200,
        pagination: true,
        paginationSpeed: 400,
        singleItem: true,
        autoPlay: true,
        navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ]
  });
 });


