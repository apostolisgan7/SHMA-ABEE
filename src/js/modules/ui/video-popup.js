/* ─────────────────────────────────────────────
   VIDEO POPUP
   Any link with [data-video-popup] (YouTube / Vimeo / mp4)
   opens in a Fancybox popup. Fancybox is loaded on demand.
───────────────────────────────────────────── */
let fancyboxPromise = null;

function loadFancybox() {
    if (!fancyboxPromise) {
        fancyboxPromise = Promise.all([
            import('@fancyapps/ui'),
            import('@fancyapps/ui/dist/fancybox/fancybox.css'),
        ]).then(([mod]) => mod.Fancybox);
    }
    return fancyboxPromise;
}

export function initVideoPopup() {
    if (!document.querySelector('[data-video-popup]')) return;

    document.addEventListener('click', async (e) => {
        const link = e.target.closest('[data-video-popup]');
        if (!link) return;

        e.preventDefault();

        const Fancybox = await loadFancybox();
        Fancybox.show([{ src: link.href }]);
    });
}
