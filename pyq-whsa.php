<?php
/**
* Plugin Name: Werth-Holz S.A. - relacje inwestorskie
* Description: Dedykowana wtyczka dla do zarządzania relacjami inwestorskimi
* Version: 0.1.1
* Author: Jakub Michalik - pyq.pl
* Text Domain: pyq-whsa
* Author URI: https://pyq.pl
* Contributors: Jakub Michalik
*/


require_once('config.php');
require_once( MY_ACF_PATH . 'acf.php' );

flush_rewrite_rules();
function pyqplconfig_register_plugin_styles() {
    wp_enqueue_style( 'custom-styles', plugin_dir_url( __FILE__ ) . 'css/style.css' );
}

function pyqplconfig_register_plugin_scripts() {
    wp_enqueue_script( 'custom-javascript', plugin_dir_url( __FILE__ ) . 'js/script.js' );
}


// Customize the url setting to fix incorrect asset URLs.
add_filter('acf/settings/url', 'my_acf_settings_url');
function my_acf_settings_url( $url ) {
    return MY_ACF_URL;
}

// (Optional) Hide the ACF admin menu item.
add_filter('acf/settings/show_admin', 'my_acf_settings_show_admin');
function my_acf_settings_show_admin( $show_admin ) {
    console.log("pyq.pl - ACF Loaded");
    return true;
}

/**
 *  Sprawdzenie istnienia wymaganych dodatkowych wtyczek 
 **/   
function pyqplconfig_plugins_dependencies() {
    
    //Advanced Custom Fields
    if( ! function_exists('get_field') )
    {   
        echo '<div class="error" style="color: red; font-weight: bold;"><p>' . __( 'Wtyczka "'.PYQ_PLUGIN_NAME.'" wymaga zainstalowanej i włączonej wtyczki "Advanced Custom Fields"!<br>', PYQ_WP_PLUGIN_TEXTDOMAIN) . '</p></div>';
        return false;
    }
}
add_action( 'admin_notices', 'pyqplconfig_plugins_dependencies' );

/**
 *  Sprawdzenie czy wybrano podstronę która ma wyświetlać konfigurator
 **/ 
function pyqpl_page_check() {
    
    $pyqpl_configurator_pageid = get_field('pyqpl_selected_page_main_template','option');
    if(! $pyqpl_configurator_pageid)
    {
        //Jesli nie ustawiono w opcjach podstrony dla konfiguratora
        echo '<div class="error"><p>' . __( 'Nie ustawiono podstrony wyświetlającej relacje inwestorskie! <a href="admin.php?page=pyqplconfig-general-settings">(przejdź do ustawień wtyczki - relacje inwestorskie)</a>', PYQ_WP_PLUGIN_TEXTDOMAIN) . ' ('.PYQ_WP_PLUGIN_NAME.')</p></div>';
        return false;
    }
}
add_action( 'admin_notices', 'pyqpl_page_check' );

/**
 *  Template relacji inwestorskich 
 */
function pyqpl_RelacjeInwestorskieTemplate( $template ) {
    global $post;
    
    $pyqpl_selected_main_pageid = get_field('pyqpl_selected_page_main_template','option');

    /*     echo "<script>alert($pyqpl_selected_main_pageid->ID);</script>";
    echo "<script>alert($post->ID);</script>"; */

    if ( ((($post->ID)==($pyqpl_selected_main_pageid->ID)) && (locate_template( array( 'relacje-inwestorskie-main-template.php' ) ) !== $template )) || (is_singular('raporty_biezace_ebi') || is_singular('raporty_biezace_espi') || is_singular('walne_zgromadzenia')) ) {
        add_action( 'wp_enqueue_scripts', 'pyqplconfig_register_plugin_styles' );
        add_action( 'wp_enqueue_scripts', 'pyqplconfig_register_plugin_scripts' );
        return plugin_dir_path( __FILE__ ) . 'templates/relacje-inwestorskie-main-template.php';
    } 
    return $template;
}
add_filter( 'page_template', 'pyqpl_RelacjeInwestorskieTemplate' );
add_filter( 'template_include', 'pyqpl_RelacjeInwestorskieTemplate' );

/**
 *  Rejestracja CPT - Relacje inwestorskie 
 **/ 
function pyqpl_custom_post_type() {

    //Raporty bieżące - EBI
    register_post_type('raporty_biezace_ebi', array(
        'labels' => array(
            'name' => __('Raporty bieżące (EBI)'),
            'singular_name' => __('Raport bieżący (EBI)'),
            'add_new' => __( 'Dodaj nowy raport EBI' ),
            'add_new_item' => __( 'Dodaj nowy raport bieżący (EBI)' ),
            'all_items' => __( 'Wszystkie raporty bieżące (EBI)' ),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
        'show_in_menu' => false
    ));

    //Raporty bieżące - ESPI
    register_post_type('raporty_biezace_espi', array(
        'labels' => array(
            'name' => __('Raporty bieżące (ESPI)'),
            'singular_name' => __('Raport bieżący (ESPI)'),
            'add_new' => __( 'Dodaj nowy raport ESPI' ),
            'add_new_item' => __( 'Dodaj nowy raport bieżący (ESPI)' ),
            'all_items' => __( 'Wszystkie raporty bieżące (ESPI)' ),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
        'show_in_menu' => false
    ));

    //Walne zgromadzenia
    register_post_type('walne_zgromadzenia', array(
        'labels' => array(
            'name' => __('Walne zgromadzenia'),
            'singular_name' => __('Walne zgromadzenie'),
            'add_new' => __( 'Dodaj nowe walne zgromadzenie' ),
            'add_new_item' => __( 'Dodaj nowe walne zgromadzenie' ),
            'all_items' => __( 'Wszystkie walne zgromadzenia' ),
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor'),
        'show_in_menu' => false
    ));

}
add_action('init', 'pyqpl_custom_post_type');


function pyqpl_custom_post_template_include($template) {
    if ( is_singular('raporty_biezace_ebi') || is_singular('raporty_biezace_espi') || is_singular('walne_zgromadzenia') ) 
    {
        // Ścieżka do szablonu w twojej wtyczce
        $custom_template = plugin_dir_path(__FILE__) . 'templates/single-relacje-inwestorskie.php';
        
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
}
add_filter('template_include', 'pyqpl_custom_post_template_include');



/**
 *  Rejestracja menu w dashboard WP - Relacje Inwestorskie
 **/ 
if( function_exists('acf_add_options_page') ) {
    $main = acf_add_options_page(array(
        'page_title'    => 'Relacje inwestorskie Werth-Holz S.A.',
        'menu_title'    => '<small><b>Relacje inwestorskie</b></small>',
        'menu_slug'     => 'pyqpl-relacje-inwestorskie',
        'capability'    => 'edit_posts',
        'icon_url' => 'dashicons-money',
        'position' => '25',
        'redirect'      => true,
    ));

    $child_1 = acf_add_options_sub_page(array(
        'page_title'  => __('Ustawienia ogólne'),
        'menu_title'  => __('<small>Ustawienia wtyczki</small>'),
        'menu_slug'     => 'pyqpl-general-settings',
        'parent_slug' => 'pyqpl-relacje-inwestorskie',
        'position' => 999,
    ));

    $child_2 = acf_add_options_page(array(
        'page_title'  => __('Wyniki finansowe'),
        'menu_title'  => __('<small>Wyniki finansowe</small>'),
        'menu_slug'     => 'pyqpl-wyniki-finansowe',
        'parent'      => 'pyqpl-relacje-inwestorskie',
        'position' => 2,
    ));

    $child_3 = acf_add_options_page(array(
        'page_title'  => __('Dokumenty informacyjne'),
        'menu_title'  => __('<small>Dokumenty informacyjne</small>'),
        'menu_slug'     => 'pyqpl-dokumenty-informacyjne',
        'parent'      => 'pyqpl-relacje-inwestorskie',
        'position' => 3,
    ));

    $child_4 = acf_add_options_page(array(
        'page_title'  => __('Dokumenty korporacyjne'),
        'menu_title'  => __('<small>Dokumenty korporacyjne</small>'),
        'menu_slug'     => 'pyqpl-dokumenty-korporacyjne',
        'parent'      => 'pyqpl-relacje-inwestorskie',
        'position' => 4,
    ));

    $child_5 = acf_add_options_page(array(
        'page_title'  => __('Władze spółki'),
        'menu_title'  => __('<small>Władze spółki</small>'),
        'menu_slug'     => 'pyqpl-wladze-spolki',
        'parent'      => 'pyqpl-relacje-inwestorskie',
        'position' => 5,
    ));

    $child_5 = acf_add_options_page(array(
        'page_title'  => __('Akcjonariusze'),
        'menu_title'  => __('<small>Akcjonariusze</small>'),
        'menu_slug'     => 'pyqpl-akcjonariusze',
        'parent'      => 'pyqpl-relacje-inwestorskie',
        'position' => 7,
    ));

    function pyqpl_add_theme_menu_item() {

        add_submenu_page(
            "pyqpl-general-settings",
            "Raporty bieżące (EBI)",
            "<small>Raporty bieżące (EBI)</small>",
            "edit_posts",
            "edit.php?post_type=raporty_biezace_ebi",
            null,
            1
        );

        add_submenu_page(
            "pyqpl-general-settings",
            "Raporty bieżące (ESPI)",
            "<small>Raporty bieżące (ESPI)</small>",
            "edit_posts",
            "edit.php?post_type=raporty_biezace_espi",
            null,
            2
        );

        add_submenu_page(
            "pyqpl-general-settings",
            "Walne zgromadzenia",
            "<small>Walne zgromadzenia</small>",
            "edit_posts",
            "edit.php?post_type=walne_zgromadzenia",
            null,
            5
        );


    }
    add_action("admin_menu", "pyqpl_add_theme_menu_item");



}


/**
 *  Rejestracja ustawień ogólnych wtyczki - strona opcji ACF 
 **/ 
if( function_exists('acf_add_local_field_group') )
{
    //Ustawienia wtyczki
	acf_add_local_field_group(
        array(
            'key' => 'group_1',
            'title' => 'Ustawienia ogólne',
            'fields' => array (
                array (
                    'key' => 'field_1_1',
                    'label' => 'Podstrona relacji inwestorskich',
                    'instructions' => "Wybierz obiekt strony lub wpisu który ma wyświetlać relacje inwestorskie (zamienia zawartość strony na podsystem relacji inwestorskich)",
                    'required' => true,
                    'name' => 'pyqpl_selected_page_main_template',
                    'type' => 'post_object',
                ),
                array (
                    'key' => 'pyqpl_profil_stooq',
                    'label' => 'Profil WHH w Stooq.pl',
                    'required' => true,
                    'name' => 'pyqpl_profil_stooq',
                    'type' => 'text',
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'pyqpl-general-settings',
                    ),
                ),
            ),
        )
    );

    //Wyniki finansowe
    acf_add_local_field_group(
        array(
            'key' => 'WF_group_1',
            'title' => 'Wyniki finansowe',
            'fields' => array (
                array (
                    'key' => 'WF_repeater_1_1',
                    'label' => 'Okres',
                    'instructions' => '<small>Okres oznacza grupowanie raportów zgodnie z danym zakresem czasu, np. <b>"Rok 2022/2023 (12 miesięcy)"</b> lub <b>"Rok 2012/2013 (21 miesięcy)"</b>.</small>',
                    'name' => 'WF_wyniki_finansowe_okres',
                    'type' => 'repeater',
                    'layout' => 'block',
                    'button_label' => 'Dodaj okres',
                    'sub_fields' => array(
                        array(
                            'key' => 'WF_text1',
                            'label' => 'Nazwa okresu',
                            'name' => 'WF_nazwa_okresu',
                            'type' => 'text',
                            'required' => true,
                        ),
                        array(
                            'key' => 'WF_inner_section_repeater',
                            'label' => 'Kategoria raportów',
                            'instructions' => '<small>Kategoria raportów to np. <b>"Raport roczny jednostkowy"</b> lub <b>"Raport roczny skonsolidowany"</b> itp. - grupuje poszczególne raporty.</small>',
                            'name' => 'WF_kategoria_raportu_repeater',
                            'type' => 'repeater',
                            'layout' => 'block',
                            'button_label' => 'Dodaj kategorię raportu',
                            'sub_fields' => array(
                                array(
                                    'key' => 'WF_inner_section_text1',
                                    'label' => 'Nazwa kategorii raportu',
                                    'name' => 'WF_kategoria_raportu_text',
                                    'type' => 'text',
                                    'required' => true,
                                ),
                                array(
                                    'key' => 'WF_inner_section_repeater_pliki',
                                    'label' => 'Raporty dla kategorii',
                                    'name' => 'WF_raoporty_dla_kategorii_repeater',
                                    'type' => 'repeater',
                                    'button_label' => 'Dodaj raport',
                                    'sub_fields' => array(
                                        array(
                                            'key' => 'WF_inner_section_repeater_pliki_tytul_pliku',
                                            'label' => 'Nazwa pliku',
                                            'instructions' => '<small>Nazwa wyświetlana w liście plików dla danej kategorii.</small>',
                                            'name' => 'WF_raoporty_dla_kategorii_nazwapliku',
                                            'type' => 'text',
                                            'required' => true,
                                        ),
                                        array(
                                            'key' => 'WF_inner_section_repeater_pliki_plik',
                                            'label' => 'Załącznik',
                                            'instructions' => '<small>Wybierz plik</small>',
                                            'name' => 'WF_inner_section_repeater_pliki_plik',
                                            'type' => 'file',
                                            'required' => true,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        array(
                            'key' => 'WF_wysiwyg_dod_text',
                            'label' => 'Dodatkowa treść w sekcji',
                            'name' => 'WF_wysiwyg_dod_text',
                            'type' => 'wysiwyg',
                            'required' => false,
                        ),
                    ),
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'pyqpl-wyniki-finansowe',
                    ),
                ),
            ),
        )
    );

    //Dokumenty informacyjne
    acf_add_local_field_group(
        array(
            'key' => 'DI_group_1',
            'title' => 'Dokumenty informacyjne',
            'fields' => array (
                array (
                    'key' => 'DI_repeater_1_1',
                    'label' => 'Lista dokumentów',
                    'name' => 'DI_dokumenty_informacyjne_repeater',
                    'type' => 'repeater',
                    'button_label' => 'Dodaj dokument informacyjny',
                    'sub_fields' => array(
                        array(
                            'key' => 'DI_tytul',
                            'label' => 'Nazwa pliku',
                            'instructions' => '<small>Nazwa wyświetlana w liście plików.</small>',
                            'name' => 'DI_tytul',
                            'type' => 'text',
                            'required' => true,
                        ),
                        array(
                            'key' => 'DI_plik',
                            'label' => 'Załącznik',
                            'instructions' => '<small>Wybierz plik</small>',
                            'name' => 'DI_plik',
                            'type' => 'file',
                            'required' => false,
                        ),
                        array(
                            'key' => 'DI_link',
                            'label' => 'Link',
                            'instructions' => '<small>Jeśli nie załączymy pliku - kliknięcie prowadzi do przekierowania na link</small>',
                            'name' => 'DI_link',
                            'type' => 'url',
                            'required' => false,
                        ),
                    ),
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'pyqpl-dokumenty-informacyjne',
                    ),
                ),
            ),
        )
    );

    //Dokumenty korporacyjne
    acf_add_local_field_group(
        array(
            'key' => 'DK_group_1',
            'title' => 'Dokumenty korporacyjne',
            'fields' => array (
                array (
                    'key' => 'DK_repeater_1_1',
                    'label' => 'Lista dokumentów',
                    'name' => 'DK_dokumenty_korporacyjne_repeater',
                    'type' => 'repeater',
                    'button_label' => 'Dodaj dokument korporacyjny',
                    'sub_fields' => array(
                        array(
                            'key' => 'DK_tytul',
                            'label' => 'Nazwa pliku',
                            'instructions' => '<small>Nazwa wyświetlana w liście plików.</small>',
                            'name' => 'DK_tytul',
                            'type' => 'text',
                            'required' => true,
                        ),
                        array(
                            'key' => 'DK_plik',
                            'label' => 'Załącznik',
                            'instructions' => '<small>Wybierz plik</small>',
                            'name' => 'DK_plik',
                            'type' => 'file',
                            'required' => true,
                        ),
                    ),
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'pyqpl-dokumenty-korporacyjne',
                    ),
                ),
            ),
        )
    );

    //Władze spółki
    acf_add_local_field_group(
        array(
            'key' => 'WS_group_1',
            'title' => 'Władze spółki',
            'fields' => array (
                array (
                    'key' => 'WS_repeater_1_1',
                    'label' => 'Grupa',
                    'name' => 'WS_grupa_wladze_spolki_repeater',
                    'type' => 'repeater',
                    'button_label' => 'Dodaj nową grupę pracowników',
                    'layout' => 'block',
                    'sub_fields' => array(
                        array(
                            'key' => 'WS_nazwa_grupy',
                            'label' => 'Nazwa grupy',
                            'instructions' => '<small>Np. "Zarząd" lub "Rada Nadzorcza" itp.</small>',
                            'name' => 'WS_nazwa_grupy',
                            'type' => 'text',
                            'required' => true,
                        ),
                        array(
                            'key' => 'WS_czlonkowie_grupy',
                            'label' => 'Członkowie grupy',
                            'instructions' => '<small>Imię i nazwisko członka grupy</small>',
                            'name' => 'WS_czlonkowie_grupy_repeater',
                            'type' => 'repeater',
                            'button_label' => 'Dodaj pracownika do grupy',
                            'sub_fields' => array(
                                array(
                                    'key' => 'WS_czlonek_grupy_imie_nazwisko',
                                    'label' => 'Imię i nazwisko',
                                    'name' => 'WS_czlonek_grupy_imie_nazwisko',
                                    'type' => 'text',
                                    'required' => true,
                                ),
                                array(
                                    'key' => 'WS_czlonek_grupy_stanowisko',
                                    'label' => 'Stanowisko',
                                    'name' => 'WS_czlonek_grupy_stanowisko',
                                    'type' => 'text',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'pyqpl-wladze-spolki',
                    ),
                ),
            ),
        )
    );

    //Akcjonariusze
    acf_add_local_field_group(
        array(
            'key' => 'AK_group_1',
            'title' => 'Akcjonariusze',
            'fields' => array (
                array (
                    'key' => 'AK_field_wysiwyg',
                    'label' => 'Tabela akcjonariuszy',
                    'name' => 'AK_field_wysiwyg',
                    'type' => 'wysiwyg',
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'pyqpl-akcjonariusze',
                    ),
                ),
            ),
        )
    );

    //Kontakt
    acf_add_local_field_group(
        array(
            'key' => 'KO_group_1',
            'title' => 'Kontakt',
            'fields' => array (
                array (
                    'key' => 'KO_wysiwyg',
                    'label' => 'Dane kontaktowe',
                    'name' => 'KO_wysiwyg',
                    'type' => 'wysiwyg',
                ),
            ),
            'location' => array (
                array (
                    array (
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'pyqpl-general-settings',
                    ),
                ),
            ),
        )
    );
}