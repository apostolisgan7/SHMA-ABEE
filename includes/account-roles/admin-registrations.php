<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// -------------------------------------------------------
// 1. Admin menu
// -------------------------------------------------------
add_action( 'admin_menu', function () {
    add_submenu_page(
        'users.php',
        'Εγγραφές B2B',
        'Εγγραφές B2B',
        'manage_options',
        'sigma-registrations',
        'sigma_registrations_page'
    );
} );

// -------------------------------------------------------
// 2. AJAX: approve / reject
// -------------------------------------------------------
add_action( 'wp_ajax_sigma_update_status', 'sigma_ajax_update_status' );

function sigma_ajax_update_status() {
    check_ajax_referer( 'sigma_update_status', 'nonce' );

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( [ 'message' => 'Unauthorized' ], 403 );
    }

    $user_id = intval( $_POST['user_id'] ?? 0 );
    $status  = sanitize_text_field( $_POST['status'] ?? '' );

    if ( ! $user_id || ! in_array( $status, [ 'approved', 'rejected' ], true ) ) {
        wp_send_json_error( [ 'message' => 'Invalid data' ], 400 );
    }

    $user = get_userdata( $user_id );
    if ( ! $user || ! array_intersect( [ 'company', 'municipality' ], (array) $user->roles ) ) {
        wp_send_json_error( [ 'message' => 'User not found' ], 404 );
    }

    $old_status = get_user_meta( $user_id, '_sigma_account_status', true );
    update_user_meta( $user_id, '_sigma_account_status', $status );

    if ( $status === 'approved' && $old_status !== 'approved' ) {
        sigma_send_approval_email( $user );
    }

    wp_send_json_success( [ 'status' => $status ] );
}

// -------------------------------------------------------
// 3. Approval email (HTML)
// -------------------------------------------------------
function sigma_send_approval_email( $user ) {
    $site_name = get_bloginfo( 'name' );
    $login_url = wc_get_page_permalink( 'myaccount' );

    $entity_name = get_user_meta( $user->ID, 'company_name', true )
        ?: get_user_meta( $user->ID, 'municipality_name', true )
        ?: $user->display_name;

    $subject = sprintf( __( 'Ο λογαριασμός σας εγκρίθηκε — %s', 'ruined' ), $site_name );

    $msg  = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial,sans-serif;">';
    $msg .= '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:32px 0;">';
    $msg .= '<tr><td align="center">';
    $msg .= '<table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:6px;overflow:hidden;">';

    $msg .= '<tr><td style="background:#1a1a1a;padding:24px 32px;">';
    $msg .= '<p style="margin:0;color:#ffffff;font-size:20px;font-weight:bold;">' . esc_html( $site_name ) . '</p>';
    $msg .= '<p style="margin:4px 0 0;color:#aaaaaa;font-size:13px;">Ενεργοποίηση λογαριασμού</p>';
    $msg .= '</td></tr>';

    $msg .= '<tr><td style="padding:32px;">';
    $msg .= '<p style="margin:0 0 16px;font-size:15px;color:#333;">Γεια σας <strong>' . esc_html( $entity_name ) . '</strong>,</p>';
    $msg .= '<p style="margin:0 0 24px;font-size:15px;color:#333;">Ο λογαριασμός σας εγκρίθηκε! Μπορείτε πλέον να συνδεθείτε στην πλατφόρμα.</p>';
    $msg .= '<div style="text-align:center;margin:28px 0;">';
    $msg .= '<a href="' . esc_url( $login_url ) . '" style="display:inline-block;background:#1a1a1a;color:#ffffff;text-decoration:none;padding:13px 32px;border-radius:4px;font-size:14px;font-weight:bold;">Σύνδεση &rarr;</a>';
    $msg .= '</div>';
    $msg .= '</td></tr>';

    $msg .= '<tr><td style="background:#f4f4f4;padding:16px 32px;border-top:1px solid #e8e8e8;">';
    $msg .= '<p style="margin:0;font-size:12px;color:#aaaaaa;">' . esc_html( $site_name ) . ' &mdash; Αυτόματη ειδοποίηση.</p>';
    $msg .= '</td></tr>';

    $msg .= '</table></td></tr></table></body></html>';

    $set_html = function () { return 'text/html'; };
    add_filter( 'wp_mail_content_type', $set_html );
    wp_mail( $user->user_email, $subject, $msg );
    remove_filter( 'wp_mail_content_type', $set_html );
}

// -------------------------------------------------------
// 4. Page renderer
// -------------------------------------------------------
function sigma_registrations_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $users = get_users( [
        'role__in' => [ 'company', 'municipality' ],
        'orderby'  => 'registered',
        'order'    => 'DESC',
    ] );

    $nonce = wp_create_nonce( 'sigma_update_status' );
    ?>
    <div class="wrap sigma-reg-wrap">
        <h1>Εγγραφές B2B</h1>
        <p class="sigma-reg-subtitle">Λίστα εταιρειών &amp; δήμων που έχουν κάνει αίτηση εγγραφής.</p>

        <?php if ( empty( $users ) ) : ?>
            <div class="sigma-reg-empty">Δεν υπάρχουν εγγραφές ακόμα.</div>
        <?php else : ?>
        <table class="sigma-reg-table widefat">
            <thead>
                <tr>
                    <th>Επωνυμία</th>
                    <th>Email</th>
                    <th>Τηλέφωνο</th>
                    <th>ΑΦΜ</th>
                    <th>Τύπος</th>
                    <th>Εγγραφή</th>
                    <th>Κατάσταση</th>
                    <th>Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $users as $user ) :
                $status   = get_user_meta( $user->ID, '_sigma_account_status', true ) ?: 'pending';
                $roles    = (array) $user->roles;
                $is_co    = in_array( 'company', $roles, true );
                $name     = $is_co
                    ? get_user_meta( $user->ID, 'company_name', true )
                    : get_user_meta( $user->ID, 'municipality_name', true );
                $phone    = get_user_meta( $user->ID, 'phone', true );
                $vat      = get_user_meta( $user->ID, 'vat', true );
                $type_lbl = $is_co ? 'Εταιρεία' : 'Δήμος';
                $reg_date = date_i18n( 'd/m/Y', strtotime( $user->user_registered ) );

                // JSON data για το popup
                $popup_data = esc_attr( wp_json_encode( [
                    'id'     => $user->ID,
                    'name'   => $name ?: '—',
                    'email'  => $user->user_email,
                    'phone'  => $phone ?: '—',
                    'vat'    => $vat ?: '—',
                    'type'   => $type_lbl,
                    'date'   => $reg_date,
                    'status' => $status,
                ] ) );
            ?>
                <tr data-id="<?php echo $user->ID; ?>" data-status="<?php echo esc_attr( $status ); ?>">
                    <td><strong><?php echo esc_html( $name ?: '—' ); ?></strong></td>
                    <td><?php echo esc_html( $user->user_email ); ?></td>
                    <td><?php echo esc_html( $phone ?: '—' ); ?></td>
                    <td><?php echo esc_html( $vat ?: '—' ); ?></td>
                    <td><span class="sigma-type-badge sigma-type-<?php echo $is_co ? 'company' : 'municipality'; ?>"><?php echo $type_lbl; ?></span></td>
                    <td><?php echo $reg_date; ?></td>
                    <td>
                        <span class="sigma-status sigma-status-<?php echo esc_attr( $status ); ?>">
                            <?php echo $status === 'approved' ? 'Εγκρίθηκε' : ( $status === 'rejected' ? 'Απορρίφθηκε' : 'Αναμονή' ); ?>
                        </span>
                    </td>
                    <td class="sigma-actions">
                        <button class="sigma-btn sigma-btn-approve" data-id="<?php echo $user->ID; ?>" data-nonce="<?php echo $nonce; ?>"
                            <?php echo $status === 'approved' ? 'disabled' : ''; ?>>
                            ✓ Έγκριση
                        </button>
                        <button class="sigma-btn sigma-btn-reject" data-id="<?php echo $user->ID; ?>" data-nonce="<?php echo $nonce; ?>"
                            <?php echo $status === 'rejected' ? 'disabled' : ''; ?>>
                            ✕ Απόρριψη
                        </button>
                        <button class="sigma-btn sigma-btn-details" data-info="<?php echo $popup_data; ?>">
                            ⋯ Λεπτομέρειες
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Popup -->
    <div id="sigma-reg-overlay" style="display:none;">
        <div id="sigma-reg-popup">
            <button id="sigma-reg-close">&times;</button>
            <h2 id="srp-name"></h2>
            <span id="srp-status-badge"></span>
            <table class="sigma-popup-table">
                <tr><th>Email</th><td id="srp-email"></td></tr>
                <tr><th>Τηλέφωνο</th><td id="srp-phone"></td></tr>
                <tr><th>ΑΦΜ</th><td id="srp-vat"></td></tr>
                <tr><th>Τύπος</th><td id="srp-type"></td></tr>
                <tr><th>Ημ. Εγγραφής</th><td id="srp-date"></td></tr>
            </table>
            <div class="sigma-popup-actions">
                <button class="sigma-btn sigma-btn-approve" id="srp-approve">✓ Έγκριση</button>
                <button class="sigma-btn sigma-btn-reject"  id="srp-reject">✕ Απόρριψη</button>
            </div>
        </div>
    </div>

    <style>
    .sigma-reg-wrap { max-width: 1200px; }
    .sigma-reg-subtitle { color: #666; margin-bottom: 20px; }
    .sigma-reg-empty { background: #fff; padding: 32px; text-align: center; color: #888; border: 1px solid #ddd; border-radius: 4px; }

    .sigma-reg-table { border-collapse: collapse; width: 100%; background: #fff; }
    .sigma-reg-table th { background: #1a1a1a; color: #fff !important; padding: 12px 14px; text-align: left; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .sigma-reg-table td { padding: 12px 14px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; font-size: 13px; }
    .sigma-reg-table tr:hover td { background: #fafafa; }

    .sigma-type-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
    .sigma-type-company      { background: #e8f0fe; color: #1a56db; }
    .sigma-type-municipality { background: #fef3c7; color: #92400e; }

    .sigma-status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .sigma-status-pending  { background: #fff7ed; color: #c2410c; }
    .sigma-status-approved { background: #f0fdf4; color: #15803d; }
    .sigma-status-rejected { background: #fef2f2; color: #b91c1c; }

    .sigma-actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
    .sigma-btn { padding: 6px 12px; border: none; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; transition: opacity .15s; }
    .sigma-btn:disabled { opacity: .4; cursor: default; }
    .sigma-btn-approve { background: #15803d; color: #fff; }
    .sigma-btn-approve:not(:disabled):hover { background: #166534; }
    .sigma-btn-reject  { background: #b91c1c; color: #fff; }
    .sigma-btn-reject:not(:disabled):hover  { background: #991b1b; }
    .sigma-btn-details { background: #f1f5f9; color: #334155; }
    .sigma-btn-details:hover { background: #e2e8f0; }

    /* Popup overlay */
    #sigma-reg-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.55); z-index: 99999; display: flex; align-items: center; justify-content: center; }
    #sigma-reg-popup { background: #fff; border-radius: 8px; width: 480px; max-width: calc(100% - 32px); padding: 32px; position: relative; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
    #sigma-reg-close { position: absolute; top: 14px; right: 16px; background: none; border: none; font-size: 22px; cursor: pointer; color: #888; line-height: 1; }
    #sigma-reg-close:hover { color: #111; }
    #sigma-reg-popup h2 { margin: 0 0 6px; font-size: 20px; color: #111; }
    #srp-status-badge { display: inline-block; margin-bottom: 20px; }

    .sigma-popup-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .sigma-popup-table th { width: 130px; text-align: left; padding: 9px 0; font-size: 13px; color: #888; font-weight: 500; border-bottom: 1px solid #f0f0f0; }
    .sigma-popup-table td { padding: 9px 0; font-size: 13px; color: #111; font-weight: 600; border-bottom: 1px solid #f0f0f0; }

    .sigma-popup-actions { display: flex; gap: 10px; }
    .sigma-popup-actions .sigma-btn { padding: 10px 20px; font-size: 13px; flex: 1; text-align: center; }
    </style>

    <script>
    (function() {
        const overlay   = document.getElementById('sigma-reg-overlay');
        const closeBtn  = document.getElementById('sigma-reg-close');
        const srpApprove = document.getElementById('srp-approve');
        const srpReject  = document.getElementById('srp-reject');
        let   currentId  = null;

        const nonce = <?php echo wp_json_encode( $nonce ); ?>;
        const ajaxUrl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;

        function statusLabel(s) {
            return s === 'approved' ? 'Εγκρίθηκε' : s === 'rejected' ? 'Απορρίφθηκε' : 'Αναμονή';
        }
        function statusClass(s) {
            return 'sigma-status sigma-status-' + s;
        }

        // Open popup
        document.querySelectorAll('.sigma-btn-details').forEach(btn => {
            btn.addEventListener('click', () => {
                const d = JSON.parse(btn.dataset.info);
                currentId = d.id;

                document.getElementById('srp-name').textContent  = d.name;
                document.getElementById('srp-email').textContent = d.email;
                document.getElementById('srp-phone').textContent = d.phone;
                document.getElementById('srp-vat').textContent   = d.vat;
                document.getElementById('srp-type').textContent  = d.type;
                document.getElementById('srp-date').textContent  = d.date;

                const badge = document.getElementById('srp-status-badge');
                badge.textContent  = statusLabel(d.status);
                badge.className    = statusClass(d.status);

                srpApprove.disabled = d.status === 'approved';
                srpReject.disabled  = d.status === 'rejected';

                overlay.style.display = 'flex';
            });
        });

        // Close popup
        closeBtn.addEventListener('click', () => overlay.style.display = 'none');
        overlay.addEventListener('click', e => { if (e.target === overlay) overlay.style.display = 'none'; });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') overlay.style.display = 'none'; });

        // Approve/Reject — shared handler
        function doAction(userId, status, rowEl, fromPopup) {
            const fd = new FormData();
            fd.append('action',  'sigma_update_status');
            fd.append('nonce',   nonce);
            fd.append('user_id', userId);
            fd.append('status',  status);

            fetch(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;

                    // Update row
                    if (rowEl) {
                        rowEl.dataset.status = status;
                        const badge = rowEl.querySelector('.sigma-status');
                        badge.textContent = statusLabel(status);
                        badge.className   = statusClass(status);
                        rowEl.querySelector('.sigma-btn-approve').disabled = status === 'approved';
                        rowEl.querySelector('.sigma-btn-reject').disabled  = status === 'rejected';
                    }

                    // Update popup if open
                    if (fromPopup) {
                        const badge = document.getElementById('srp-status-badge');
                        badge.textContent = statusLabel(status);
                        badge.className   = statusClass(status);
                        srpApprove.disabled = status === 'approved';
                        srpReject.disabled  = status === 'rejected';
                    }
                });
        }

        // Table buttons
        document.querySelectorAll('.sigma-btn-approve, .sigma-btn-reject').forEach(btn => {
            if (btn.id === 'srp-approve' || btn.id === 'srp-reject') return; // handled separately
            btn.addEventListener('click', () => {
                const status = btn.classList.contains('sigma-btn-approve') ? 'approved' : 'rejected';
                const row    = btn.closest('tr');
                doAction(btn.dataset.id, status, row, false);
            });
        });

        // Popup buttons
        srpApprove.addEventListener('click', () => {
            if (!currentId) return;
            const row = document.querySelector('tr[data-id="' + currentId + '"]');
            doAction(currentId, 'approved', row, true);
        });
        srpReject.addEventListener('click', () => {
            if (!currentId) return;
            const row = document.querySelector('tr[data-id="' + currentId + '"]');
            doAction(currentId, 'rejected', row, true);
        });
    })();
    </script>
    <?php
}
