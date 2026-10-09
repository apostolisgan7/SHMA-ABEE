/**
 * Lightbox for images inside single post content (.rv-post-content),
 * including Gutenberg galleries. Fancybox is loaded on first click.
 */
const IMG_EXT = /\.(jpe?g|png|gif|webp|avif)(\?.*)?$/i;

function fullSrc(img) {
    const link = img.closest('a');
    if (link && IMG_EXT.test(link.getAttribute('href') || '')) return link.href;

    // Largest candidate from srcset, fallback to src
    if (img.srcset) {
        const best = img.srcset
            .split(',')
            .map((c) => c.trim().split(/\s+/))
            .sort((a, b) => parseInt(b[1] || '0', 10) - parseInt(a[1] || '0', 10))[0];
        if (best && best[0]) return best[0];
    }
    return img.currentSrc || img.src;
}

export function initPostLightbox() {
    const content = document.querySelector('.rv-post-content');
    if (!content) return;

    content.querySelectorAll('img').forEach((img) => {
        const link = img.closest('a');
        if (!link || IMG_EXT.test(link.getAttribute('href') || '')) img.style.cursor = 'zoom-in';
    });

    content.addEventListener('click', async (e) => {
        const img = e.target.closest('img');
        if (!img || !content.contains(img)) return;

        const link = img.closest('a');
        if (link && !IMG_EXT.test(link.getAttribute('href') || '')) return; // normal link

        e.preventDefault();

        const scope = img.closest('.wp-block-gallery') || content;
        const imgs = Array.from(scope.querySelectorAll('img'));
        const items = imgs.map((i) => ({ src: fullSrc(i), type: 'image', caption: i.alt || '' }));
        const startIndex = Math.max(0, imgs.indexOf(img));

        const [{ Fancybox }] = await Promise.all([
            import('@fancyapps/ui'),
            import('@fancyapps/ui/dist/fancybox/fancybox.css'),
        ]);

        Fancybox.show(items, {
            startIndex,
            Thumbs: imgs.length > 1 ? { type: 'classic' } : false,
            Toolbar: { display: { left: [], middle: [], right: ['close'] } },
        });
    });
}
