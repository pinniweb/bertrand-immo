<?php

// done categories
// done chambres
// done salles de bain
// done prix
// done submit

$args                       =   wpestate_get_select_arguments();
$action_select_list         =   wpestate_get_action_select_list($args);

if(isset($_GET['property_action_category']) && $_GET['property_action_category']!='' && $_GET['property_action_category']!='all'){
    $full_name = get_term_by('slug', esc_html( wp_kses( $_GET['property_action_category'],[]) ),'property_action_category');
    $adv_actions_value=$adv_actions_value1= $full_name->name;
    $adv_actions_value1 = mb_strtolower ( str_replace(' ', '-', $adv_actions_value1) );
}else{
    $adv_actions_value= wpestate_category_labels_dropdowns('second');
    $adv_actions_value1='all';
}


$args                     =   wpestate_get_select_arguments();
$city_select_list         =   wpestate_get_city_select_list($args);

if(isset($_GET['property_city']) && $_GET['property_city']!='' && $_GET['property_city']!='all'){
    $full_name = get_term_by('slug', esc_html( wp_kses( $_GET['property_city'],[]) ),'property_city');
    $adv_cities_value=$adv_cities_value1= $full_name->name;
    $adv_cities_value1 = mb_strtolower ( str_replace(' ', '-', $adv_cities_value1) );
}else{
    $adv_cities_value= 'Ville';
    $adv_cities_value1='all';
}

wp_enqueue_script('kbi_search_form_js',trailingslashit( get_stylesheet_directory_uri() ).'js/kbi_search_form.js',array('jquery'), '1.0', true);

?>

<?php if(empty($kbi_tabs_search)): ?>
    <link rel='stylesheet' id='google-fonts-1-css_lobster'  href='https://fonts.googleapis.com/css?family=Lobster%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CRoboto%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&#038;ver=5.6.2' type='text/css' media='all' />
    <h1 style="margin-left: 20px;margin-bottom: 0;font-family: 'Roboto', Sans-serif;font-size: 4rem;font-weight: 200;text-transform: uppercase;letter-spacing: 1px;">Location saisonnière de logements</h1>
<?php endif; ?>

<form action="/location-saisonniere/" id="kbi_loc_saison_search_form">
    <input type="hidden" id="property_category_loc_saison" name="property_category" value="location-saisonniere">
    <div class="row">
        <div class="col-md-4">
            <i class="custom_icon_class_icon fas fa-home"></i>
            <div class="dropdown form-control dropdown custom_icon_class icon_actionslist form-control">
                <div data-toggle="dropdown" id="adv_actions_loc_saison" class="filter_menu_trigger " data-value="<?php echo esc_attr(strtolower ( rawurlencode ( $adv_actions_value1) )) ?>">
                    <?php echo $adv_actions_value ; ?>
                    <span class="caret caret_filter"></span>
                </div>
                <input type="hidden" id="property_action_category_loc_saison" name="property_action_category" value="<?php echo !empty($_GET['property_action_category'])?strtolower( esc_attr($_GET['property_action_category']) ):'' ?>">
                <ul  class="dropdown-menu filter_menu" role="menu" aria-labelledby="adv_actions_loc_saison">
                    <?php echo $action_select_list; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <i class="custom_icon_class_icon fas fa-bed"></i>
            <div class="dropdown form-control dropdown custom_icon_class icon_actionslist form-control">
                <div data-toggle="dropdown" id="property_bedrooms_toogle_loc_saison" class="filter_menu_trigger " data-value="chambres">
                    <?php echo !empty($_GET['property_bedrooms'])?strtolower( esc_attr($_GET['property_bedrooms']) ):'Chambres' ?>
                    <span class="caret caret_filter"></span>
                </div>
                <input type="hidden" id="property_bedrooms_loc_saison" name="property_bedrooms" value="<?php echo !empty($_GET['property_bedrooms'])?strtolower( esc_attr($_GET['property_bedrooms']) ):'' ?>">
                <ul class="dropdown-menu filter_menu" role="menu" aria-labelledby="property_bedrooms_toogle_loc_saison">
                    <li role="presentation" data-value="all">Chambres</li>
                    <li data-value="1" value="1">Au moins 1</li>
                    <li data-value="2" value="2">Au moins 2</li>
                    <li data-value="3" value="3">Au moins 3</li>
                    <li data-value="4" value="4">Au moins 4</li>
                    <li data-value="5" value="5">Au moins 5</li>
                    <li data-value="6" value="6">Au moins 6</li>
                    <li data-value="7" value="7">Au moins 7</li>
                    <li data-value="8" value="8">Au moins 8</li>
                    <li data-value="9" value="9">Au moins 9</li>
                    <li data-value="10" value="10">Au moins 10</li>
                </ul>
            </div>
        </div>
        <div class="col-md-4">
            <input type="text" id="property_ref_loc_saison" name="property_ref" placeholder="Référence" class="form-control" value="<?php echo !empty($_GET['property_ref'])?strtolower( esc_attr($_GET['property_ref']) ):'' ?>">
        </div> 
        
        
    </div>
    <div class="row">
        <div class="col-md-4">
            <i class="custom_icon_class_icon fas fa-map-marker"></i>
            <div class="dropdown form-control dropdown custom_icon_class icon_actionslist form-control">
                <div data-toggle="dropdown" id="adv_city_loc_saison" class="filter_menu_trigger " data-value="<?php echo esc_attr(strtolower ( rawurlencode ( $adv_cities_value1) )) ?>">
                    <?php echo $adv_cities_value ; ?>
                    <span class="caret caret_filter"></span>
                </div>
                <input type="hidden" id="property_city_loc_saison" name="property_city" value="<?php echo !empty($_GET['property_city'])?strtolower( esc_attr($_GET['property_city']) ):'' ?>">
                <ul  class="dropdown-menu filter_menu" role="menu" aria-labelledby="adv_city_loc_saison">
                    <?php echo $city_select_list; ?>
                </ul>
            </div>
        </div>
        
        <div class="col-md-4 Check-In">
            <i class="custom_icon_class_icon fas fa-calendar-alt"></i>
            <input type="text" id="check_in" name="check_in" placeholder="Date d'arrivée" class="advanced_select form-control custom_icon_class_input" value="" readonly="readonly">
        </div>
        <div class="col-md-4 Check-Out">
            <i class="custom_icon_class_icon fas fa-calendar-alt"></i>
            <input type="text" id="check_out" name="check_out" placeholder="Date de départ" class="advanced_select form-control custom_icon_class_input" value="">
        </div>
    </div>
    <div class="row">
        <?php if(!empty($kbi_tabs_search)): ?>
            <div class="col-md-8">
                <?php echo wpestate_price_form_adv_search('kbi_loc_saison', '', ''); ?>
            </div>
            <div class="col-md-4">
                <input type="submit" class="advanced_search_submit_button " value="Recherche">
            </div>
        <?php else: ?>
            <div class="col-md-12">
                <?php echo wpestate_price_form_adv_search('kbi_loc_saison', '', ''); ?>
            </div>
        <?php endif; ?>
    </div>
</form>

<script>
  jQuery(document).ready(function($) {
    $('#kbi_loc_saison_search_form .dropdown-menu li').on('click',function () {
      var pick, value, parent;
      pick = $(this).text();
      value = $(this).attr('data-value');
      parent = $(this).parent().parent();
      parent.find('.filter_menu_trigger').text(pick).append('<span class="caret caret_filter"></span>').attr('data-value', value);
      parent.find('input').val(value);
    });

      <?php if(empty($kbi_tabs_search)): ?>
        wpestate_start_filtering_ajax_map(1);
      <?php endif; ?>
  });
</script>
<style>
    #kbi_loc_saison_search_form .custom_icon_class_icon {margin-left: 15px;}
    #kbi_slider_loc_saison{
        height: 6px;
        margin: 10px 10px 6px 10px;
        background-color: #e9edf3!important;
    }
    #kbi_slider_loc_saison .ui-slider-handle{
        border-radius: 1px;
        background-image: none;
        width: 18px;
        cursor: e-resize;
        height: 18px;
        top:-7px;
        border: 1px solid #e7e9ef;
    }
    #kbi_slider_loc_saison .ui-slider-range{
        background-color: #eb5c5f;
    }
    #google_map_prop_list_sidebar > form{
        padding: 20px;
    }
</style>