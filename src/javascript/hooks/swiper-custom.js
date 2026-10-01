import Swiper from "swiper/bundle";

let gallery = document.querySelector(".gallery");
if (gallery) {
    new Swiper(".gallery", {
        speed: 1000,
        autoplay: {
            delay: 2000,
        },
        slidersPerView: 1,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
    });
}
