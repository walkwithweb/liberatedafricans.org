<?php 
    require_once("../database.php");
    /* Get the CV_Location data */
    $CV_Location = array();
    $q_CV_source_fields1= "SELECT * FROM CV_Location_AL";
    $query_CV_source_fields1 = $conn->query($q_CV_source_fields1);
    while($row = $query_CV_source_fields1->fetch(PDO::FETCH_ASSOC)){
        $id = $row['ID'];
        $CV_Location[$id] = $row;
    }

    /* Get the CV_Nationality data */
    $CV_Nationality = array();
    $q_CV_source_fields2= "SELECT * FROM CV_Nationality_AL";
    $query_CV_source_fields2 = $conn->query($q_CV_source_fields2);
    while($row = $query_CV_source_fields2->fetch(PDO::FETCH_ASSOC)){
        $id = $row['ID'];
        $CV_Nationality[$id] = $row;
    }

    $perPage = 15;
    if (isset($_POST['page'])) {
        $page  = $_POST['page'];
    } else {
        $page=1;
    }
    $startFrom = ($page-1) * $perPage;
    $toRow = ($page * $perPage);

    $q_source_data = "SELECT objectID, UI as 'UID', Field1 as 'Title', Field4 as 'Date', Field27 as 'Location', Field31 as 'Country' FROM `object_AL`";
    if(isset($_POST['ftr']) && $_POST['ftr']=='1'){
        if(isset($_POST['fromDate']) && isset($_POST['toDate'])){
            $fDate = $_POST['fromDate'];
            $eDate = $_POST['toDate'];
            $q_source_data .= " WHERE (Field4 BETWEEN ".$fDate." AND ".$eDate.")";
        }
        if(isset($_POST['country']) && !empty($_POST['country'])){
            $cData = $_POST['country'];
            foreach($cData as $c){
                $q_source_data .= " AND (Field31 LIKE '".$c.";' or Field31 LIKE '".$c.";%' or Field31 LIKE '%;".$c.";' or Field31 LIKE '%;".$c.";%')";
            }
        }
        if(isset($_POST['place']) && !empty($_POST['place'])){
            $pData = $_POST['place'];
            foreach($pData as $p){
                $q_source_data .= " AND (Field27 LIKE '".$p.";' or Field27 LIKE '".$p.";%' or Field27 LIKE '%;".$p.";' or Field27 LIKE '%;".$p.";%')";
            }
        }
    }
    $sqlForCount = $q_source_data;
    /* Get the objects - per page */
    $q_source_data.= " ORDER BY Field4 ASC LIMIT $startFrom,$perPage";
    $query_source_data = $conn->query($q_source_data);
    $source_results = $query_source_data->fetchAll(PDO::FETCH_ASSOC);
    $paginationHtml = '';

    $foundObjects = false; // Flag 
    $objectCount = 0; 
    $totalPages = 0;

    if(!$source_results){
        $foundObjects = false;
        $paginationHtml.= '<p>No records found. <a class="reload_btn" href="legislation.php">Reload page <i class="fa-regular fa-arrow-rotate-left"></i></a></p>';
    } else {
        $foundObjects = true;
        /* Get the count */
        $q = $conn->query($sqlForCount);
        $q_res = $q->fetchAll(PDO::FETCH_ASSOC);
        $objectCount = count($q_res);
        $totalPages = ceil($objectCount/$perPage);
        $paginationHtml.= '<div id="crd-view" class="row row-cols-md-2 row-cols-lg-3 g-4 p-3 mb-5 rounded">';
        foreach($source_results as $key => $source_data):
            $paginationHtml.= '<div class="col">';
            $paginationHtml.= '<div class="card sub-card h-100 cr-card" style="min-height:350px;">';
            $paginationHtml.= '<div class="card-body">';
            $paginationHtml.= '<a class="link-box-color" href="../databases/source_details_AL.php?ObjectID='.$source_data['objectID'].'" target="_blank">';
            $paginationHtml.= '<h3 class="card-title">UID: '.$source_data['UID'].'</h3>';
            $paginationHtml.= '<p class="card-text"><strong>Date: </strong>'.$source_data['Date'].'</p>';
            $paginationHtml.= '<p class="card-text"><strong>Title: </strong>'.$source_data['Title'].'</p>';

            $sCountry = '';
            $selectedcountries = $source_data['Country'];
            $selectedcountries_arr = preg_split('/;/', $source_data['Country'], -1, PREG_SPLIT_NO_EMPTY);

            if(count($selectedcountries_arr)>0){
                $selectedcountry = array();
                foreach($selectedcountries_arr as $countryID){
                    /* Loop through the locations and get names from CV */
                    if($countryID != 0 && $countryID != '' && $countryID != null){
                        if(array_key_exists($countryID,$CV_Nationality)){
                            array_push($selectedcountry, $CV_Nationality[$countryID]['Name']);
                        }
                    } else {
                        array_push($selectedcountry,'');
                    }
                }
                if(count($selectedcountry)==1){
                    $sCountry = $selectedcountry[0];
                } else if(count($selectedcountry)>1){
                    $sCountry = implode(", ",$selectedcountry);
                } else {
                    $sCountry = '';
                }
            } else {
                $sCountry = '';
            }
            $paginationHtml.= '<p class="card-text"><strong>Country: </strong>'.$sCountry.'</p>';

            $sLoc = '';
            $selectedlocations=$source_data['Location'];
            $selectedlocations_arr = preg_split('/;/', $source_data['Location'], -1, PREG_SPLIT_NO_EMPTY);
                            
            if(count($selectedlocations_arr)>0){
                $selectedloc = array();
                /* Loop through the locations and get names from CV */
                foreach($selectedlocations_arr as $locationID){
                    if($locationID != 0 && $locationID != '' && $locationID != null){
                        array_push($selectedloc, $CV_Location[$locationID]['Name']);
                    } else {
                        array_push($selectedloc,'');
                    }
                }

                if(count($selectedloc)==1){
                    $sLoc = $selectedloc[0];
                } else if(count($selectedloc)>1){
                    $sLoc = implode(", ",$selectedloc);
                } else {
                    $sLoc = '';
                }
            } else {
                $sLoc = '';
            }
            $paginationHtml.= '<p class="card-text"><strong>Place: </strong>'.$sLoc.'</p>';
            $paginationHtml.= '</a>';
            $paginationHtml.= '</div>';
            $paginationHtml.= '<div class="card-footer">';
            $paginationHtml.= '<small class="text-muted"><a class="category_btn primary_text_color" href="../databases/source_details_AL.php?ObjectID='.$source_data['objectID'].'" target="_blank"><h6 class="cr">View Source Details</h6></a></small>';
            $paginationHtml.= '</div>';
            $paginationHtml.= '</div>';
            $paginationHtml.= '</div>';
        endforeach;
        $paginationHtml.= '</div>';
        /* Assemble table view */
        $paginationHtml.= '<div id="tbl-view" class="table-responsive p-3 pt-5 ">';
        $paginationHtml.= '<table class="table">';
        $paginationHtml.= '<thead>';
        $paginationHtml.= '<tr>';
        $paginationHtml.= '<th scope="col">UID</th>';
        $paginationHtml.= '<th scope="col">Date</th>';
        $paginationHtml.= '<th scope="col">Title</th>';
        $paginationHtml.= '<th scope="col">Country</th>';
        $paginationHtml.= '<th scope="col">Place</th>';
        $paginationHtml.= '<th scope="col"></th>';
        $paginationHtml.= '</tr>';
        $paginationHtml.= '</thead>';
        $paginationHtml.= '<tbody class="pb-5">';
        foreach($source_results as $key => $source_data):
            /* Start new table row */
            $paginationHtml.='<tr>';
            $paginationHtml.='<td style=white-space:nowrap;>'.$source_data['UID'].'</td>';
            $paginationHtml.='<td>'.$source_data['Date'].'</td>';
            $paginationHtml.='<td>'.$source_data['Title'].'</td>';

            $sCountry = '';
            $selectedcountries = $source_data['Country'];
            $selectedcountries_arr = preg_split('/;/', $source_data['Country'], -1, PREG_SPLIT_NO_EMPTY);

            if(count($selectedcountries_arr)>0){
                $selectedcountry = array();
                foreach($selectedcountries_arr as $countryID){
                    /* Loop through the locations and get names from CV */
                    if($countryID != 0 && $countryID != '' && $countryID != null){
                        if(array_key_exists($countryID,$CV_Nationality)){
                            array_push($selectedcountry, $CV_Nationality[$countryID]['Name']);
                        }
                    } else {
                        array_push($selectedcountry,'');
                    }
                }
                if(count($selectedcountry)==1){
                    $sCountry = $selectedcountry[0];
                } else if(count($selectedcountry)>1){
                    $sCountry = implode(", ",$selectedcountry);
                } else {
                    $sCountry = '';
                }
            } else {
                $sCountry = '';
            }
            $paginationHtml.= '<td>'.$sCountry.'</td>';

            $sLoc = '';
            $selectedlocations=$source_data['Location'];
            $selectedlocations_arr = preg_split('/;/', $source_data['Location'], -1, PREG_SPLIT_NO_EMPTY);
                            
            if(count($selectedlocations_arr)>0){
                $selectedloc = array();
                /* Loop through the locations and get names from CV */
                foreach($selectedlocations_arr as $locationID){
                    if($locationID != 0 && $locationID != '' && $locationID != null){
                        array_push($selectedloc, $CV_Location[$locationID]['Name']);
                    } else {
                        array_push($selectedloc,'');
                    }
                }

                if(count($selectedloc)==1){
                    $sLoc = $selectedloc[0];
                } else if(count($selectedloc)>1){
                    $sLoc = implode(", ",$selectedloc);
                } else {
                    $sLoc = '';
                }
            } else {
                $sLoc = '';
            }
            $paginationHtml.= '<td>'.$sLoc.'</td>';

            $button='<small class="text-muted"><a style="color:#AB4503;font-size:18px;" class="category_btn" target="_blank" href="../databases/source_details_AL.php?ObjectID='.$source_data['objectID'].'" target="_blank"><h6 class="text-center">View Source Details</h6></a></small>';
            $paginationHtml.='<td>'.$button.'</td>';
            $paginationHtml.='</tr>';

        endforeach;
        $paginationHtml.= '</tbody>';
        $paginationHtml.= '</table>';
        $paginationHtml.= '</div>';

    }

    echo json_encode(array($paginationHtml, $foundObjects, $objectCount, $startFrom+1,$toRow, $totalPages, $page, $q_source_data));
?>