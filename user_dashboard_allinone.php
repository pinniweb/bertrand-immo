<?php
// Template Name: All in one calendar
// Wp Estate Pack
if ( !is_user_logged_in() ) {
    wp_redirect(  esc_url( home_url('/') ) );exit();
}
if ( !wpestate_check_user_level()){
   wp_redirect(  esc_url( home_url('/') ) );exit();
}



global $user_login;
$current_user = wp_get_current_user();
$userID                         =   $current_user->ID;
$user_login                     =   $current_user->user_login;
$user_pack                      =   get_the_author_meta( 'package_id' , $userID );
$user_registered                =   get_the_author_meta( 'user_registered' , $userID );
$user_package_activation        =   get_the_author_meta( 'package_activation' , $userID );
$paid_submission_status         =   esc_html ( wprentals_get_option('wp_estate_paid_submission','') );
$price_submission               =   floatval( wprentals_get_option('wp_estate_price_submission','') );
$submission_curency_status      =   wpestate_curency_submission_pick();
$edit_link                      =   wpestate_get_template_link('user_dashboard_edit_listing.php');
$processor_link                 =   wpestate_get_template_link('processor.php');
$week_days=array(
    '0'=>esc_html__('All','wprentals'),
    '1'=>esc_html__('Monday','wprentals'),
    '2'=>esc_html__('Tuesday','wprentals'),
    '3'=>esc_html__('Wednesday','wprentals'),
    '4'=>esc_html__('Thursday','wprentals'),
    '5'=>esc_html__('Friday','wprentals'),
    '6'=>esc_html__('Saturday','wprentals'),
    '7'=>esc_html__('Sunday','wprentals')

    );
$wp_estate_currency_symbol = esc_html( wprentals_get_option('wp_estate_currency_label_main', '') );
get_header();
$wpestate_options                =   wpestate_page_details($post->ID);
$submission_page_fields =  wprentals_get_option('wp_estate_submission_page_fields','');
?>

<div class="row is_dashboard">
    <?php
    if( wpestate_check_if_admin_page($post->ID) ){
        if ( is_user_logged_in() ) {
            include(locate_template('templates/user_menu.php') );
        }
    }
    ?>
    <div class=" dashboard-margin">

        <?php       wprentals_dashboard_header_display(); ?>

        <div class=" user_dashboard_panel wprentals_allinone_wrapper">    
            <div class="arrow-wrapper-allinone">
                <div id="calendar-prev-internal-allinone" class=""><i class="fas fa-chevron-left"></i></div>
                <div id="calendar-next-internal-allinone" class=""><i class="fas fa-chevron-right"></i></div>
            </div>


            <?php wpestate_get_calendar_allinone(); ?>



            <div class="arrow-wrapper-allinone_legend">

                <div class="calendar-reserved calendar_pad has_future allinone_external_booking"></div>
                <div class="allinone_legend"><?php _e('External Booking','wprentals') ?></div>

                <div class="calendar-reserved calendar_pad has_future allinone_internal_booking"></div>
                <div class="allinone_legend"><?php _e('Internal Booking','wprentals') ?></div>

                <div class="calendar-free calendar_pad has_future"></div>
                  <div class="allinone_legend"><?php _e('Free','wprentals') ?></div>
            </div>
            <div class="arrow-wrapper-allinone_legend">
                <?php _e('The calendar will not be displayed correctly on resolution lower than 1200px (because of lack of space). Please do not use this feature on mobile devices.','wprentals');?>
            </div>


        </div>
    </div>
</div>



 <!-- Modal -->
<div class="modal fade" id="allinone_reservation_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
       <div class="modal-content allinone_modal">

            <div class="modal-header">
              <button type="button" id="close_custom_price_internal" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h2 class="modal-title_big"><?php esc_html_e('Custom Price & Period reservation','wprentals');?></h2>
              <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Set custom price or mark dates as booked for selected period','wprentals');?></h4>
            </div>

            <div class="modal-body">

                <div id="booking_form_request_mess_modal"></div>
                <input type="hidden" id="property_id" name="property_id" value="" />
                <input type="hidden" id="listing_edit" name="listing_edit" value="" />
                <input name="prop_id" type="hidden"  id="agent_property_id" value="">
                <input name="period_min_days_booking" type="hidden"  id="period_min_days_booking" value="7">
                <input name="period_checkin_checkout_change_over" type="hidden" id="period_checkin_checkout_change_over" value="6">

                <div style="padding-bottom: 40px;">
                    <div class="col-md-6">
                        <button type="button" id="kbi_allinone_booking_existing_customer" style="width: 100%" class="wpb_button  wpb_btn-info  wpb_regularsize   wpestate_vc_button  vc_button">Pour un client existant</button>
                    </div>

                    <div class="col-md-6">
                        <button type="button" id="kbi_allinone_booking_new_customer" style="width: 100%" class="wpb_button  wpb_btn-info  wpb_regularsize   wpestate_vc_button  vc_button">Pour un nouveau client</button>
                    </div>
                </div>

                <div id="kbi_dates" class="hidden">
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-md-6">
                        <label for="start_date_owner_book"><?php esc_html_e('Start Date','wprentals');?></label>
                        <input type="text" id="start_date_owner_book" size="40" name="booking_from_date" class="form-control" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="end_date_owner_book"><?php  esc_html_e('End Date','wprentals');?></label>
                        <input type="text" id="end_date_owner_book" size="40" name="booking_to_date" class="form-control" value="">
                    </div>
                </div>

<!--
                <div id="kbi_reservation_set_price_saison_form" class="hidden">

                    <div class="col-md-6">
                        <label for="coment">Nouveau Tarif / <b>Semaine</b> en €</label>
                        <input type="number" id="new_custom_price_week" size="40" class="form-control kbi_calc_price_week_to_day">
                    </div>

                    <div class="col-md-6">
                        <label for="coment">Nouveau Tarif / <b>Jour</b> en €</label>
                        <input type="number" id="new_custom_price" size="40" name="new_custom_price" class="form-control kbi_calc_price_day_to_week">
                    </div>

                </div>
-->

                <div id="kbi_reservation_set_booking_for_user" class="col-md-12 hidden" style="padding-bottom: 20px">

                    <select name="" id="kbi_booking_user" style="width: 100%;">
                        <option></option>
                    </select>

                    <button type="submit" id="allinone_set_custom_for_user" style="width: 100%;margin-top: 20px;" class="wpb_button  wpb_btn-info  wpb_regularsize   wpestate_vc_button  vc_button">Valider la réservation</button>
                </div>

                <div id="kbi_reservation_set_booking_for_new" class="hidden">

                    <div class="col-md-6">
                        <label for="kbi_user_lastname">Nom</label>
                        <input type="text" id="kbi_user_lastname" size="40" name="kbi_user_lastname" class="form-control" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="kbi_user_firstname">Prenom</label>
                        <input type="text" id="kbi_user_firstname" size="40" name="kbi_user_firstname" class="form-control" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="kbi_user_email">Adresse email</label>
                        <input type="email" id="kbi_user_email" size="40" name="kbi_user_email" class="form-control" value="">
                    </div>

                    <div class="col-md-6">
                        <label for="kbi_user_tel">Tel</label>
                        <input type="text" id="kbi_user_tel" size="40" name="kbi_user_tel" class="form-control" value="">
                    </div>

                    <button type="submit" id="allinone_set_custom_for_new" style="width: 100%;margin-top: 20px;" class="wpb_button  wpb_btn-info  wpb_regularsize   wpestate_vc_button  vc_button">Valider la réservation</button>
                </div>




<!--                allinone_set_custom-->

                <!--                wpestate_ajax_add_booking => créer resa-->
                <!--
                                fromdate: 15-02-21
                                todate: 17-02-21
                                listing_edit: 133
                                comment: sss
                                confirmed: 1
                                security: d42bb4ee73
                                allinone: 1
                                -->

                <!--                wpestate_ajax_add_allinone_custom => créer plage de date custom-->
                <!--
                                book_from: 15-02-21
                                book_to: 17-02-21
                                listing_id: 133
                                new_price: 70
                                period_min_days_booking: 7
                                period_checkin_change_over: 6
                                period_checkin_checkout_change_over: 6
                                security: d42bb4ee73
                -->

            </div><!-- /.modal-body -->


        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php


function wpestate_get_calendar_allinone ($initial = true, $echo = true) {
    global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;
    $daywithpost =array();
    // week_begins = 0 stands for Sunday


    $time_now  = current_time('timestamp');
    $now=date('Y-m-d');
    $date = new DateTime();

    $thismonth = gmdate('m', $time_now);
    $thisyear  = gmdate('Y', $time_now);

    $unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);
    $last_day = date('t', $unixmonth);

    $month_no       =   1;
    $max_month_no   =   intval   ( wprentals_get_option('wp_estate_month_no_show','') );

        while ($month_no<$max_month_no){

            wpestate_draw_month_allinone($month_no, $unixmonth, $daywithpost,$thismonth,$thisyear,$last_day);
            $date->modify( 'first day of next month' );
            $thismonth=$date->format( 'm' );
            $thisyear  = $date->format( 'Y' );
            $unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);
            $month_no++;
        }

}




function    wpestate_draw_month_allinone($month_no, $unixmonth, $daywithpost,$thismonth,$thisyear,$last_day){
    global $wpdb, $m, $monthnum, $year, $wp_locale, $posts,$current_user;

    $week_begins = intval(get_option('start_of_week'));

    $calendar_output='';
    $initial=true;
    $echo=true;

    $table_style='';
    if( $month_no>1 ){
           $table_style='style="display:none;"';
    }

    $calendar_output = '<div class="booking-calendar-wrapper-allinone " data-mno="'.esc_attr($month_no).'" '.trim($table_style).'>';
    $calendar_output .= '<div class="month-title"> '. date_i18n("F", mktime(0, 0, 0, $thismonth, 10)).' '.$thisyear.' </div>';
    $calendar_output .= '<div class="property_tab_header"></div> <div class="calendar_tab_header">';

    $myweek = array();

        $daysinmonth = intval(date('t', $unixmonth));
        for ( $day = 1; $day <= $daysinmonth; ++$day ) {

                $timestamp = strtotime( $day.'-'.$thismonth.'-'.$thisyear).' | ';
                $timestamp_java = strtotime( $day.'-'.$thismonth.'-'.$thisyear);

                $dayname = date_i18n ( 'D', $timestamp_java);

                $has_past_class='';
                if($timestamp_java < (time()-24*60*60)  ){
                    $has_past_class="has_past";
                }else{
                    $has_past_class="has_future";
                }
                $is_reserved=0;
                $reservation_class='';
                if ( $day == gmdate('j', current_time('timestamp')) && $thismonth == gmdate('m', current_time('timestamp')) && $thisyear == gmdate('Y', current_time('timestamp')) ){
                    $calendar_output .= '<div class="calendar-today  calendar_pad_title '.$has_past_class.' "  data-curent-date="'.esc_attr($timestamp_java).'">';
                }
                else{// is not today and no resrvation
                    $calendar_output .= '<div class="calendar-free calendar_pad_title '.esc_attr($has_past_class).'"   data-curent-date="'.esc_attr($timestamp_java).'">';
                }
                $calendar_output .= '<div class="dayname">'.$dayname.'</div>';
                $calendar_output .= $day;
                $calendar_output .= '</div>';

        }
      $calendar_output .= '</div>';


        /** kbi_comment => Pour afficher seulement les loc saison on a rajouté une tax query ici **/
        $args = array(
            'post_type'        =>  'estate_property',
            'author'           =>  $current_user->ID,
            'posts_per_page'    => -1,
            'post_status'      =>  array( 'any' ),
            'tax_query' =>  array(
                array(
                  'taxonomy' => 'property_category',
                  'field'    => 'slug',
                  'terms'    => 'location-saisonniere'
                )
            )
        );


        $prop_selection = new WP_Query($args);
        if( !$prop_selection->have_posts() ){
            $calendar_output.= ' '.esc_html__( 'You don\'t have any properties yet!','wprentals').' ';
        }else{
            while ($prop_selection->have_posts()): $prop_selection->the_post();
                $post_id                    =   get_the_ID();
                $property_operation =   get_post_meta($post_id, 'property_operation', true);
                $property_ref       =   get_post_meta($post_id, 'property_ref', true);
                $property_mandat    =   get_post_meta($post_id, 'property_mandat', true);
                $ref = in_array($property_operation, array('vente', 'location')) ? $property_mandat : $property_ref;
                $link= esc_url ( get_permalink() );
                $calendar_output.=  '<div class="property_tab_list_header"><a href="'.esc_url($link).'">';
                $title=get_the_title();
                $calendar_output .= mb_substr( $ref.' '.html_entity_decode( $title ), 0, 20);
                if(strlen($title)>20){
                    $calendar_output.= '...';
                }
                $calendar_output.='</a></div>';
                $calendar_output .= wpestate_draw_month_for_listing($post_id, $daysinmonth, $thismonth, $thisyear);
            endwhile;
        }
        $calendar_output .= '</div>';

        print trim($calendar_output) ;

}







function wpestate_draw_month_for_listing ($post_id, $daysinmonth, $thismonth, $thisyear) {
    $calendar_output_month='';
    $reservation_array  = get_post_meta($post_id, 'booking_dates',true  );

    if ( !is_array($reservation_array) || $reservation_array==''){
        $reservation_array=array();
    }

    $start_reservation      =   '';
    $end_reservation        =   '';
    $end_reservation_class  =   '';
    $reservation_class      =   '';

    for ( $day = 1; $day <= $daysinmonth; ++$day ) {

            $timestamp      = strtotime( $day.'-'.$thismonth.'-'.$thisyear).' | ';
            $timestamp_java = strtotime( $day.'-'.$thismonth.'-'.$thisyear);

            $dayname =  date( 'D', $timestamp_java);

            $has_past_class='';
            if($timestamp_java < (time()-24*60*60)  ){
                $has_past_class="has_past";
            }else{
                $has_past_class="has_future";
            }
            $is_reserved=0;
            $reservation_class='';
            $booking_type_class='';



            if ( $day == gmdate('j', current_time('timestamp')) && $thismonth == gmdate('m', current_time('timestamp')) && $thisyear == gmdate('Y', current_time('timestamp')) ){
                // if is today check for reservation

                if(array_key_exists ($timestamp_java,$reservation_array) ){

                    if( is_numeric ($reservation_array[$timestamp_java]) !=0 ){
                        $booking_type_class=' allinone_internal_booking ';
                    }else{
                        $booking_type_class=' allinone_external_booking ';
                    }
                    $calendar_output_month .= '<div class="calendar-reserved calendar_pad '.esc_attr($has_past_class).' "    data-curent-id="'.esc_attr($post_id).'"   data-curent-date="'.esc_attr($timestamp_java).'">'.wpestate_draw_reservation_allinone($reservation_array[$timestamp_java]);
                }else{
                    $calendar_output_month .= '<div class="calendar-today calendar_pad '.esc_attr($has_past_class).' "     data-curent-id="'.esc_attr($post_id).'"     data-curent-date="'.esc_attr($timestamp_java).'">';
                }

            }

            else if(array_key_exists ($timestamp_java,$reservation_array) ){ // check for reservation
                $end_reservation=1;
                if($start_reservation == 1){
                    $reservation_class  =   ' kbi_start_reservation start_reservation';
                    $start_reservation  =   0;
                }else{
                    $reservation_class  =   ' start_reservation';
                }

                if( is_numeric ($reservation_array[$timestamp_java]) !=0 ){
                    $booking_type_class     =   ' allinone_internal_booking ';
                    $end_reservation_class  =   ' end_allinone_internal_booking ';
                }else{
                    $booking_type_class     =   ' allinone_external_booking ';
                    $end_reservation_class  =   ' end_allinone_external_booking ';
                }

                $calendar_output_month .= '<div class="calendar-reserved calendar_pad '.esc_attr($has_past_class.$reservation_class.$booking_type_class).' "   data-curent-id="'.esc_attr($post_id).'"   data-curent-date="'.esc_attr($timestamp_java).'">'.wpestate_draw_reservation_allinone($reservation_array[$timestamp_java]);
            }

            else{// is not today and no resrvation

                $start_reservation=1;

                if($end_reservation===1){
                    $reservation_class=' kbi_end_reservation start_reservation '.$end_reservation_class;
                    $end_reservation=0;
                }
                $calendar_output_month .= '<div class="calendar-free calendar_pad '.$has_past_class.$reservation_class.'"    data-curent-id="'.esc_attr($post_id).'"       data-curent-date="'.esc_attr($timestamp_java).'">';
            }
            $calendar_output_month .= '</div>';
    }
    return $calendar_output_month;
}



function wpestate_draw_reservation_allinone($reservation_note){
    //$reservation_array[$timestamp_java]

    if ( is_numeric($reservation_note)!=0){
        return '<div class="rentals_reservation allinone_reservation" data-internal-reservation="'.esc_attr($reservation_note).'" >'.esc_html__('Booking id','wprentals').': '.$reservation_note.'</div>';
    }else{

        if (strpos($reservation_note,'@') !== false) {
            $reservation_array=  explode('@', $reservation_note);
            return '<div class="rentals_reservation external_reservation allinone_reservation">'.$reservation_array[1].'</div>';
        }else{
            return '<div class="rentals_reservation external_reservation allinone_reservation">'.esc_html__('External Booking','wprentals').'</div>';
        }


    }

}


$ajax_nonce = wp_create_nonce( "wprentals_allinone_nonce" );
print'<input type="hidden" id="wprentals_allinone" value="'.esc_html($ajax_nonce).'" />    ';


wp_reset_query();
get_footer();
?>
