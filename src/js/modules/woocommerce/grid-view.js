document.addEventListener('DOMContentLoaded', () => {
    const gridViewButton = document.getElementById('grid-view');
    const listViewButton = document.getElementById('list-view');
    const body = document.body;

    const setShopView = (view) => {
        // Set cookie that expires in 30 days
        const d = new Date();
        d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
        const expires = `expires=${d.toUTCString()}`;
        document.cookie = `shop_view=${view};${expires};path=/;SameSite=Lax`;

        // Update body class
        if (view === 'list') {
            body.classList.remove('shop-view-grid');
            body.classList.add('shop-view-list');
            listViewButton.classList.add('active');
            gridViewButton.classList.remove('active');
        } else {
            body.classList.remove('shop-view-list');
            body.classList.add('shop-view-grid');
            gridViewButton.classList.add('active');
            listViewButton.classList.remove('active');
        }
    };

    if (gridViewButton && listViewButton) {
        gridViewButton.addEventListener('click', (e) => {
            e.preventDefault();
            setShopView('grid');
        });

        listViewButton.addEventListener('click', (e) => {
            e.preventDefault();
            setShopView('list');
        });
    }
});
