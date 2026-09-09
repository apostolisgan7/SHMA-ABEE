<?php
/**
 * Admin-only documentation viewer.
 *
 * Renders woocommerce-admin-docs.md (theme root) as a wp-admin page and as a
 * standalone /sima-docs route, with sidebar navigation, one section per
 * top-level "## " heading in the markdown. Edit that .md file to change the
 * content — this file only parses/renders it.
 *
 * IMAGES: in the markdown, reference screenshots as:
 *   ![alt text]({{ASSETS_URL}}/filename.png)
 * and place the actual image files in src/img/docs-assets/. {{ASSETS_URL}}
 * is swapped for the correct URL automatically.
 */

if (!defined('ABSPATH')) exit;

add_action('template_redirect', 'ruined_maybe_render_sima_docs_route');

function ruined_maybe_render_sima_docs_route() {
    $path = trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    if ($path !== 'sima-docs') {
        return;
    }

    if (!is_user_logged_in()) {
        wp_safe_redirect(wp_login_url(home_url('/sima-docs')));
        exit;
    }

    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Δεν έχεις δικαίωμα πρόσβασης σε αυτή τη σελίδα.', 'ruined'), '', ['response' => 403]);
    }

    ruined_render_sima_docs_standalone_page();
    exit;
}

add_action('admin_menu', 'ruined_register_admin_docs_page');

function ruined_register_admin_docs_page() {
    add_menu_page(
            'Admin Documentation',
            'Τεκμηρίωση',
            'manage_options',
            'ruined-admin-docs',
            'ruined_render_admin_docs_page',
            'dashicons-book',
            59
    );
}

function ruined_admin_docs_file_path() {
    return get_stylesheet_directory() . '/woocommerce-admin-docs.md';
}

function ruined_admin_docs_resolve_assets_url($markdown) {
    return str_replace('{{ASSETS_URL}}', get_stylesheet_directory_uri() . '/src/img/docs-assets', $markdown);
}

/**
 * Split the markdown on top-level "## " headings.
 */
function ruined_admin_docs_get_sections($markdown) {
    $lines   = preg_split('/\r\n|\r|\n/', $markdown);
    $sections = [];
    $title   = 'Επισκόπηση';
    $buffer  = [];

    foreach ($lines as $line) {
        if (preg_match('/^##\s+(.*)$/', $line, $m)) {
            $sections[] = ['title' => $title, 'body' => implode("\n", $buffer)];
            $title  = trim($m[1]);
            $buffer = [];
        } else {
            $buffer[] = $line;
        }
    }
    $sections[] = ['title' => $title, 'body' => implode("\n", $buffer)];

    return $sections;
}

/**
 * Minimal markdown -> HTML converter. Headings get ids (prefixed by the
 * parent section's slug, so h3/h4 anchors are unique site-wide) — used by
 * the mini table-of-contents.
 */
function ruined_admin_docs_md_to_html($md, $id_prefix = '') {
    $lines = preg_split('/\r\n|\r|\n/', $md);
    $n     = count($lines);
    $html  = '';
    $i     = 0;
    $paragraph_buffer = [];

    $flush_paragraph = function () use (&$paragraph_buffer, &$html) {
        if (!empty($paragraph_buffer)) {
            $text  = trim(implode(' ', $paragraph_buffer));
            if ($text !== '') {
                $html .= '<p>' . ruined_admin_docs_md_inline($text) . "</p>\n";
            }
            $paragraph_buffer = [];
        }
    };

    while ($i < $n) {
        $line    = $lines[$i];
        $trimmed = trim($line);

        if ($trimmed === '') {
            $flush_paragraph();
            $i++;
            continue;
        }

        if (preg_match('/^!\[([^\]]*)\]\(([^)]+)\)$/', $trimmed, $m)) {
            $flush_paragraph();
            $html .= '<figure class="rv-doc-figure"><img src="' . esc_url($m[2]) . '" alt="' . esc_attr($m[1]) . '" loading="lazy"></figure>' . "\n";
            $i++;
            continue;
        }

        if (preg_match('/^(#{1,6})\s+(.*)$/', $trimmed, $m)) {
            $flush_paragraph();
            $level    = strlen($m[1]);
            $text     = trim($m[2]);
            $heading_slug = sanitize_title($text);
            $id       = $id_prefix !== '' ? $id_prefix . '-' . $heading_slug : $heading_slug;
            $toc_attr = ($level === 3 || $level === 4) ? ' data-toc="1" data-toc-level="' . $level . '"' : '';
            $html .= "<h{$level} id=\"" . esc_attr($id) . "\"{$toc_attr}>" . ruined_admin_docs_md_inline($text) . "</h{$level}>\n";
            $i++;
            continue;
        }

        if (preg_match('/^-{3,}$/', $trimmed)) {
            $flush_paragraph();
            $html .= "<hr>\n";
            $i++;
            continue;
        }

        if (strpos($trimmed, '>') === 0) {
            $flush_paragraph();
            $quote_lines = [];
            while ($i < $n && strpos(trim($lines[$i]), '>') === 0) {
                $quote_lines[] = preg_replace('/^>\s?/', '', trim($lines[$i]));
                $i++;
            }
            $html .= '<blockquote>' . ruined_admin_docs_md_to_html(implode("\n", $quote_lines), $id_prefix) . "</blockquote>\n";
            continue;
        }

        if (strpos($trimmed, '```') === 0) {
            $flush_paragraph();
            $i++;
            $code_lines = [];
            while ($i < $n && strpos(trim($lines[$i]), '```') !== 0) {
                $code_lines[] = $lines[$i];
                $i++;
            }
            $i++;
            $html .= '<pre><code>' . htmlspecialchars(implode("\n", $code_lines), ENT_QUOTES, 'UTF-8') . "</code></pre>\n";
            continue;
        }

        if (strpos($trimmed, '|') === 0) {
            $flush_paragraph();
            $table_lines = [];
            while ($i < $n && trim($lines[$i]) !== '' && strpos(trim($lines[$i]), '|') !== false) {
                $table_lines[] = trim($lines[$i]);
                $i++;
            }
            $html .= ruined_admin_docs_md_table($table_lines);
            continue;
        }

        if (preg_match('/^[-*]\s+(.*)$/', $trimmed, $m)) {
            $flush_paragraph();
            $html .= "<ul>\n";
            while ($i < $n && preg_match('/^[-*]\s+(.*)$/', trim($lines[$i]), $m2)) {
                $html .= '<li>' . ruined_admin_docs_md_inline($m2[1]) . "</li>\n";
                $i++;
            }
            $html .= "</ul>\n";
            continue;
        }

        if (preg_match('/^\d+\.\s+(.*)$/', $trimmed, $m)) {
            $flush_paragraph();
            $html .= "<ol>\n";
            while ($i < $n && preg_match('/^\d+\.\s+(.*)$/', trim($lines[$i]), $m2)) {
                $html .= '<li>' . ruined_admin_docs_md_inline($m2[1]) . "</li>\n";
                $i++;
            }
            $html .= "</ol>\n";
            continue;
        }

        $paragraph_buffer[] = $trimmed;
        $i++;
    }

    $flush_paragraph();

    return $html;
}

function ruined_admin_docs_md_table($lines) {
    if (count($lines) < 2) {
        return '';
    }

    $parse_row = function ($line) {
        $line = trim($line);
        $line = preg_replace('/^\|/', '', $line);
        $line = preg_replace('/\|$/', '', $line);
        return array_map('trim', explode('|', $line));
    };

    $header = $parse_row($lines[0]);
    $html   = '<div class="rv-doc-table-wrap"><table><thead><tr>';
    foreach ($header as $cell) {
        $html .= '<th>' . ruined_admin_docs_md_inline($cell) . '</th>';
    }
    $html .= '</tr></thead><tbody>';

    for ($r = 2; $r < count($lines); $r++) {
        $cells = $parse_row($lines[$r]);
        $html .= '<tr>';
        foreach ($cells as $cell) {
            $html .= '<td>' . ruined_admin_docs_md_inline($cell) . '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</tbody></table></div>';

    return $html;
}

function ruined_admin_docs_md_inline($text) {
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text);
    $text = preg_replace('/`([^`]+?)`/', '<code>$1</code>', $text);
    $text = preg_replace('/!\[([^\]]*)\]\(([^)]+)\)/', '<img src="$2" alt="$1" loading="lazy">', $text);
    $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" rel="noopener">$1</a>', $text);
    return $text;
}

/**
 * Shared JS: sidebar nav + Cmd/Ctrl+K + full-content search (matches
 * section titles AND the actual text inside each section, not just
 * headings) + live <mark> highlighting of the query in the active section.
 * $has_toc toggles the mini table-of-contents wiring (standalone page only).
 */
function ruined_admin_docs_shared_script($has_toc = false) {
    ?>
    <script>
        (function () {
            var links    = document.querySelectorAll('[data-rv-nav]');
            var sections = document.querySelectorAll('[data-rv-section]');
            var search   = document.getElementById('rv-search');
            var tocList  = document.getElementById('rv-toc-list');
            var tocWrap  = document.getElementById('rv-toc');

            function buildToc(sectionEl) {
                if (!tocList) return;
                tocList.innerHTML = '';
                if (!sectionEl) { if (tocWrap) tocWrap.style.display = 'none'; return; }
                var heads = sectionEl.querySelectorAll('[data-toc]');
                if (!heads.length) { if (tocWrap) tocWrap.style.display = 'none'; return; }
                if (tocWrap) tocWrap.style.display = '';
                heads.forEach(function (h) {
                    var li = document.createElement('li');
                    li.className = 'rv-toc-level-' + h.dataset.tocLevel;
                    var a = document.createElement('a');
                    a.href = '#' + h.id;
                    a.textContent = h.textContent;
                    a.addEventListener('click', function (e) {
                        e.preventDefault();
                        h.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                    li.appendChild(a);
                    tocList.appendChild(li);
                });
            }

            function clearHighlights(root) {
                root.querySelectorAll('mark.rv-search-mark').forEach(function (m) {
                    var parent = m.parentNode;
                    parent.replaceChild(document.createTextNode(m.textContent), m);
                    parent.normalize();
                });
            }

            function highlightText(root, query) {
                if (!query) return;
                var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null);
                var nodes = [];
                var node;
                while ((node = walker.nextNode())) nodes.push(node);
                var q = query.toLowerCase();
                nodes.forEach(function (n) {
                    var text = n.nodeValue;
                    var lower = text.toLowerCase();
                    var idx = lower.indexOf(q);
                    if (idx === -1) return;
                    var frag = document.createDocumentFragment();
                    var lastIndex = 0;
                    while (idx !== -1) {
                        frag.appendChild(document.createTextNode(text.slice(lastIndex, idx)));
                        var mark = document.createElement('mark');
                        mark.className = 'rv-search-mark';
                        mark.textContent = text.slice(idx, idx + query.length);
                        frag.appendChild(mark);
                        lastIndex = idx + query.length;
                        idx = lower.indexOf(q, lastIndex);
                    }
                    frag.appendChild(document.createTextNode(text.slice(lastIndex)));
                    n.parentNode.replaceChild(frag, n);
                });
            }

            function activate(slug) {
                var activeSection = null;
                links.forEach(function (l) { l.classList.toggle('is-active', l.dataset.target === slug); });
                sections.forEach(function (s) {
                    var isActive = s.dataset.rvSection === slug;
                    s.classList.toggle('is-active', isActive);
                    if (isActive) activeSection = s;
                });
                if (activeSection) {
                    clearHighlights(activeSection);
                    if (search && search.value.trim()) {
                        highlightText(activeSection, search.value.trim());
                    }
                }
                if (window.__rvHasToc) buildToc(activeSection);
                return activeSection;
            }

            window.__rvHasToc = <?php echo $has_toc ? 'true' : 'false'; ?>;

            links.forEach(function (link) {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    activate(link.dataset.target);
                    history.replaceState(null, '', '#' + link.dataset.target);
                    var contentEl = document.querySelector('.rv-content, .rv-admin-docs__content');
                    if (contentEl) contentEl.scrollTop = 0;
                    window.scrollTo({ top: 0 });
                });
            });

            if (search) {
                search.addEventListener('input', function () {
                    var q = search.value.trim().toLowerCase();
                    var firstMatchSlug = null;
                    var currentActiveMatches = false;

                    links.forEach(function (link) {
                        var li = link.closest('li');
                        var slug = link.dataset.target;
                        var section = document.querySelector('[data-rv-section="' + slug + '"]');
                        var titleMatch = link.textContent.toLowerCase().indexOf(q) !== -1;
                        var contentMatch = section ? section.textContent.toLowerCase().indexOf(q) !== -1 : false;
                        var matches = q === '' || titleMatch || contentMatch;
                        li.style.display = matches ? '' : 'none';
                        if (matches && q !== '' && firstMatchSlug === null) firstMatchSlug = slug;
                        if (matches && link.classList.contains('is-active')) currentActiveMatches = true;
                    });

                    if (q !== '' && !currentActiveMatches && firstMatchSlug) {
                        activate(firstMatchSlug);
                        history.replaceState(null, '', '#' + firstMatchSlug);
                    } else {
                        var activeLink = document.querySelector('[data-rv-nav].is-active');
                        var activeSection = activeLink ? document.querySelector('[data-rv-section="' + activeLink.dataset.target + '"]') : null;
                        if (activeSection) {
                            clearHighlights(activeSection);
                            if (q !== '') highlightText(activeSection, q);
                        }
                    }
                });

                document.addEventListener('keydown', function (e) {
                    var isCmdK = (e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k';
                    if (isCmdK) {
                        e.preventDefault();
                        search.focus();
                        search.select();
                    }
                });
            }

            var initialSlug = window.location.hash ? window.location.hash.substring(1) : null;
            if (initialSlug && document.querySelector('[data-rv-section="' + initialSlug + '"]')) {
                activate(initialSlug);
            } else {
                var firstLink = links[0];
                if (firstLink) activate(firstLink.dataset.target);
            }
        })();
    </script>
    <?php
}

function ruined_render_admin_docs_page() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Δεν έχεις δικαίωμα πρόσβασης σε αυτή τη σελίδα.', 'ruined'));
    }

    $path = ruined_admin_docs_file_path();

    if (!file_exists($path)) {
        echo '<div class="wrap"><h1>Admin Documentation</h1><p>Το αρχείο <code>woocommerce-admin-docs.md</code> δεν βρέθηκε στο theme.</p></div>';
        return;
    }

    $markdown = ruined_admin_docs_resolve_assets_url(file_get_contents($path));
    $sections = ruined_admin_docs_get_sections($markdown);
    ?>
    <div class="wrap rv-admin-docs">
        <h1 class="rv-admin-docs__page-title">Admin Documentation</h1>

        <div class="rv-admin-docs__layout">
            <nav class="rv-admin-docs__sidebar" aria-label="Ενότητες τεκμηρίωσης">
                <div class="rv-search-wrap">
                    <input type="text" class="rv-search" id="rv-search" placeholder="Αναζήτηση…" autocomplete="off">
                    <kbd class="rv-kbd">⌘K</kbd>
                </div>
                <ul>
                    <?php foreach ($sections as $index => $section) :
                        $slug = sanitize_title($section['title']) ?: 'section-' . $index;
                        ?>
                        <li>
                            <a href="#<?php echo esc_attr($slug); ?>" data-rv-nav
                               class="rv-admin-docs__nav-link<?php echo $index === 0 ? ' is-active' : ''; ?>"
                               data-target="<?php echo esc_attr($slug); ?>">
                                <?php echo esc_html($section['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <div class="rv-admin-docs__content">
                <?php foreach ($sections as $index => $section) :
                    $slug = sanitize_title($section['title']) ?: 'section-' . $index;
                    ?>
                    <section id="<?php echo esc_attr($slug); ?>" data-rv-section="<?php echo esc_attr($slug); ?>"
                             class="rv-admin-docs__section<?php echo $index === 0 ? ' is-active' : ''; ?>">
                        <?php echo ruined_admin_docs_md_to_html($section['body'], $slug); ?>
                    </section>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <style>
        .rv-admin-docs__page-title { margin-bottom: 16px; }
        .rv-admin-docs__layout { display: flex; align-items: flex-start; gap: 24px; }
        .rv-admin-docs__sidebar {
            flex: 0 0 270px; position: sticky; top: 40px; background: #fff; border: 1px solid #dcdcde;
            border-radius: 4px; padding: 8px; max-height: calc(100vh - 80px); overflow-y: auto;
        }
        .rv-search-wrap { position: relative; margin-bottom: 8px; }
        .rv-search { width: 100%; padding: 8px 40px 8px 10px; border: 1px solid #dcdcde; border-radius: 4px; font-size: 13px; box-sizing: border-box; }
        .rv-kbd { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); font-size: 10px; color: #8c8f94; border: 1px solid #dcdcde; border-radius: 3px; padding: 1px 5px; background: #f6f7f7; pointer-events: none; font-family: inherit; }
        .rv-admin-docs__sidebar ul { margin: 0; padding: 0; list-style: none; }
        .rv-admin-docs__nav-link { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 4px; color: #1d2327; text-decoration: none; font-size: 13px; line-height: 1.4; }
        .rv-admin-docs__nav-link:hover { background: #f0f0f1; }
        .rv-admin-docs__nav-link.is-active { background: #171717; color: #fff; font-weight: 600; }
        .rv-admin-docs__content { flex: 1 1 auto; background: #fff; border: 1px solid #dcdcde; border-radius: 4px; padding: 28px 36px; max-width: 900px; }
        .rv-admin-docs__section { display: none; }
        .rv-admin-docs__section.is-active { display: block; }
        .rv-admin-docs__content h1 { font-size: 22px; margin-top: 0; }
        .rv-admin-docs__content h2 { font-size: 19px; border-bottom: 1px solid #dcdcde; padding-bottom: 8px; margin-top: 0; }
        .rv-admin-docs__content h3 { font-size: 15px; margin-top: 28px; }
        .rv-admin-docs__content h4 { font-size: 13px; text-transform: uppercase; letter-spacing: .04em; color: #646970; margin: 24px 0 8px; }
        .rv-admin-docs__content p { font-size: 13px; line-height: 1.7; color: #3c434a; }
        .rv-admin-docs__content code { background: #f0f0f1; padding: 1px 5px; border-radius: 3px; font-size: 12px; }
        .rv-admin-docs__content pre { background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 4px; padding: 12px; overflow-x: auto; }
        .rv-admin-docs__content blockquote { margin: 0 0 16px; padding: 10px 16px; border-left: 3px solid #17B83A; background: #f6f7f7; border-radius: 0 4px 4px 0; }
        .rv-admin-docs__content blockquote p { margin: 6px 0; }
        .rv-admin-docs__content ul, .rv-admin-docs__content ol { font-size: 13px; line-height: 1.7; color: #3c434a; padding-left: 22px; }
        .rv-admin-docs__content li { margin-bottom: 6px; }
        .rv-admin-docs__content hr { border: none; border-top: 1px solid #dcdcde; margin: 28px 0; }
        .rv-doc-table-wrap { overflow-x: auto; margin-bottom: 20px; }
        .rv-admin-docs__content table { border-collapse: collapse; width: 100%; font-size: 12.5px; }
        .rv-admin-docs__content th, .rv-admin-docs__content td { border: 1px solid #dcdcde; padding: 8px 10px; text-align: left; vertical-align: top; }
        .rv-admin-docs__content th { background: #f0f0f1; }
        .rv-admin-docs__content tr:nth-child(even) td { background: #fafafa; }
        .rv-doc-figure { margin: 0 0 20px; }
        .rv-doc-figure img { max-width: 100%; height: auto; display: block; border-radius: 6px; border: 1px solid #dcdcde; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        mark.rv-search-mark { background: #fff2a8; color: inherit; border-radius: 2px; padding: 0 1px; }
        @media (max-width: 900px) {
            .rv-admin-docs__layout { flex-direction: column; }
            .rv-admin-docs__sidebar { position: static; flex: 1 1 auto; width: 100%; max-height: none; }
        }
    </style>
    <?php
    ruined_admin_docs_shared_script(false);
}

/**
 * Full standalone HTML document for the /sima-docs route — dark topbar with
 * a subtle diagonal-beam pattern (echoing the site's own hero section),
 * green accent, Aeonik Pro font, sidebar with icons + search, and a
 * right-hand mini table-of-contents for the active section.
 */
function ruined_render_sima_docs_standalone_page() {
    $path = ruined_admin_docs_file_path();

    if (!file_exists($path)) {
        wp_die(esc_html__('Το αρχείο woocommerce-admin-docs.md δεν βρέθηκε στο theme.', 'ruined'));
    }

    $markdown = ruined_admin_docs_resolve_assets_url(file_get_contents($path));
    $sections = ruined_admin_docs_get_sections($markdown);
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>Admin Documentation — <?php bloginfo('name'); ?></title>
        <style>
            @font-face {
                font-family: 'Aeonik Pro';
                src: url('<?php echo esc_url(get_template_directory_uri()); ?>/src/fonts/AeonikPro/AeonikProRegular.woff2') format('woff2'),
                url('<?php echo esc_url(get_template_directory_uri()); ?>/src/fonts/AeonikPro/AeonikProRegular.woff') format('woff');
                font-weight: 400 700;
                font-style: normal;
                font-display: swap;
            }

            :root {
                --ink: #171717;
                --muted: #666666;
                --muted-light: #999999;
                --line: #e9e9e9;
                --accent: #17B83A;
                --accent-dark: #0d9f2e;
                --accent-tint: rgba(23, 184, 58, .08);
                --accent-tint-strong: rgba(23, 184, 58, .14);
                --page-bg: #f6f6f6;
                --card: #ffffff;
                --radius: 12px;
                --radius-sm: 6px;
                --font: 'Aeonik Pro', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            }
            * { box-sizing: border-box; }
            html { scroll-behavior: smooth; }
            body { margin: 0; background: var(--page-bg); color: var(--ink); font-family: var(--font); -webkit-font-smoothing: antialiased; }

            ::-webkit-scrollbar { width: 8px; height: 8px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cfcfcf; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: var(--ink); }

            /* Topbar — dark base with a subtle green diagonal-beam pattern,
               echoing the hero section on the main site. */
            .rv-topbar {
                position: sticky; top: 0; z-index: 10; overflow: hidden;
                background:
                        repeating-linear-gradient(115deg, rgba(23,184,58,.10) 0px, rgba(23,184,58,.10) 2px, transparent 2px, transparent 46px),
                        linear-gradient(135deg, #0d1a10 0%, #171717 55%, #101010 100%);
                color: #fff; padding: 16px 32px;
                display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
                border-bottom: 1px solid rgba(23,184,58,.25);
            }
            .rv-topbar-brand { display: flex; align-items: center; gap: 14px; position: relative; z-index: 1; }
            .rv-topbar-logo.site-branding { display: flex; align-items: center; }
            .rv-topbar-logo img { display: block; max-height: 26px; width: auto; filter: brightness(0) invert(1); }
            .rv-topbar-sitename { color: #fff; font-weight: 700; font-size: 15px; text-decoration: none; }
            .rv-topbar-divider { width: 1px; height: 22px; background: #3a3a3a; flex: none; }
            .rv-topbar-label { font-size: 13px; font-weight: 500; color: #b8b8b8; }
            .rv-topbar a.rv-back {
                position: relative; z-index: 1;
                color: #d4d4d4; text-decoration: none; font-size: 13px; font-weight: 500;
                display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 999px;
                border: 1px solid #3a3a3a; transition: all .18s ease;
            }
            .rv-topbar a.rv-back:hover { color: #fff; border-color: var(--accent); background: rgba(23,184,58,.12); }

            .rv-layout { display: flex; align-items: flex-start; gap: 24px; padding: 28px 32px 60px; max-width: 1600px; margin: 0 auto; }

            .rv-sidebar {
                flex: 0 0 264px; position: sticky; top: 88px;
                background: var(--card); border: 1px solid var(--line); box-shadow: 0 1px 2px rgba(0,0,0,.03);
                border-radius: var(--radius); padding: 14px; max-height: calc(100vh - 110px); overflow-y: auto;
            }
            .rv-search-wrap { position: relative; margin-bottom: 10px; }
            .rv-search {
                width: 100%; padding: 9px 46px 9px 12px; border: 1px solid var(--line); border-radius: var(--radius-sm);
                font-family: var(--font); font-size: 13px; color: var(--ink); background: var(--page-bg);
                outline: none; transition: border-color .15s ease, background .15s ease; box-sizing: border-box;
            }
            .rv-search:focus { border-color: var(--accent); background: #fff; }
            .rv-search::placeholder { color: var(--muted-light); }
            .rv-kbd {
                position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
                font-size: 10.5px; color: var(--muted-light); border: 1px solid var(--line); border-radius: 4px;
                padding: 2px 6px; background: #fff; pointer-events: none; font-family: inherit;
            }

            .rv-sidebar ul { margin: 0; padding: 0; list-style: none; }
            .rv-nav-link {
                display: flex; align-items: center; gap: 8px; padding: 9px 12px; margin-bottom: 2px; border-radius: var(--radius-sm);
                color: #454545; text-decoration: none; font-size: 13px; font-weight: 500; line-height: 1.4;
                border-left: 3px solid transparent; transition: background .15s ease, color .15s ease, border-color .15s ease;
            }
                .rv-nav-link:hover { background: var(--page-bg); color: var(--ink); }
            .rv-nav-link.is-active { background: var(--accent-tint); color: var(--ink); font-weight: 700; border-left-color: var(--accent); }

            .rv-content {
                flex: 1 1 auto; min-width: 0; background: var(--card); border: 1px solid var(--line); box-shadow: 0 1px 2px rgba(0,0,0,.03);
                border-radius: var(--radius); padding: 40px 48px; max-width: 860px;
            }
            .rv-section { display: none; animation: rv-fade .25s ease; }
            .rv-section.is-active { display: block; }
            @keyframes rv-fade { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }

            .rv-content h1 { font-size: 26px; font-weight: 700; margin: 0 0 6px; letter-spacing: -.01em; }
            .rv-content h2 { font-size: 21px; font-weight: 700; margin: 0 0 22px; padding-bottom: 14px; border-bottom: 1px solid var(--line); letter-spacing: -.01em; }
            .rv-content h3 { font-size: 15.5px; font-weight: 700; margin: 32px 0 12px; color: var(--ink); }
            .rv-content h4 { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--accent-dark); margin: 26px 0 10px; }
            .rv-content p { font-size: 14px; line-height: 1.75; color: var(--muted); margin: 0 0 16px; }
            .rv-content a { color: var(--accent-dark); text-decoration: none; border-bottom: 1px solid rgba(23,184,58,.35); }
            .rv-content a:hover { color: var(--accent); border-bottom-color: var(--accent); }
            .rv-content code { background: var(--page-bg); border: 1px solid var(--line); padding: 1.5px 6px; border-radius: 4px; font-size: 12.5px; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace; color: #c0255e; }
            .rv-content pre { background: var(--ink); border-radius: var(--radius-sm); padding: 16px 18px; overflow-x: auto; margin: 0 0 18px; }
            .rv-content pre code { background: none; border: none; color: #eaeaea; padding: 0; }
            .rv-content blockquote { margin: 0 0 20px; padding: 14px 20px; border-left: 3px solid var(--accent); background: var(--accent-tint); border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }
            .rv-content blockquote p { margin: 6px 0; color: #3d3d3d; }
            .rv-content blockquote p:first-child { margin-top: 0; }
            .rv-content blockquote p:last-child { margin-bottom: 0; }
            .rv-content ul, .rv-content ol { font-size: 14px; line-height: 1.75; color: var(--muted); padding-left: 22px; margin: 0 0 16px; }
            .rv-content li { margin-bottom: 6px; }
            .rv-content li::marker { color: var(--accent-dark); }
            .rv-content hr { border: none; border-top: 1px solid var(--line); margin: 34px 0; }

            .rv-table-wrap { overflow-x: auto; margin: 0 0 22px; border: 1px solid var(--line); border-radius: var(--radius-sm); }
            .rv-content table { border-collapse: collapse; width: 100%; font-size: 13px; }
            .rv-content th, .rv-content td { padding: 10px 14px; text-align: left; vertical-align: top; border-bottom: 1px solid var(--line); }
            .rv-content th { background: var(--ink); color: #fff; font-weight: 600; font-size: 11.5px; text-transform: uppercase; letter-spacing: .04em; }
            .rv-content tr:last-child td { border-bottom: none; }
            .rv-content tbody tr:hover td { background: var(--accent-tint); }
            .rv-content strong { color: var(--ink); font-weight: 700; }

            .rv-doc-figure { margin: 0 0 24px; }
            .rv-doc-figure img { max-width: 100%; height: auto; display: block; border-radius: var(--radius-sm); border: 1px solid var(--line); box-shadow: 0 4px 16px rgba(0,0,0,.08); }

            mark.rv-search-mark { background: #fff2a8; color: inherit; border-radius: 2px; padding: 0 1px; }

            /* Right-hand mini table-of-contents */
            .rv-toc {
                flex: 0 0 220px; position: sticky; top: 88px; max-height: calc(100vh - 110px); overflow-y: auto;
                padding: 4px 0 4px 16px; border-left: 1px solid var(--line);
            }
            .rv-toc-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: var(--muted-light); margin: 0 0 10px; }
            .rv-toc ul { list-style: none; margin: 0; padding: 0; }
            .rv-toc li { margin-bottom: 2px; }
            .rv-toc a {
                display: block; padding: 5px 0; font-size: 12.5px; color: var(--muted); text-decoration: none;
                border-left: 2px solid transparent; padding-left: 10px; transition: color .15s ease, border-color .15s ease;
            }
            .rv-toc a:hover { color: var(--ink); border-left-color: var(--accent); }
            .rv-toc-level-4 a { padding-left: 20px; font-size: 11.5px; color: var(--muted-light); }

            @media (max-width: 1200px) { .rv-toc { display: none; } }
            @media (max-width: 900px) {
                .rv-layout { flex-direction: column; padding: 20px 16px 40px; }
                .rv-sidebar { position: static; flex: 1 1 auto; width: 100%; max-height: none; }
                .rv-content { padding: 28px 24px; max-width: 100%; }
            }
        </style>
    </head>
    <body>
    <div class="rv-topbar">
        <div class="rv-topbar-brand">
            <div class="site-branding rv-topbar-logo">
                <?php if (has_custom_logo()) : the_custom_logo(); else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="rv-topbar-sitename"><?php bloginfo('name'); ?></a>
                <?php endif; ?>
            </div>
            <span class="rv-topbar-divider" aria-hidden="true"></span>
            <span class="rv-topbar-label">Admin Documentation</span>
        </div>
        <a class="rv-back" href="<?php echo esc_url(home_url('/')); ?>">← Επιστροφή στο site</a>
    </div>

    <div class="rv-layout">
        <nav class="rv-sidebar" aria-label="Ενότητες τεκμηρίωσης">
            <div class="rv-search-wrap">
                <input type="text" class="rv-search" id="rv-search" placeholder="Αναζήτηση…" autocomplete="off">
                <kbd class="rv-kbd">⌘K</kbd>
            </div>
            <ul id="rv-nav-list">
                <?php foreach ($sections as $index => $section) :
                    $slug = sanitize_title($section['title']) ?: 'section-' . $index;
                    ?>
                    <li>
                        <a href="#<?php echo esc_attr($slug); ?>" data-rv-nav
                           class="rv-nav-link<?php echo $index === 0 ? ' is-active' : ''; ?>"
                           data-target="<?php echo esc_attr($slug); ?>">
                            <?php echo esc_html($section['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="rv-content">
            <?php foreach ($sections as $index => $section) :
                $slug = sanitize_title($section['title']) ?: 'section-' . $index;
                ?>
                <section id="<?php echo esc_attr($slug); ?>" data-rv-section="<?php echo esc_attr($slug); ?>"
                         class="rv-section<?php echo $index === 0 ? ' is-active' : ''; ?>">
                    <?php echo ruined_admin_docs_md_to_html($section['body'], $slug); ?>
                </section>
            <?php endforeach; ?>
        </div>

        <aside class="rv-toc" id="rv-toc">
            <p class="rv-toc-label">Σε αυτή τη σελίδα</p>
            <ul id="rv-toc-list"></ul>
        </aside>
    </div>

    <?php ruined_admin_docs_shared_script(true); ?>
    </body>
    </html>
    <?php
}