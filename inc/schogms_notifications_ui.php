<?php
/**
 * Notification bell dropdown (chairman, coordinator, registrar).
 */

require_once __DIR__ . '/schogms_notifications.php';

if (!function_exists('schogms_notifications_render_bell')) {
    function schogms_notifications_render_bell(mysqli $conn, int $userId, string $role): void
    {
        if (!schogms_notifications_role_show_bell($role) || $userId < 1) {
            return;
        }

        $unread = schogms_notifications_unread_count($conn, $userId);
        $badge = $unread > 0
            ? '<span class="badge badge-danger schogms-notif-badge">' . ($unread > 99 ? '99+' : (string) $unread) . '</span>'
            : '';
        ?>
        <li class="nav-item dropdown schogms-notif-wrap">
            <a class="nav-link schogms-notif-toggle" href="javascript:void(0)" id="schogmsNotifToggle"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Notifications">
                <i data-feather="bell" class="schogms-notif-icon"></i>
                <?= $badge ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right schogms-notif-menu p-0 animated fadeIn"
                 aria-labelledby="schogmsNotifToggle">
                <div class="schogms-notif-header px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                    <strong class="text-dark">Notifications</strong>
                    <button type="button" class="btn btn-link btn-sm p-0 schogms-notif-mark-all" style="font-size:0.75rem;">Mark all read</button>
                </div>
                <div class="schogms-notif-list" style="max-height:320px;overflow-y:auto;">
                    <div class="px-3 py-3 text-muted small text-center schogms-notif-loading">Loading…</div>
                </div>
            </div>
        </li>
        <style>
            .schogms-notif-wrap { position: relative; }
            .schogms-notif-toggle { position: relative; padding: 0.5rem 0.75rem !important; }
            .schogms-notif-icon { width: 20px; height: 20px; }
            .schogms-notif-badge {
                position: absolute; top: 4px; right: 2px;
                font-size: 0.65rem; padding: 0.15em 0.45em; border-radius: 10px;
            }
            .schogms-notif-menu { width: 340px; max-width: 95vw; }
            .schogms-notif-item {
                display: block; padding: 10px 14px; border-bottom: 1px solid #eef2f8;
                color: #1e293b; text-decoration: none !important;
            }
            .schogms-notif-item:hover { background: #f8fafc; }
            .schogms-notif-item.unread { background: #eef2ff; border-left: 3px solid #5f76e8; }
            .schogms-notif-item .schogms-notif-title { font-weight: 600; font-size: 0.82rem; margin-bottom: 2px; }
            .schogms-notif-item .schogms-notif-msg { font-size: 0.75rem; color: #64748b; margin: 0; line-height: 1.35; }
            .schogms-notif-item .schogms-notif-time { font-size: 0.68rem; color: #94a3b8; margin-top: 4px; }
        </style>
        <?php
    }
}

if (!function_exists('schogms_notifications_footer_script')) {
    function schogms_notifications_footer_script(): void
    {
        ?>
        <script>
        (function () {
            var apiUrl = '../notifications_api.php';
            var toggle = document.getElementById('schogmsNotifToggle');
            if (!toggle) return;

            var listEl = document.querySelector('.schogms-notif-list');
            var markAllBtn = document.querySelector('.schogms-notif-mark-all');
            var badgeEl = document.querySelector('.schogms-notif-badge');

            function escapeHtml(s) {
                var d = document.createElement('div');
                d.textContent = s;
                return d.innerHTML;
            }

            function updateBadge(count) {
                if (!badgeEl && count > 0) {
                    var wrap = toggle.closest('.schogms-notif-wrap');
                    if (wrap) {
                        badgeEl = document.createElement('span');
                        badgeEl.className = 'badge badge-danger schogms-notif-badge';
                        toggle.appendChild(badgeEl);
                    }
                }
                if (!badgeEl) return;
                if (count < 1) {
                    badgeEl.remove();
                    badgeEl = null;
                } else {
                    badgeEl.textContent = count > 99 ? '99+' : String(count);
                }
            }

            function renderList(items) {
                if (!listEl) return;
                if (!items || !items.length) {
                    listEl.innerHTML = '<div class="px-3 py-3 text-muted small text-center">No notifications yet.</div>';
                    return;
                }
                listEl.innerHTML = items.map(function (n) {
                    var href = n.link_url ? escapeHtml(n.link_url) : '#';
                    var cls = n.is_read === '0' || n.is_read === 0 ? ' schogms-notif-item unread' : ' schogms-notif-item';
                    return '<a href="' + href + '" class="' + cls.trim() + '" data-id="' + escapeHtml(String(n.id)) + '">'
                        + '<div class="schogms-notif-title">' + escapeHtml(n.title || '') + '</div>'
                        + '<p class="schogms-notif-msg">' + escapeHtml(n.message || '') + '</p>'
                        + '<div class="schogms-notif-time">' + escapeHtml(n.created_at || '') + '</div>'
                        + '</a>';
                }).join('');
            }

            function loadNotifications() {
                fetch(apiUrl + '?action=list', { credentials: 'same-origin' })
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.success) {
                            if (listEl) listEl.innerHTML = '<div class="px-3 py-2 text-danger small">Could not load notifications.</div>';
                            return;
                        }
                        renderList(data.items || []);
                        updateBadge(data.unread || 0);
                    })
                    .catch(function () {
                        if (listEl) listEl.innerHTML = '<div class="px-3 py-2 text-danger small">Could not load notifications.</div>';
                    });
            }

            toggle.addEventListener('click', function () {
                setTimeout(loadNotifications, 80);
            });

            if (listEl) {
                listEl.addEventListener('click', function (e) {
                    var a = e.target.closest('a[data-id]');
                    if (!a) return;
                    var id = a.getAttribute('data-id');
                    var fd = new FormData();
                    fd.append('action', 'mark_read');
                    fd.append('id', id);
                    fetch(apiUrl, { method: 'POST', body: fd, credentials: 'same-origin' });
                });
            }

            if (markAllBtn) {
                markAllBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var fd = new FormData();
                    fd.append('action', 'mark_all_read');
                    fetch(apiUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
                        .then(function () { loadNotifications(); });
                });
            }

            loadNotifications();
            setInterval(loadNotifications, 120000);
        })();
        </script>
        <?php
    }
}
