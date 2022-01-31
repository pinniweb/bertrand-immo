<?php
$dpe = intval(get_post_meta($post_id, 'property_dpe', true));
$ges = intval(get_post_meta($post_id, 'property_ges', true));

if($dpe != '' && $dpe != 0){
    $dpe_letter = '';
    $dpe_height = '';

    if($dpe <= 50){
        $dpe_letter = 'A';
        $dpe_height = '34' ;
    }elseif ($dpe >= 51 && $dpe <= 90){
        $dpe_letter = 'B';
        $dpe_height = '69' ;
    }elseif ($dpe >= 91 && $dpe <= 150){
        $dpe_letter = 'C';
        $dpe_height = '104' ;
    }elseif ($dpe >= 151 && $dpe <= 230){
        $dpe_letter = 'D';
        $dpe_height = '139' ;
    }elseif ($dpe >= 231 && $dpe <= 330){
        $dpe_letter = 'E';
        $dpe_height = '174' ;
    }elseif ($dpe >= 331 && $dpe <= 450){
        $dpe_letter = 'F';
        $dpe_height = '209' ;
    }elseif ($dpe > 450){
        $dpe_letter = 'G';
        $dpe_height = '244' ;
    }
}else{
    $dpe = false;
}


if($ges != '' && $ges != 0){
    $ges_letter = '';
    $ges_height = '';

    if($ges <= 5){
        $ges_letter = 'A';
        $ges_height = '34' ;
    }elseif ($ges >= 6 && $ges <= 10){
        $ges_letter = 'B';
        $ges_height = '69' ;
    }elseif ($ges >= 11 && $ges <= 20){
        $ges_letter = 'C';
        $ges_height = '104' ;
    }elseif ($ges >= 21 && $ges <= 35){
        $ges_letter = 'D';
        $ges_height = '139' ;
    }elseif ($ges >= 36 && $ges <= 55){
        $ges_letter = 'E';
        $ges_height = '174' ;
    }elseif ($ges >= 56 && $ges <= 80){
        $ges_letter = 'F';
        $ges_height = '209' ;
    }elseif ($ges > 80){
        $ges_letter = 'G';
        $ges_height = '244' ;
    }
}else{
    $ges = false;
}
?>
<?php if($dpe || $ges): ?>
    <div class="dpe_ges_wrapper">
        <div>
            <?php if($dpe): ?>
                <div style="height: 300px; width: 300px; display: inline-block; background-image: url('<?php echo get_stylesheet_directory_uri() ; ?>/img/dpe_ges/dpe_<?php echo $dpe_letter; ?>.png');">
                    <span style="position: relative; top: <?php echo $dpe_height; ?>px; left: 260px; font-weight: 600"><?php echo $dpe; ?></span>
                </div>
            <?php endif; ?>
            <?php if($ges): ?>
                <div style="height: 300px; width: 300px; display: inline-block; background-image: url('<?php echo get_stylesheet_directory_uri() ; ?>/img/dpe_ges/ges_<?php echo $ges_letter; ?>.png');">
                    <span style="position: relative; top: <?php echo $ges_height; ?>px; left: 260px; font-weight: 600"><?php echo $ges; ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>