/**
 * Blog listing: AJAX category tabs + load more (Alpine component).
 * Server contract: includes/blog.php → rv_filter_posts
 */
export function rvBlog({ cat = '', max = 1 } = {}) {
    return {
        cat,
        page: 1,
        max,
        loading: false,
        empty: false,

        setCat(slug, url) {
            if (slug === this.cat || this.loading) return;
            this.cat = slug;
            this.fetchPosts(1, true);
            if (url) history.replaceState(null, '', url);
        },

        loadMore() {
            if (this.loading || this.page >= this.max) return;
            this.fetchPosts(this.page + 1, false);
        },

        async fetchPosts(page, replace) {
            this.loading = true;

            const body = new FormData();
            body.append('action', 'rv_filter_posts');
            body.append('nonce', rv_globals.nonce);
            body.append('cat', this.cat);
            body.append('page', page);

            try {
                const res = await fetch(rv_globals.ajaxurl, { method: 'POST', body });
                const json = await res.json();
                if (!json.success) throw new Error('Request failed');

                const grid = this.$refs.grid;
                if (replace) grid.innerHTML = '';

                const prevCount = grid.children.length;
                grid.insertAdjacentHTML('beforeend', json.data.html);

                this.page = page;
                this.max = json.data.max;
                this.empty = grid.children.length === 0;

                const fresh = Array.from(grid.children).slice(prevCount);
                if (window.gsap && fresh.length) {
                    gsap.fromTo(fresh,
                        { autoAlpha: 0, y: 30 },
                        { autoAlpha: 1, y: 0, duration: 0.6, ease: 'power3.out', stagger: 0.08, clearProps: 'all' }
                    );
                }

                if (window.__lenis__) window.__lenis__.resize();
            } catch (err) {
                console.error('Blog load failed:', err);
            } finally {
                this.loading = false;
            }
        },
    };
}
