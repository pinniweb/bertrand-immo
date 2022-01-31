<?php
// $preview   =  wp_get_attachment_image_src(get_post_thumbnail_id($post_id), 'wpestate_slider_thumb',$extra);
// $featured  =  intval  ( get_post_meta($post->ID, 'prop_featured', true) );
$arguments      =   array(
    'numberposts'   =>  -1,
    'post_type'     =>  'attachment',
    'post_mime_type'=>  'image',
    'post_parent'   =>  $post->ID,
    'post_status'   =>  null,
    'orderby'         => 'menu_order',
    'order'           => 'ASC',
     'exclude'      =>get_post_thumbnail_id(),
);

$post_attachments   = get_posts($arguments);
if(count($post_attachments)) {
    $full_prty          = wp_get_attachment_image_src($post_attachments[0]->ID, 'listing_full_slider');
}
?>

<div class=" dashboard_imagine">
    <?php
    if (count($post_attachments) > 0){
    ?>
      <a href="<?php print esc_url($link); ?>"><img src="<?php  print esc_url($full_prty[0]); ?>" class="b-lazy dashboad-prop-img img-responsive " alt="<?php esc_html_e('image','wprentals');?>" /></a>
    <?php
    } else{
        $thumb_prop_default =  get_stylesheet_directory_uri().'/img/defaultimage_prop.jpg';?>
        <img src="<?php print esc_url($thumb_prop_default);?>"   class="b-lazy img-responsive dashboad-prop-img  wp-post-image " alt="<?php esc_html_e('image','wprentals');?>" />
    <?php
    }
    ?>
</div>
