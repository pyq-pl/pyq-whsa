<?php 
    /* ACF Fields */
    //$pyqconfig_company_logo_field = get_field('pyqplconfig_company_logo','option');
    global $wpdb;

    $pyqpl_selected_section_val = $_GET['sekcja'] ? $_GET['sekcja'] : "raporty_biezace_ebi";
    $pyqpl_selected_section_text = $_GET['sekcja'] ? "Raporty bieżące ESPI" : null;
    $pyqpl_selected_year = $_GET['rok'] ? $_GET['rok'] : "all";
    $current_page = max(1, get_query_var('paged'));

    $pyqpl_profil_stooqpl_url = get_field('pyqpl_profil_stooq','option');
    
    $pyqpl_selected_main_pageid = get_field('pyqpl_selected_page_main_template','option');
    get_header(); 
?>

<div class="zolo-container zolo_vc_container">
    <section class="pyqpl-section-nav-relacje-inwestorskie"> 
        <nav class="pyqpl-relacje-inwestorskie">
            <form action="<?php echo(get_permalink( $pyqpl_selected_main_pageid -> ID )); ?>" method="GET" id="form-menu-relacje-inwestorskie">
                <input id="pyqpl-selected-section" type="hidden" name="sekcja" value="<?php echo $pyqpl_selected_section_val; ?>" readonly>
                <input type="hidden" name="paged" value="1" readonly>
                <button type="button" value="raporty_biezace_ebi" id="pyqpl-raporty_biezace_ebi">Raporty bieżące (EBI)</button>
                <button type="button" value="raporty_biezace_espi" id="pyqpl-raporty_biezace_espi">Raporty bieżące (ESPI)</button>
                <button type="button" value="wyniki_finansowe" id="pyqpl-wyniki_finansowe">Wyniki finansowe</button>
                <button type="button" value="dokument_informacyjny" id="pyqpl-dokument_informacyjny">Dokumenty informacyjne</button>
                <button type="button" value="dokumenty_korporacyjne" id="pyqpl-dokumenty_korporacyjne">Dokumenty korporacyjne</button>
                <button type="button" value="wladze_spolki" id="pyqpl-wladze_spolki">Władze spółki</button>
                <button type="button" value="walne_zgromadzenia" id="pyqpl-walne-zgromadzenia">Walne zgromadzenia</button>
                <?php if($pyqpl_profil_stooqpl_url) { ?> <a style="width: 100%;" href="<?php echo $pyqpl_profil_stooqpl_url; ?>" target="blank"><button type="button" class="disabled">Profil WWH w Stooq.pl</button></a> <?php } ?>
                <button type="button" value="akcjonariusze" id="pyqpl-akcjonariusze">Akcjonariusze</button>
                <button type="button" value="kontakt" id="pyqpl-ri-kontakt">Kontakt</button>
            </form>        
        </nav>
        
        <div class="pyqpl-container-relacje-inwestorskie">
            <h2 id="pyqpl-container-relacje-title"><?php echo $pyqpl_selected_section_text; ?></h3>
            <?php

                $args = array(
                    'post_type' => $pyqpl_selected_section_val, // Nazwa Twojego niestandardowego typu postów
                    'posts_per_page' => 25, // Pobierz wszystkie wpisy tego typu
                    'orderby' => 'date', // Sortuj według daty
                    'order' => 'DESC', // Sortuj malejąco (od najnowszego do najstarszego)
                    'paged' => $current_page,
                    'date_query' => array(
                        array(
                            'year'  => $pyqpl_selected_year, // Wybierz wpisy z określonego roku
                        ),
                    ),
                );
                $selected_query = new WP_Query($args);

                
                $pagination_args = array(
                    'total' => $selected_query->max_num_pages, // Ilość wszystkich stron
                    'current' => $current_page, // Aktualna strona
                    'type' => 'array', // Typ wyjścia ('array', 'plain', 'list' itp.)
                    'prev_text' => '&laquo; Poprzednia', // Tekst dla poprzedniej strony
                    'next_text' => 'Następna &raquo;', // Tekst dla następnej strony
                );
                $pagination_links = paginate_links($pagination_args);
            ?>
            <?php switch($pyqpl_selected_section_val) {
                case "raporty_biezace_ebi": 
                {
                    if ($selected_query->have_posts()) {

                        // Pobierz unikalne lata z tabeli wpisów
                        $years_query = $wpdb->get_results(
                            "SELECT DISTINCT YEAR(wp_posts.post_date) as post_year
                            FROM $wpdb->posts
                            WHERE wp_posts.post_type = 'raporty_biezace_ebi' 
                            AND wp_posts.post_status = 'publish'
                            ORDER BY wp_posts.post_date DESC"
                        );

                        ?>
                        <form action="<?php echo(get_permalink( $pyqpl_selected_main_pageid -> ID )); ?>" method="GET" id="pyqpl-form-select-year">
                            <input type="hidden" name="sekcja" value="<?php echo $pyqpl_selected_section_val; ?>" readonly> 
                            <input type="hidden" name="paged" value="1" readonly="">                          
                            <?php
                                
                                echo "<input ".(($pyqpl_selected_year == "all") ? "checked" : "")." type='radio' id='rok_zero' name='rok' value='all'><label class='pyqpl-box-radio' for='rok_zero'>".Wszystkie."</label>";

                                foreach($years_query as $year)
                                {
                                    $is_selected = (strval($year->post_year) == strval($pyqpl_selected_year)) ? "checked" : null;
                                    echo "<input ".$is_selected." type='radio' id='rok_".$year->post_year."' name='rok' value='".$year->post_year."'><label class='pyqpl-box-radio' for='rok_".$year->post_year."'>".$year->post_year."</label>";
                                }
                            ?>
                        </form>

                        <?php

                        while ($selected_query->have_posts()) {
                            $selected_query->the_post();
                            ?>
                            <div class="pyqpl-row-report-single">
                                <a class="pyqpl-row-report-single-title" href="<?php echo the_permalink();?>"><?php echo the_title();?></a>
                                <small class="pyqpl-row-report-single-date"><?php echo get_the_date(); ?></small>
                                <div class="pyqpl-row-report-single-content">
                                    <small>
                                        <?php echo get_the_excerpt(); ?>
                                        <a href="<?php echo the_permalink();?>">(więcej)</a>
                                    </small>
                                </div>
                            </div>
                            <?php                            
                        }
                        wp_reset_postdata(); // Zresetuj zapytanie do oryginalnych danych
                        echo '<div class="pagination pagination-pyqpl">' . implode(' ', $pagination_links) . '</div>';
                    } else {
                        // Komunikat, jeśli nie ma żadnych wpisów typu niestandardowego
                        echo 'Brak wpisów.';
                    }
                    break;
                }
                case "raporty_biezace_espi":
                {
                    if ($selected_query->have_posts()) {

                        // Pobierz unikalne lata z tabeli wpisów
                        $years_query = $wpdb->get_results(
                            "SELECT DISTINCT YEAR(wp_posts.post_date) as post_year
                            FROM $wpdb->posts
                            WHERE wp_posts.post_type = 'raporty_biezace_espi' 
                            AND wp_posts.post_status = 'publish'
                            ORDER BY wp_posts.post_date DESC"
                        );

                        ?>
                        <form action="<?php echo(get_permalink( $pyqpl_selected_main_pageid -> ID )); ?>" method="GET" id="pyqpl-form-select-year">
                            <input type="hidden" name="sekcja" value="<?php echo $pyqpl_selected_section_val; ?>" readonly>
                            <input type="hidden" name="paged" value="1" readonly="">
                            <?php
                                
                                echo "<input ".(($pyqpl_selected_year == "all") ? "checked" : "")." type='radio' id='rok_zero' name='rok' value='all'><label class='pyqpl-box-radio' for='rok_zero'>".Wszystkie."</label>";

                                foreach($years_query as $year)
                                {
                                    $is_selected = (strval($year->post_year) == strval($pyqpl_selected_year)) ? "checked" : null;
                                    echo "<input ".$is_selected." type='radio' id='rok_".$year->post_year."' name='rok' value='".$year->post_year."'><label class='pyqpl-box-radio' for='rok_".$year->post_year."'>".$year->post_year."</label>";
                                }
                            ?>
                        </form>

                        <?php

                        while ($selected_query->have_posts()) {
                            $selected_query->the_post();
                            ?>
                            <div class="pyqpl-row-report-single">
                                <a class="pyqpl-row-report-single-title" href="<?php echo the_permalink();?>"><?php echo the_title();?></a>
                                <small class="pyqpl-row-report-single-date"><?php echo get_the_date(); ?></small>
                                <div class="pyqpl-row-report-single-content">
                                    <small>
                                        <?php echo get_the_excerpt(); ?>
                                        <a href="<?php echo the_permalink();?>">(więcej)</a>
                                    </small>
                                </div>
                            </div>
                            <?php                            
                        }
                        wp_reset_postdata(); // Zresetuj zapytanie do oryginalnych danych
                        echo '<div class="pagination pagination-pyqpl">' . implode(' ', $pagination_links) . '</div>';
                    } else {
                        // Komunikat, jeśli nie ma żadnych wpisów typu niestandardowego
                        echo 'Brak wpisów.';
                    }
                    break;
                }
                case "wyniki_finansowe":
                {
                    $wyniki_finansowe_data = get_field("WF_wyniki_finansowe_okres", "option");
                    ?>
                    <section class="pyqpl-collapsible-rows">
                        <?php foreach($wyniki_finansowe_data as $index_i => $okres) { ?>
                            <div class="pyqpl-accordion-item">
                                <span class="pyqpl-accordion-title">
                                    <?php echo $okres['WF_nazwa_okresu']; ?>
                                </span>
                                <div class="pyqpl-accordion-content">
                                    <?php foreach($okres['WF_kategoria_raportu_repeater'] as $index_j => $kategoria_raportu) { ?>

                                        <span class="pyqpl-accordion-section-title"><?php echo $kategoria_raportu['WF_kategoria_raportu_text']; ?></span>
                                        <div class="pyqpl-accordion-section-inner-content">
                                            <ul>
                                            <?php foreach($kategoria_raportu['WF_raoporty_dla_kategorii_repeater'] as $index_k => $file) { 
                                                $file_temp = $file['WF_inner_section_repeater_pliki_plik']; ?>
                                                    <li>
                                                        <a target="blank" href="<?php echo $file_temp['url'];?>">
                                                        <?php echo $file['WF_raoporty_dla_kategorii_nazwapliku'];?>
                                                        </a>
                                                    </li>    
                                                <?php } ?>
                                            </ul>
                                        </div>

                                    <?php } ?>
                                    <?php echo($okres["WF_wysiwyg_dod_text"]); ?> 
                                </div>
                            </div>
                        <?php } ?>
                    </section>  
                    <?
                    break;
                }
                case "dokument_informacyjny":
                {
                    $dokumenty_informacyjne = get_field("DI_dokumenty_informacyjne_repeater", "option");
                    ?>
                    <section class="pyqpl-dokumenty-informacyjne-inner-content">
                        <ul>
                            <?php foreach($dokumenty_informacyjne as $index_i => $dokument) { 
                                $file_temp = $dokument["DI_plik"];
                                $file_url = $file_temp['url'] ? $file_temp['url'] : $dokument["DI_link"];

                                ?>
                                <li>
                                    <a target="blank" href="<?php echo $file_url;?>">
                                        <?php echo $dokument['DI_tytul'];?>
                                    </a>
                                </li>    
                            <?php } ?>
                        </ul>
                    </section>  
                    <?
                    break;
                }
                case "dokumenty_korporacyjne":
                {
                    $dokumenty_korporacyjne = get_field("DK_dokumenty_korporacyjne_repeater", "option");
                    ?>
                    <section class="pyqpl-dokumenty-korporacyjne-inner-content">
                        <ul>
                            <?php foreach($dokumenty_korporacyjne as $index_i => $dokument) { 
                                $file_temp = $dokument["DK_plik"];?>
                                <li>
                                    <a target="blank" href="<?php echo $file_temp['url'];?>">
                                        <?php echo $dokument['DK_tytul'];?>
                                    </a>
                                </li>    
                            <?php } ?>
                        </ul>
                    </section>  
                    <?
                    break;
                }
                case "wladze_spolki":
                {
                    $wladze_spolki_data = get_field("WS_grupa_wladze_spolki_repeater", "option");
                    ?>
                    <section class="pyqpl-collapsible-rows">
                        <?php foreach($wladze_spolki_data as $index_i => $grupa_pracownicza) { ?>
                            <div class="pyqpl-accordion-item">
                                <span class="pyqpl-accordion-title">
                                    <?php echo $grupa_pracownicza['WS_nazwa_grupy']; ?>
                                </span>
                                <div class="pyqpl-accordion-content">
                                    <ul>
                                        <?php foreach($grupa_pracownicza['WS_czlonkowie_grupy_repeater'] as $index_j => $pracownik) { ?>
                                            <li><b><?php echo $pracownik['WS_czlonek_grupy_imie_nazwisko']; ?></b> - <?php echo $pracownik['WS_czlonek_grupy_stanowisko']; ?></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                    </section>  
                    <?
                    break;
                }
                case "walne_zgromadzenia":
                {
                    if ($selected_query->have_posts()) {

                        // Pobierz unikalne lata z tabeli wpisów
                        $years_query = $wpdb->get_results(
                            "SELECT DISTINCT YEAR(wp_posts.post_date) as post_year
                            FROM $wpdb->posts
                            WHERE wp_posts.post_type = 'walne_zgromadzenia' 
                            AND wp_posts.post_status = 'publish'
                            ORDER BY wp_posts.post_date DESC"
                        );

                        ?>
                        <form action="<?php echo(get_permalink( $pyqpl_selected_main_pageid -> ID )); ?>" method="GET" id="pyqpl-form-select-year">
                            <input type="hidden" name="sekcja" value="<?php echo $pyqpl_selected_section_val; ?>" readonly>
                            <input type="hidden" name="paged" value="1" readonly="">
                            <?php
                                
                                echo "<input ".(($pyqpl_selected_year == "all") ? "checked" : "")." type='radio' id='rok_zero' name='rok' value='all'><label class='pyqpl-box-radio' for='rok_zero'>".Wszystkie."</label>";
                                foreach($years_query as $year)
                                {
                                    $is_selected = (strval($year->post_year) == strval($pyqpl_selected_year)) ? "checked" : null;
                                    echo "<input ".$is_selected." type='radio' id='rok_".$year->post_year."' name='rok' value='".$year->post_year."'><label class='pyqpl-box-radio' for='rok_".$year->post_year."'>".$year->post_year."</label>";
                                }
                            ?>
                        </form>

                        <?php

                        while ($selected_query->have_posts()) {
                            $selected_query->the_post();
                            ?>
                            <div class="pyqpl-row-report-single">
                                <a class="pyqpl-row-report-single-title" href="<?php echo the_permalink();?>"><?php echo the_title();?></a>
                                <small class="pyqpl-row-report-single-date"><?php echo get_the_date(); ?></small>
                                <div class="pyqpl-row-report-single-content">
                                    <small>
                                        <?php echo get_the_excerpt(); ?>
                                        <a href="<?php echo the_permalink();?>">(więcej)</a>
                                    </small>
                                </div>
                            </div>
                            <?php                            
                        }
                        wp_reset_postdata(); // Zresetuj zapytanie do oryginalnych danych
                        echo '<div class="pagination pagination-pyqpl">' . implode(' ', $pagination_links) . '</div>';
                    } else {
                        // Komunikat, jeśli nie ma żadnych wpisów typu niestandardowego
                        echo 'Brak wpisów.';
                    }
                    break;
                }
                case "akcjonariusze":
                {
                    $akcjonariusze_data = get_field("AK_field_wysiwyg", "option");
                    echo "<br>";
                    echo($akcjonariusze_data);
                    break;
                }
                case "kontakt":
                    {
                        $kontakt_data = get_field("KO_wysiwyg", "option");
                        echo($kontakt_data);
                        break;
                    }
            } ?>
        </div>

    </section>
</div>

<?php
    get_footer(); 
?>
    

