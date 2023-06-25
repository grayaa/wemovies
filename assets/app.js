/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import '@fortawesome/fontawesome-free/css/all.css';
import 'bootstrap/dist/css/bootstrap.css';
import $ from 'jquery';
import 'bootstrap/dist/js/bootstrap.js';
import 'slick-carousel';
import 'slick-carousel/slick/slick.css';
import 'slick-carousel/slick/slick-theme.css';


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
        console.log($(event.target))
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


    $('#search-input').on('input', function() {
        $("#overlay").fadeIn(300);
        var query = $(this).val();
        var autocompleteResults = $('#autocomplete-results');
        if (query.length >= 3) {
            autocompleteResults.show();
            $.ajax({
                url: '/ajax_autocomplete',
                method: 'GET',
                data: { query: query },
                success: function(response) {
                    var movies = response.results;
                    autocompleteResults.empty();

                    $.each(movies, function(index, movie) {
                        var title = movie.title;
                        var id = movie.id;
                        var image = movie.image;
                        var imageUrl = image ? 'https://image.tmdb.org/t/p/w200/' + image : '/img/placeholder.png';

                        var movieUrl = '/movie_details/'+id;

                        var resultItem = '<a href="' + movieUrl + '" class="result-item">' +
                            '<img src="' + imageUrl + '" alt="' + title + '">' +
                            '<p>' + title + '</p>' +
                            '</a>';

                        autocompleteResults.append(resultItem);
                    });


                },
                error: function(xhr, status, error) {
                    $("#overlay").fadeOut(300);
                }
            }).done(function() {
                setTimeout(function(){
                    $("#overlay").fadeOut(300);
                }, 500);
            });
        }else {
            $("#overlay").fadeOut(300);
            autocompleteResults.hide();
            autocompleteResults.empty();
        }
    });
    $(document).on('click', '.js-load-movie-content', function() {
        $("#overlay").fadeIn(300);
        $('#popup-movie .modal-dialog').html("");
        var movieId = $(this).data('movie');
        $.ajax({
            url: '/ajax_load_movie_modal',
            method: 'POST',
            data: { id: movieId },
            success: function(response) {
                var movieModal = response;
                $('#popup-movie .modal-dialog').html(movieModal);
                $('#popup-movie').modal('show');

            },
            error: function(xhr, status, error) {
                $("#overlay").fadeOut(300);
            }
        }).done(function() {
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            }, 500);
        });
    });

    $('#popup-movie').on('hide.bs.modal', function () {
        $('#popup-movie .modal-dialog').html("");
        $('#popup-movie').hide();
    });

    // Load selected genres from localStorage
    var selectedGenres = localStorage.getItem('selectedGenres') ? JSON.parse(localStorage.getItem('selectedGenres')) : [];

    // Update the checked state of genre checkboxes based on the loaded genres
    $('.genre').each(function() {
        var genreId = $(this).attr('id');
        if (selectedGenres.includes(genreId)) {
            $(this).prop('checked', true);
        }
    });


    $('.genre').on('change', function() {
        $("#overlay").fadeIn(300);
        selectedGenres = [];
        $('.genre:checked').each(function() {
            selectedGenres.push($(this).attr('id'));
        });
        console.log(selectedGenres);

        // Save selected genres to localStorage
        localStorage.setItem('selectedGenres', JSON.stringify(selectedGenres));

        $.ajax({
            url: '/ajax_get_by_genres',
            method: 'POST',
            data: { genres: selectedGenres },
            success: function(response) {
                $(".movies").html(response);
            },
            error: function(xhr, status, error) {
                $("#overlay").fadeOut(300);
            }
        }).done(function() {
            setTimeout(function(){
                $("#overlay").fadeOut(300);
            }, 500);
        });
    });

    // Trigger the change event if there are already selected genres
    if (selectedGenres.length > 0) {
        $('.genre').trigger('change');
    }

});




