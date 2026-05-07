export function initHeroVideo() {
    const wrapper = document.querySelector('.home-hero__video-wrapper');
    if (!wrapper) return;

    const startVideo = () => {
        // Self-hosted video
        const video = wrapper.querySelector('video.hero-video');
        if (video) {
            video.play().catch(() => {});
            return;
        }

        // YouTube / Vimeo iframe
        const iframe = wrapper.querySelector('iframe.hero-video--iframe');
        if (iframe && iframe.dataset.src) {
            iframe.src = iframe.dataset.src;
        }
    };

    // Wait for page load so video doesn't compete with LCP image
    if (document.readyState === 'complete') {
        setTimeout(startVideo, 500);
    } else {
        window.addEventListener('load', () => setTimeout(startVideo, 500));
    }
}
