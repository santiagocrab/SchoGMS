<?php
/**
 * Lean CSS/JS for chairman pages (sidebar + datatables + sweetalert).
 */
require_once __DIR__ . '/../../../inc/schogms_app_scripts.php';

if (!function_exists('schogms_chairman_head')) {
    function schogms_chairman_head(bool $datatables = false): void
    {
        echo '<link href="../../dist/css/style.min.css" rel="stylesheet">' . "\n";
        echo '<style>.preloader{display:none!important}#main-wrapper{opacity:1!important}.topbar{position:relative;z-index:1030}.topbar .dropdown-menu{z-index:1050}</style>' . "\n";
        if ($datatables) {
            echo '<link href="../../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">' . "\n";
        }
    }
}

if (!function_exists('schogms_chairman_footer_scripts')) {
    /** @param array{datatables?:bool,sweetalert?:bool} $opts */
    function schogms_chairman_footer_scripts(array $opts = []): void
    {
        static $datatablesDone = false;
        static $sweetalertDone = false;
        static $notificationsDone = false;

        $datatables = !empty($opts['datatables']);
        $sweetalert = !empty($opts['sweetalert']);

        schogms_app_emit_core_scripts();

        if ($sweetalert && !$sweetalertDone) {
            $sweetalertDone = true;
            echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">' . "\n";
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>' . "\n";
        }
        if ($datatables && !$datatablesDone) {
            $datatablesDone = true;
            $base = schogms_app_assets_base();
            echo '<script src="' . htmlspecialchars($base, ENT_QUOTES, 'UTF-8') . '/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>' . "\n";
            echo '<script src="' . htmlspecialchars($base, ENT_QUOTES, 'UTF-8') . '/extra-libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>' . "\n";
            echo '<script>jQuery(function(){if(!jQuery.fn.DataTable)return;var o={pageLength:25,lengthMenu:[[10,25,50,100,-1],[10,25,50,100,"All"]],deferRender:true,processing:true,stateSave:false,order:[]};jQuery("#zero_config, #reqTable, #annex7Table").each(function(){if(!jQuery.fn.dataTable.isDataTable(this))jQuery(this).DataTable(o);});if(typeof window.schogmsInitTopbarUi==="function")window.schogmsInitTopbarUi();});</script>' . "\n";
        }
        if (!$notificationsDone) {
            $notificationsDone = true;
            if (!function_exists('schogms_notifications_footer_script')) {
                require_once __DIR__ . '/../../../inc/schogms_notifications_ui.php';
            }
            schogms_notifications_footer_script();
        }
    }
}
