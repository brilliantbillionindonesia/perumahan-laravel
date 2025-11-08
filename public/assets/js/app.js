function windowScroll() {
    const navbar = document.getElementById("navbar");
    if (
        document.body.scrollTop >= 50 ||
        document.documentElement.scrollTop >= 50
    ) {
        navbar.classList.add("nav-sticky");
    } else {
        navbar.classList.remove("nav-sticky");
    }
}

window.addEventListener('scroll', (ev) => {
    ev.preventDefault();
    windowScroll();
});

var swiper = new Swiper(".testi-slider", {
    slidesPerView: 1,
    freeMode: true,
    loop: true,
    pagination: {
    el: ".swiper-pagination",
    clickable: true,
    },
    breakpoints: {
    768: {
        slidesPerView: 1
    },
    992: {
        slidesPerView: 3
    }
    }
});



var swiper = new Swiper(".mySwiper", {
    slidesPerView: 3,
    spaceBetween: 50,
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        375: {
            slidesPerView: 1,
            spaceBetween: 20,
        },

        640: {
            slidesPerView: 3,
            spaceBetween: 20,
        },

        768: {
            slidesPerView: 3,
            spaceBetween: 40,
        },

        768: {
            slidesPerView: 3,
            spaceBetween: 40,
        },

        771: {
            slidesPerView: 3,
            spaceBetween: 40,
        },
        1024: {
            slidesPerView: 3,
            spaceBetween: 50,
        },
    },
    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
});