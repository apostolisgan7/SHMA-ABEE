import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import gsap from "gsap";
import { ScrollToPlugin } from "gsap/ScrollToPlugin";

// Register το plugin του GSAP για το scrolling
gsap.registerPlugin(ScrollToPlugin);

export function initVideoBox() {

    // 1. Ρύθμιση του Fancybox (για το popup)
    Fancybox.bind("[data-fancybox]", {
        Html: {
            videoTpl: `<video class="fancybox__html5video" playsinline controls controlsList="nodownload" poster="{{poster}}">
                        <source src="{{src}}" type="{{format}}" />
                        Σφάλμα: Ο περιηγητής σας δεν υποστηρίζει το video tag.
                      </video>`
        },
        closeButton: "outside",
        showClass: "f-fadeIn",
    });

    // 2. Ρύθμιση του Smooth Scroll (για το "Video Tutorial" button)
    const trigger = document.querySelector('.rv-video-trigger');
    const target = document.querySelector('.video_box_wrapper');

    if (trigger && target) {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();

            // Το ID ή το class του wrapper που θέλεις να πας
            gsap.to(window, {
                duration: 1.2,
                scrollTo: {
                    y: target,
                    offsetY: 50 // Το offset που ζήτησες
                },
                ease: "power3.inOut"
            });
        });
    }
}