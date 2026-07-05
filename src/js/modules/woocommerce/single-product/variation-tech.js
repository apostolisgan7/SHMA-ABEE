export function initVariationTech() {
    const data = window.rvVariationTech;
    if (!data) return;

    const table = document.querySelector('.rv-tech-right .rv-tech-table');
    if (!table) return;

    const { base, vars } = data;
    const originalHTML   = table.innerHTML;

    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function makeRow({ label, value }) {
        return `<div class="row"><span>${esc(label)}</span><strong>${esc(value)}</strong></div>`;
    }

    jQuery(document.body).on('found_variation.rvTech', (e, variation) => {
        const varRows = vars[variation.variation_id] ?? [];
        table.innerHTML = [...varRows, ...base].map(makeRow).join('');
    });

    jQuery(document.body).on('reset_data.rvTech', () => {
        table.innerHTML = originalHTML;
    });
}
