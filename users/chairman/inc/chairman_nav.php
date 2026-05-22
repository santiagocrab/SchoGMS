<?php
/**
 * Unified chairman top bar + sidebar (same menu on every page).
 */

if (!function_exists('schogms_chairman_nav_script')) {
    function schogms_chairman_nav_script(): string
    {
        return basename($_SERVER['SCRIPT_NAME'] ?? '');
    }
}

if (!function_exists('schogms_chairman_nav_active')) {
    function schogms_chairman_nav_active(string $script, array $aliases = []): bool
    {
        $current = schogms_chairman_nav_script();

        return $current === $script || in_array($current, $aliases, true);
    }
}

if (!function_exists('schogms_chairman_nav_link_class')) {
    function schogms_chairman_nav_link_class(string $script, array $aliases = []): string
    {
        $class = 'sidebar-link sidebar-link';

        return schogms_chairman_nav_active($script, $aliases)
            ? $class . ' active'
            : $class;
    }
}

if (!function_exists('schogms_chairman_render_topbar')) {
    function schogms_chairman_render_topbar(?string $pageTitle = null): void
    {
        global $fullname;
        $title = $pageTitle ?? 'Scholarship and Grants Management System';
        $user = htmlspecialchars((string) ($fullname ?? $_SESSION['username'] ?? 'Chairman'), ENT_QUOTES, 'UTF-8');
        ?>
        <header class="topbar" data-navbarbg="skin6">
            <nav class="navbar top-navbar navbar-expand-md">
                <div class="navbar-header" data-logobg="skin6">
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                        <i class="ti-menu ti-close"></i>
                    </a>
                    <div class="navbar-brand">
                        <a href="index.php">
                            <img src="../../assets/images/logo.png" style="height:auto;width:200px" alt="SchoGMS" class="dark-logo">
                        </a>
                    </div>
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti-more"></i>
                    </a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav float-left mr-auto ml-3 pl-1">
                        <li class="nav-item">
                            <h3 class="page-title text-truncate text-dark font-weight-medium mb-1"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>
                        </li>
                    </ul>
                    <ul class="navbar-nav float-right">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <img src="../../assets/images/users/image.png" alt="user" class="rounded-circle" width="40">
                                <span class="ml-2 d-none d-lg-inline-block"><span>Hello,</span>
                                    <span class="text-dark"><?= $user ?></span>
                                    <i data-feather="chevron-down" class="svg-icon"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <a class="dropdown-item" href="change_password.php">
                                    <i data-feather="key" class="svg-icon mr-2 ml-1"></i> Change Password
                                </a>
                                <a class="dropdown-item" href="logout.php">
                                    <i data-feather="power" class="svg-icon mr-2 ml-1"></i> Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <?php
    }
}

if (!function_exists('schogms_chairman_render_sidebar')) {
    function schogms_chairman_render_sidebar(): void
    {
        ?>
        <aside class="left-sidebar" data-sidebarbg="skin6">
            <div class="scroll-sidebar" data-sidebarbg="skin6">
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                            <a class="<?= schogms_chairman_nav_link_class('index.php') ?>" href="index.php">
                                <i data-feather="home" class="feather-icon"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Annex 7</span></li>
                        <li class="sidebar-item">
                            <a class="<?= schogms_chairman_nav_link_class('anex-form2.php') ?>" href="anex-form2.php">
                                <i data-feather="check-square" class="feather-icon"></i>
                                <span class="hide-menu">Review submissions</span>
                            </a>
                        </li>

                        <li class="list-divider"></li>
                        <li class="nav-small-cap"><span class="hide-menu">Masterlists</span></li>
                        <li class="sidebar-item">
                            <a class="<?= schogms_chairman_nav_link_class('ched_masterlist.php') ?>" href="ched_masterlist.php">
                                <i data-feather="list" class="feather-icon"></i>
                                <span class="hide-menu">TDP masterlist</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="<?= schogms_chairman_nav_link_class('ched_masterlist_tes.php') ?>" href="ched_masterlist_tes.php">
                                <i data-feather="list" class="feather-icon"></i>
                                <span class="hide-menu">TES masterlist</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="<?= schogms_chairman_nav_link_class('masterlist.php') ?>" href="masterlist.php">
                                <i data-feather="users" class="feather-icon"></i>
                                <span class="hide-menu">Registrar masterlist</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="<?= schogms_chairman_nav_link_class('upload_ched_tdp.php') ?>" href="upload_ched_tdp.php">
                                <i data-feather="upload" class="feather-icon"></i>
                                <span class="hide-menu">Upload TDP</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="<?= schogms_chairman_nav_link_class('program_list.php') ?>" href="program_list.php">
                                <i data-feather="folder" class="feather-icon"></i>
                                <span class="hide-menu">Program list</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="<?= schogms_chairman_nav_link_class('verified-scholars.php') ?>" href="verified-scholars.php">
                                <i data-feather="users" class="feather-icon"></i>
                                <span class="hide-menu">Verified scholars</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <?php
    }
}

if (!function_exists('schogms_chairman_shell_open')) {
    function schogms_chairman_shell_open(?string $pageTitle = null): void
    {
        schogms_loading_screen_once();
        ?>
        <div id="main-wrapper" data-theme="light" data-layout="vertical" data-navbarbg="skin6"
            data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <?php
        schogms_chairman_render_topbar($pageTitle);
        schogms_chairman_render_sidebar();
        ?>
        <div class="page-wrapper">
        <?php
    }
}

if (!function_exists('schogms_chairman_shell_close')) {
    function schogms_chairman_shell_close(): void
    {
        echo "</div><!-- /.page-wrapper -->\n</div><!-- /#main-wrapper -->\n";
    }
}
