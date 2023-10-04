<?php get_header(); 

global $apress_data;
$page_single_post_layout = get_post_meta( $post->ID , 'zt_single_post_layout_style', true );
$admin_single_post_layout_style = isset($apress_data['single_post_layout_style']) ? $apress_data['single_post_layout_style'] : 'layout_style1';

$pyq_cpt_post = get_queried_object();
$pyq_cpt_postType = get_post_type_object(get_post_type($pyq_cpt_post));
//var_dump($pyq_cpt_postType);
if ($pyq_cpt_postType) {
    $pyq_cpt_postType_name =  $pyq_cpt_postType->name;
}else {
    $pyq_cpt_postType_name = "artykul";
}

$pyqpl_selected_section_val = $pyq_cpt_postType_name;
$pyqpl_selected_section_text = $_GET['sekcja'] ? "Raporty bieżące ESPI" : null;
$pyqpl_selected_year = $_GET['rok'] ? $_GET['rok'] : "all";
$current_page = max(1, get_query_var('paged'));

$pyqpl_profil_stooqpl_url = get_field('pyqpl_profil_stooq','option');
$pyqpl_selected_main_pageid = get_field('pyqpl_selected_page_main_template','option');

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
            <a class="pyqpl-back-to-the-list-btn" href="<?php echo get_permalink( $pyqpl_selected_main_pageid -> ID ).'?sekcja='.$pyq_cpt_postType_name;?>"><small><b>Wróć do listy</b></small></a>
            <?php the_content(); ?>
        </div>

    </section>
</div>

<?php // Previous/next post navigation Start 

echo '<div class="zolo-container">';
apress_theme_single_page_nav();
echo '</div>';

// Previous/next post navigation End ?>	

<?php

/* var_dump($admin_single_post_layout_style);
$admin_single_post_layout_style = "layout_style4";

if($page_single_post_layout == 'default' || $page_single_post_layout == ''){
	$single_single_post_layout_value = $admin_single_post_layout_style;
}else{
	$single_single_post_layout_value = $page_single_post_layout;
}
 
if($single_single_post_layout_value == 'layout_style1'){
	
	get_template_part( 'post_layout/'.$single_single_post_layout_value);
	
}else if($single_single_post_layout_value == 'layout_style2'){

	get_template_part( 'post_layout/'.$single_single_post_layout_value);

}else if($single_single_post_layout_value == 'layout_style3'){
	
	get_template_part( 'post_layout/'.$single_single_post_layout_value);
	
}else if($single_single_post_layout_value == 'layout_style4'){

	get_template_part( 'post_layout/'.$single_single_post_layout_value);
	
}else if($single_single_post_layout_value == 'layout_style5'){

	get_template_part( 'post_layout/'.$single_single_post_layout_value);

}else if($single_single_post_layout_value == 'layout_style6'){

	get_template_part( 'post_layout/'.$single_single_post_layout_value);

} */

get_footer(); ?>
