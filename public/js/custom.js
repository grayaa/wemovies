$(document).ready(function() {
    const $searchInput = $('#search-input');
    const $autocompleteResults = $('#autocomplete-results');

    // Hide autocomplete results by default
    $autocompleteResults.hide();

    // Show autocomplete results on focus
    $searchInput.on('focus', function() {
        $autocompleteResults.show();
    });

    // Hide autocomplete results on focus out
    $searchInput.on('focusout', function(event) {
        if (!$(event.target).is('#search-input')) {
            $autocompleteResults.hide();
        }
    });





    //Slick slider
    $('.slider-for').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.slider-nav'
    }).on('init', function(event, slick) {
        var firstSlideImage = $(slick.$slides[0]).find('img').attr('src');
        $('.masthead::before').css('background-image', 'url("' + firstSlideImage + '")');
    }).on('beforeChange', function(event, slick, currentSlide, nextSlide) {
        var slideImage = $(slick.$slides[nextSlide]).find('img').attr('src');
        $('.masthead::before').css('background-image', 'url("' + slideImage + '")');
    });

    $('.slider-nav').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: '.slider-for',
        dots: false,
        focusOnSelect: true
    });


});


