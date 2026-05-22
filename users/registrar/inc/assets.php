<?php
/**
 * Lean CSS/JS bundle for registrar pages.
 */
if (!function_exists('schogms_registrar_head')) {
    function schogms_registrar_head(bool $datatables = false): void
    {
        echo '<link href="../../dist/css/style.min.css" rel="stylesheet">' . "\n";
        echo '<style>.preloader{display:none!important}#main-wrapper{opacity:1!important}</style>' . "\n";
        if ($datatables) {
            echo '<link href="../../assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">' . "\n";
        }
    }
}

if (!function_exists('schogms_registrar_footer_scripts')) {
    /**
     * @param array{datatables?:bool,sweetalert?:bool,chart?:bool} $opts
     */
    function schogms_registrar_footer_scripts(array $opts = []): void
    {
        $datatables = !empty($opts['datatables']);
        $sweetalert = !empty($opts['sweetalert']);
        $chart = !empty($opts['chart']);

        echo '<script src="../../assets/libs/jquery/dist/jquery.min.js"></script>' . "\n";
        echo '<script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>' . "\n";
        echo '<script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>' . "\n";
        echo '<script src="../../dist/js/app-style-switcher.js"></script>' . "\n";
        echo '<script src="../../dist/js/feather.min.js"></script>' . "\n";
        echo '<script src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>' . "\n";
        echo '<script src="../../dist/js/sidebarmenu.js"></script>' . "\n";
        echo '<script src="../../dist/js/custom.min.js"></script>' . "\n";

        if ($sweetalert) {
            echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">' . "\n";
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>' . "\n";
        }
        if ($chart) {
            echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>' . "\n";
        }
        if ($datatables) {
            echo '<script src="../../assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>' . "\n";
            echo '<script src="../../assets/extra-libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>' . "\n";
            echo '<script>$(function(){if(!$.fn.DataTable)return;var o={pageLength:25,lengthMenu:[[10,25,50,100,-1],[10,25,50,100,"All"]],deferRender:true,processing:true,stateSave:false,order:[]};$("#zero_config, #reqTable, #documentsTable").each(function(){if(!$.fn.dataTable.isDataTable(this))$(this).DataTable(o);});});</script>' . "\n";
        }
        echo '<script>if(typeof feather!=="undefined"){feather.replace();}</script>' . "\n";
        if (!function_exists('schogms_notifications_footer_script')) {
            require_once __DIR__ . '/../../../inc/schogms_notifications_ui.php';
        }
        schogms_notifications_footer_script();
    }
}
