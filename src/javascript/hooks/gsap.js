import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";

gsap.registerPlugin(ScrollTrigger);

window.addEventListener("load", () => {
    // Add the direction & where the animation began, assur to hide the element before the animation
    gsap.set(".animate-from-right", { x: 100, opacity: 0 });
    gsap.set(".animate-from-bottom", { y: 100, opacity: 0 });
    gsap.set(".animate-from-left", { x: -100, opacity: 0 });
    gsap.set(".animate-from-top", { y: -100, opacity: 0 });

    // Appear from the right
    gsap.to(".animate-from-right", {
        x: 0,
        opacity: 1,
        duration: 1,
        stagger: 0.2,
        scrollTrigger: {
            trigger: ".animate-from-right",
            start: "top 85%", // Adjust  as needed
            end: "top 20%",    // Adjust as needed
            toggleActions: "play none none none",
            once: true,
        },
    });

    // Appear from the left
    gsap.to(".animate-from-left", {
        x: 0,
        opacity: 1,
        duration: 1,
        stagger: 0.2,
        scrollTrigger: {
            trigger: ".animate-from-left",
            start: "top 85%", // Adjust as needed
            end: "top 20%",   // Adjust as needed
            toggleActions: "play none none none",
            once: true,
        },
    });

    // Appear from the top
    gsap.to(".animate-from-top", {
        y: 0,
        opacity: 1,
        duration: 1,
        stagger: 0.2,
        scrollTrigger: {
            trigger: ".animate-from-top",
            start: "top 85%", // Adjust as needed
            end: "top 20%",    // Adjust as needed
            toggleActions: "play none none none",
            once: true,
        },
    });

    // Appear from the bottom
    gsap.to(".animate-from-bottom", {
        y: 0,
        opacity: 1,
        duration: 1,
        stagger: 0.2,
        scrollTrigger: {
            trigger: ".animate-from-bottom",
            start: "top 85%", // Adjust as needed
            end: "top 20%",   // Adjust as needed
            toggleActions: "play none none none",
            once: true,
        },
    });
});
