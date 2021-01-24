require('@fortawesome/fontawesome-free/js/all.js');
import $ from 'jquery';
import slick from 'slick-carousel';

let $q = document.querySelector.bind(document),
    $$q = document.querySelectorAll.bind(document);

document.addEventListener("DOMContentLoaded", function(){
    if( $('.carousel-slides').length ){
        var slickSettings = { dots: true, autoplay: true, autplaySpeed: 3000 };
        $('.carousel-slides').slick({
            autoplay: slickSettings.autoplay,
            autoplaySpeed: slickSettings.autoplaySpeed,
            arrows: false,
            dots: slickSettings.dots,
            appendDots: $q('.carousel-slides .hero-dots'),
            pauseOnHover: true
        });
    }
});
