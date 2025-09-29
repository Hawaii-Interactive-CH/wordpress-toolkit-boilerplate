//import { brotliDecompressSync } from "zlib";

let hamburger = document.querySelector(".hamburger");
let mainNav = document.querySelector(".main-nav");

if (hamburger && mainNav) {
    hamburger.addEventListener("click", (e) => {
        e.stopPropagation();
        document.body.classList.toggle("nav_open");
    });
    document.body.addEventListener("click", (e) => {
        e.stopPropagation();
        document.body.classList.remove("nav_open");
    });
    mainNav.addEventListener("click", (e) => {
        e.stopPropagation();
    });
}
