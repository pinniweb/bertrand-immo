<?php

    function syncProperties($updateAnyway = false){
        $dateNow = new \DateTime();
        $dir = wp_upload_dir();
        $startMicroTime = microtime(true);
        $agencyfile = $dir['basedir'].'/adapt_immo/34566.xml';
        if(file_exists($agencyfile)){
            $xml = simplexml_load_file($agencyfile);

            $nbAddedProperties = 0;
            $nbUpdatedProperty = 0;
            $nbDeletedProperties = 0;
            $previousPropertyPosts = array();

            /**
             * On boucle sur les propriétés de la BDD -> on save l'ancien POST ID et l'ancien prix dans $previousPropertyPosts
             */
            foreach(get_posts(array('post_type' => 'estate_property', 'post_status' => 'publish', 'nopaging' => true, 'tax_query' => array(array('taxonomy' => 'property_category', 'field' => 'ID', 'terms' => array(68, 67))))) as $post){
                if($post->kbi_adapt_immo_id != ''){
                    $previousPropertyPosts[$post->kbi_adapt_immo_id] = array(
                        'ID'             => $post->ID,
                        'last_update'    => ($post->post_modified)?date_create_from_format('Y-m-d H:i:s', $post->post_modified):new \DateTime()
                    );
                }
            }

            /**
             * ADD / UPDATE
             * On boucle sur les propriétés du fichiers XML -> On Update les propriétés existants / on créer les nouveaux
             */
            foreach($xml->annonce as $property){

                $property_id = (int)$property->reference;
                $property_maj_date = (empty($property->date_maj) || $updateAnyway)?new \DateTime():date_create_from_format('Ymd', (string)$property->date_maj);
                $property_maj_date->setTime(23,59,59);
                if(array_key_exists($property_id, $previousPropertyPosts)){ // On check si le propriété est déjà synchro
                    if($previousPropertyPosts[$property_id]['last_update'] <= $property_maj_date){ //On check si il a été mis à jour depuis la dernière modification
                        if(updateProperty($property, $previousPropertyPosts[$property_id])){
                            $nbUpdatedProperty++;
                        }
                    }
                    unset($previousPropertyPosts[$property_id]);

                }else{ // Si c'est pas le cas on l'importe
                    if($post_id = addProperty($property)){
                        $nbAddedProperties++;
                        unset($previousPropertyPosts[$property_id]);
                    }
                }
            }

            /**
             * DELETE
             * On delete les bataux qui sont en BDD mais plus dans le XML
             */
            foreach($previousPropertyPosts as $property_id => $previousPropertyPost)
                if(deleteProperty($previousPropertyPost['ID'])){
                    $nbDeletedProperties++;
                }
        }else
            wp_mail('caro@keole.net', 'Bertrand Immo : Adapt Immo Sync Failed - '.$dateNow->format('d/m/Y'), '<b>File not found : '.$agencyfile.'</b>', array('Content-Type: text/html; charset=UTF-8'));
    }

    /** Utilities **/
    function addProperty($property){
        $post_id = wp_insert_post(array(
            'post_status'       => 'publish',
            'post_author'       => 9,
            'post_type'         => 'estate_property',
            'post_title'        => (string)$property->titre_fr,
            'post_name'         => (string)$property->titre_fr,
            'post_content'      => (string)$property->texte_fr,
            'meta_input'        => getMetaArray($property)
        ));
        if($post_id){
            addTermToPost($post_id, $property);
            return $post_id;
        }
        return false;
    }

    function updateProperty($property, $previous_post){
        $post_id = wp_update_post(array(
            'ID'                => $previous_post['ID'],
            'post_author'       => 9,
            'post_title'        => (string)$property->titre_fr,
            'post_name'         => (string)$property->titre_fr,
            // 'post_content'      => (string)$property->texte_fr,
            'meta_input'        => getMetaArray($property)
        ));
        if($post_id){
            addTermToPost($post_id, $property);
            return $post_id;
        }
        return false;
    }

    function getMetaArray($property){

        $coords = getCoords(array(
            'ville' => $property->ville,
            'rue' => $property->adresse,
            'num' => $property->numero_voie,
        ));

        return array(
            'kbi_adapt_immo_id'                     => (int)$property->reference,
            'property_operation'                    => (string)$property->operation,
            'property_ref'                          => (int)$property->reference,
            'property_price'                        => (float)$property->prix,
            'property_size'                         => (float)$property->surf_hab,
            'property_rooms'                        => (int)$property->piece,
            'property_bedrooms'                     => (int)$property->nb_chambre,
            'property_bathrooms'                    => (int)$property->nb_salle_deau,
            'property_address'                      => (string)$property->adresse,
            'property_zip'                          => (string)$property->code_postal,
            'property_state'                        => (string)$property->ville,
            'property_country'                      => (string)$property->pays,
            'property_dpe'                          => (int)$property->dpe_consom_energ,
            'property_ges'                          => (int)$property->dpe_emissions_ges,
            'virtual_tour'                          => (int)$property->visite_virtuelle != '0'?$property->visite_virtuelle:false,
            'property_latitude'                     => (float)$coords?$coords[1]:false,
            'property_longitude'                    => (float)$coords?$coords[0]:false,
            'property_mandat'                       => (int)$property->mandat,   
            'property_charges_mensuelles'           => (float)$property->charges_mensuelles,
            'property_nb_etage'                     => (int)$property->nb_etage,
            'property_num_etage'                    => (int)$property->num_etage,
            'property_nb_sdb'                       => (int)$property->nb_sdb,
            'property_nb_salle_deau'                => (int)$property->nb_salle_deau,
            'property_nb_wc'                        => (int)$property->nb_wc,
            'property_nature_chauffage'             => (string)$property->nature_chauffage,
            'property_google_view'                  => 1,
            'advanced_view'                         => 1,
            'prop_featured'                         => 0,
            // check si il manque des meta ?
        );
    }

    function addTermToPost($post_id, $property){
        if($property->balcon == 'oui'){
            wp_set_object_terms($post_id, 'balcon', 'property_features');
        }
        if($property->meuble == '1'){
            wp_set_object_terms($post_id, 'appartement-meuble', 'property_features');
        }
        if($property->ascenseur == '1'){
            wp_set_object_terms($post_id, 'ascenseur', 'property_features');
        }
        if($property->nb_garage != '0'){
            wp_set_object_terms($post_id, 'garage', 'property_features');
        }
        if($property->terrasse == '1'){
            wp_set_object_terms($post_id, 'terrasse', 'property_features');
        }
        if($property->piscine == '1'){
            wp_set_object_terms($post_id, 'piscine', 'property_features');
        }

        switch ($property->chauffage){
            case 'gaz' :
                wp_set_object_terms($post_id, 'chauffage-au-gaz', 'property_features');
                break;
            case 'électrique' :
            case 'électrique, électrique éco.' :
                wp_set_object_terms($post_id, 'chauffage-electrique', 'property_features');
                break;
            case 'bois, électrique, clim. réversible' :
                wp_set_object_terms($post_id, 'cheminee', 'property_features');
                wp_set_object_terms($post_id, 'chauffage-electrique', 'property_features');
                wp_set_object_terms($post_id, 'climatisation', 'property_features');
                break;
            case 'électrique, clim. réversible' :
            case 'électrique éco., clim. réversible' :
            case 'électrique, électrique éco., clim. réversible' :
                wp_set_object_terms($post_id, 'chauffage-electrique', 'property_features');
                wp_set_object_terms($post_id, 'climatisation', 'property_features');
                break;
        }

        switch ($property->ville){
            case 'MONTPELLIER' :
                wp_set_object_terms($post_id, 'montpellier', 'property_city');
                break;
            case 'PALAVAS LES FLOTS' :
                wp_set_object_terms($post_id, 'palavas-les-flots', 'property_city');
                break;
            case 'VILLENEUVE LES MAGUELONE' :
                wp_set_object_terms($post_id, 'villeneuve', 'property_city');
                break;
            case 'LE GRAU DU ROI' :
                wp_set_object_terms($post_id, 'le-grau-du-roi', 'property_city');
                break;
            case 'PEROLS' :
                wp_set_object_terms($post_id, 'perols', 'property_city');
                break;
            case 'LATTES' :
                wp_set_object_terms($post_id, 'lattes', 'property_city');
                break;
        }

        $category = get_term_by('slug', $property->operation, 'property_category');
        
        $action = get_term_by('slug', $property->famille, 'property_action_category');

        if($property->famille == 'local / bureau'){
            $action = get_term_by('slug', 'local-bureau', 'property_action_category');
        }

        if($property->famille == 'local / bureau' && ($property->idtype == 9 || $property->idtype == 56)){
            $action = get_term_by('slug', 'garage-parking', 'property_action_category');
        }
        
        
        wp_set_object_terms($post_id, intval($category->term_id), 'property_category');
        wp_set_object_terms($post_id, intval($action->term_id), 'property_action_category');
    }

    function deleteProperty($post_id){
        foreach(get_attached_media('image', $post_id) as $image)
            wp_delete_attachment($image->ID, true);

        return wp_delete_post($post_id, true);
    }

    function getCoords($address){
        $strAddress = $address['num'] . ' ' .$address['rue']. ' ' .$address['ville'];
        $strAddress = str_replace(' ', '+', $strAddress);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api-adresse.data.gouv.fr/search/?q='.$strAddress,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));
        $resp = curl_exec($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);   //get status code
        curl_close($curl);
        $arrayResp = json_decode($resp);
        if(!empty($arrayResp->features[0])){
            return $arrayResp->features[0]->geometry->coordinates;
        }
        return false;
    }


    /** Admin functions **/
    function deleteProperties($agency_slug){
        $startMicroTime = microtime(true);

        $nbDeletedProperties = 0;

        foreach(get_posts(array('post_type' => 'propriété', 'post_status' => 'publish', 'nopaging' => true, 'tax_query' => array(array('taxonomy' => 'pointfinderlocations', 'field' => 'slug', 'terms' => $agency_slug)))) as $post)
            if(deleteProperty($post->ID))
                $nbDeletedProperties++;
        die;
    }
