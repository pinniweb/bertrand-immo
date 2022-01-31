<?php

    function syncPropertiesPictures($updateAnyway = false){
        // Vars for a pseudo pagination
        $propertiesDone = 0;
        $maxProperties = 9999; // Was for debug syncCapDagde
        $dateNow = new \DateTime();
        $startMicroTime = microtime(true);
        $dir = wp_upload_dir();
        $agencyfile = $dir['basedir'].'/adapt_immo/34566.xml';
        if(file_exists($agencyfile)){
            $xml = simplexml_load_file($agencyfile);
            $nbAddedPictures = 0;
            $nbUpdatedPictures = 0;
            $previousPropertyPosts = array();

            /**
             * On boucle sur les biens de la BDD -> on save l'ancien POST ID et l'ancien prix dans $previousPropertyPosts
             */
            foreach(get_posts(array('post_type' => 'estate_property', 'post_status' => 'publish', 'nopaging' => true, 'tax_query' => array(array('taxonomy' => 'property_category', 'field' => 'ID', 'terms' => array(68, 67))))) as $post){
                if($post->kbi_adapt_immo_id != ''){
                    $previousPropertyPosts[$post->kbi_adapt_immo_id] = array(
                        'ID'             => $post->ID,
                        'last_update'    => ($post->images_last_update != '')?date_create_from_format('Y-m-d H:i:s', $post->images_last_update):false
                    );
                }
            }

            /**
             * ADD / UPDATE
             * On boucle sur les biens du fichiers XML -> On Update les biens existants / on créer les nouveaux
             */
            foreach($xml->annonce as $property){
                $property_id = (int)$property->reference;
                // Check pagination
                if($propertiesDone < $maxProperties){
                    $property_maj_date = (empty($property->date_maj) || $updateAnyway)?new \DateTime():date_create_from_format('Ymd', (string)$property->date_maj);
                    $property_maj_date->setTime(23,59,59);

                    // Check property is not already done
                    echo "Start Property id #$property_id";
                    if(array_key_exists($property_id, $previousPropertyPosts)) // On check si le bien est déjà synchro
                        if(
                            (!$previousPropertyPosts[$property_id]['last_update'] || ($previousPropertyPosts[$property_id]['last_update'] <= $property_maj_date)) &&
                            ($results = updatePropertyPictures($property, $previousPropertyPosts[$property_id]['ID'], $property_id))
                        ){ // On check si il a été mis à jour
                            $nbAddedPictures = $nbAddedPictures+$results['added'];
                            $nbUpdatedPictures = $nbUpdatedPictures+$results['updated'];
                        }
                    echo " - done \r\n";
                    // Increase a counter
                    $propertiesDone++;
                }else{echo "Max nb of property reached \r\n";}

                // Line break when changing property
                echo "\r\n";
            }
        }else
            wp_mail('caro@keole.net', 'Bertrand Immo : Adapt Immo Sync Failed - '.$dateNow->format('d/m/Y'), '<b>File not found : '.$agencyfile.'</b>', array('Content-Type: text/html; charset=UTF-8'));
    }


    /** Utilities **/

    function updatePropertyPictures($property, $post_id, $property_id){
        $results = array(
            'added' => 0,
            'updated' => 0
        );
        $currentAttachments = get_attached_media('image', $post_id);
        $wp_upload_dir = wp_upload_dir();
        $wasUpdated = false;

        $count = 0;
        foreach ($property->liste_photos->photo as $i => $imgUrl){
            $localeFilename = $property_id.'_'.$post_id.'_'.$count;
            $count++;
            $needUpdate = false;
            $currentPictureUrl = false;
            $currentPicturePath = false;

            $currentPicture = get_page_by_title($localeFilename, "OBJECT", 'attachment');

            if($currentPicture){
                $currentPictureUrl = wp_get_attachment_url($currentPicture->ID);
                $currentPicturePath = get_attached_file($currentPicture->ID);
            }

            if($currentPicture && $currentPictureUrl && $currentPicturePath){
                list($width, $height) = getimagesize($imgUrl);
                if(pathinfo($currentPictureUrl, PATHINFO_EXTENSION) !== pathinfo($imgUrl, PATHINFO_EXTENSION) || !isSizeEqual($imgUrl, $currentPicturePath)){
                    wp_delete_attachment($currentPicture->ID, true);
                    $needUpdate = true;
                }
            }

            if(!$currentPicture || ($currentPicture && $needUpdate)){
                $imgPath = $wp_upload_dir['path'].'/'.$localeFilename.'.'.pathinfo($imgUrl, PATHINFO_EXTENSION);

                // Check remote image exist and open pointer to it
                if(@fopen($imgUrl, "r") !== false && $handle1 = fopen($imgUrl, "r")){
                    // Open pointer to destination
                    $handle2 = fopen(
                        $imgPath, "w"
                    );

                    // Copy image's pointer to destination's pointer
                    $copy = stream_copy_to_stream($handle1, $handle2);

                    // Close both
                    fclose($handle1);
                    fclose($handle2);

                    if($copy !== false){
                        $filetype = wp_check_filetype(basename($imgPath),null);
                        $attach_id = wp_insert_attachment(
                            array(
                                'guid'           => $wp_upload_dir['url'].'/'.basename($imgPath),
                                'post_mime_type' => $filetype['type'],
                                'post_title'     => preg_replace( '/\.[^.]+$/', '',basename($imgPath)),
                                'post_content'   => '',
                                'post_status'    => 'inherit',
                                'post_author'    => 1
                            ),
                            $imgPath,
                            $post_id
                        );

                        $attach_data = wp_generate_attachment_metadata($attach_id, $imgPath);
                        wp_update_attachment_metadata($attach_id, $attach_data);

                        if($i == 0){
                            set_post_thumbnail($post_id, $attach_id);
                        }

                        if($attach_id != 0){
                            $wasUpdated = true;
                            ($needUpdate)?$results['updated']++:$results['added']++;
                        }
                    }
                }
            }
        }

        if($wasUpdated){
            $dateNow = new \DateTime();
            update_post_meta($post_id, 'images_last_update', $dateNow->format('Y-m-d H:i:s'));
        }

        return $results;
    }

    function isSizeEqual($newPictureUrl, $currentPicturePath){
        $newPictureSize = sizeOfFile($newPictureUrl);
        $currentPictureSize = filesize($currentPicturePath);
        return (intval($newPictureSize) == intval($currentPictureSize));
    }

    function sizeOfFile($url){ //Curl version
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);
        return $size;
    }

    /** Admin functions **/

    function deletePictures($agency_slug){
        $startMicroTime = microtime(true);
        $nbDeletedPictures = 0;

        foreach(get_posts(array('post_type' => 'bien', 'post_status' => 'publish', 'nopaging' => true, 'tax_query' => array(array('taxonomy' => 'pointfinderlocations', 'field' => 'slug', 'terms' => $agency_slug)))) as $post)
            if($post->kbi_adapt_immo_id != '')
                foreach($currentAttachments = get_attached_media('image', $post->ID) as $currentPicture){
                    delete_post_meta( $post->ID, 'webbupointfinder_item_images', $currentPicture->ID);
                    wp_delete_attachment($currentPicture->ID, true);
                    $nbDeletedPictures++;
                }
    }
