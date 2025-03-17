<?php


/* =========================
   ESTILOS DE ADMIN
   ========================= */
function savour_manager_admin_bar_style()
{
    echo '<style>
        #wpadminbar {
            background-color: #212121 !important;
        }
        #wpadminbar .ab-item, #wpadminbar a.ab-item {
            color: white !important;
        }
        #wpadminbar .ab-item:hover, #wpadminbar a.ab-item:hover {
            background-color: #ed9032 !important;
        }
    </style>';
}
add_action('wp_head', 'savour_manager_admin_bar_style');
add_action('admin_head', 'savour_manager_admin_bar_style');

function savour_manager_admin_menu_style()
{
    echo '<style>
        #toplevel_page_configurador-sitio {
            background-color: #ea0f0f;
        }
        #toplevel_page_configurador-sitio a {
            color: white !important;
        }
        #toplevel_page_configurador-sitio a:hover,
        #toplevel_page_configurador-sitio a:active {
            background-color: #981e1e !important;
        }
    </style>';
}
add_action('admin_head', 'savour_manager_admin_menu_style');

function savour_manager_icon_color()
{
    echo '<style>
        #adminmenu .toplevel_page_configurador-sitio .wp-menu-image:before {
            font-family: "dashicons";
            content: "\f527"; /* dashicon ghost */
            color: white !important;
        }
    </style>';
}
add_action('admin_head', 'savour_manager_icon_color');

function savour_manager_active_menu_color()
{
    echo '<style>
        #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head,
        #adminmenu .wp-menu-arrow,
        #adminmenu .wp-menu-arrow div,
        #adminmenu li.current a.menu-top,
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu {
            background-color: #ea0f0f !important;
            color: white !important;
            font-size:15px;
        }
    </style>';
}
add_action('admin_head', 'savour_manager_active_menu_color');
