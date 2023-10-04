console.log("pyq.pl - Relacje inwestorskie - script.js loaded");

jQuery(document).ready(function(){

    var selectedSectionHiddenInputValue = jQuery("#pyqpl-selected-section").val();

    //Submit form przy zmianie sekcji
    jQuery("#form-menu-relacje-inwestorskie button").click(function(){
        jQuery("#pyqpl-selected-section").val(jQuery(this).val());
        if(!jQuery(this).hasClass("disabled"))
        {
            jQuery("#form-menu-relacje-inwestorskie").submit();
        }
    });

    //Podswietlenie aktywnego przycisku po kliknięciu
    var form_buttons = jQuery("#form-menu-relacje-inwestorskie").find("button");
    jQuery(form_buttons).each(function(index){
        if(jQuery(this).val() == jQuery("#pyqpl-selected-section").val())
        {
            jQuery(this).addClass("active");
        }else{
            jQuery(this).removeClass("active");
        }
    });
    jQuery(".pyqpl-relacje-inwestorskie, .pyqpl-container-relacje-inwestorskie").fadeTo(10, 1);

    //Zmiana tytułu kontenera wyświetlającego content - h3
    jQuery("#pyqpl-container-relacje-title").text(jQuery("#form-menu-relacje-inwestorskie button[value='"+selectedSectionHiddenInputValue+"']").text());

    jQuery("#pyqpl-form-select-year input").click(function(){
        jQuery("#pyqpl-selected-year").val(jQuery(this).val());
        jQuery("#pyqpl-form-select-year").submit();
    });

    jQuery(".pyqpl-accordion-title").click(function(){
        jQuery(this).closest(".pyqpl-accordion-item").find(".pyqpl-accordion-title").toggleClass("active");
        jQuery(this).closest(".pyqpl-accordion-item").find(".pyqpl-accordion-content").slideToggle("fast");
    })

});