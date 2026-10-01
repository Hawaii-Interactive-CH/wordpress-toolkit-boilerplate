let hamburger = document.querySelector(".hamburger");
let mainNav = document.querySelector(".main-nav");

function setOpen(open) {
    document.body.classList.toggle("nav_open", open);
    hamburger.setAttribute("aria-expanded", String(open));
}

if (hamburger && mainNav) {
    hamburger.addEventListener("click", (e) => {
        e.stopPropagation();
        setOpen(!document.body.classList.contains("nav_open"));
    });
    document.body.addEventListener("click", () => {
        setOpen(false);
    });
    mainNav.addEventListener("click", (e) => {
        e.stopPropagation();
    });
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && document.body.classList.contains("nav_open")) {
            setOpen(false);
            hamburger.focus();
        }
    });
}
