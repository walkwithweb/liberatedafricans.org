<?php 
    require_once("../database.php");
    /* Get the CV_Court_Names data */
    $CV_Court = array();
    $q_CV_source_fields1= "SELECT * FROM CV_Court_Names";
    $query_CV_source_fields1 = $conn->query($q_CV_source_fields1);
    while($row = $query_CV_source_fields1->fetch(PDO::FETCH_ASSOC)){
        $id = $row['ID'];
        $CV_Court[$id] = $row;
    }

    /* Get the CV_Places data */
    $CV_Places = array();
    $q_CV_source_fields2= "SELECT * FROM CV_Places";
    $query_CV_source_fields2 = $conn->query($q_CV_source_fields2);
    while($row = $query_CV_source_fields2->fetch(PDO::FETCH_ASSOC)){
        $id = $row['ID'];
        $CV_Places[$id] = $row;
    }

    $perPage = 15;
    if (isset($_POST['page'])) {
        $page  = $_POST['page'];
    } else {
        $page=1;
    }
    $startFrom = ($page-1) * $perPage;
    $toRow = ($page * $perPage);

    $q_source_data = "SELECT objectID, UI, Field11 as 'Court', Field27 as 'Place', Field1 as 'Title', Field5 as 'Year', Field8 as 'desc' FROM `object` WHERE `Field26` = 'TRUE'";
    if(isset($_POST['ftr']) && $_POST['ftr']=='1'){
        if(isset($_POST['fromDate']) && isset($_POST['toDate'])){
            $fDate = $_POST['fromDate'];
            $eDate = $_POST['toDate'];
            $q_source_data .= " AND (Field5 BETWEEN ".$fDate." AND ".$eDate.")";
        }
        if(isset($_POST['court']) && $_POST['court']!=''){
            $c = $_POST['court'];
            $q_source_data .= " AND (Field11='".$c."')";
        }
        if(isset($_POST['place']) && $_POST['place']!=''){
            $p = $_POST['place'];
            $q_source_data .= " AND (Field27='".$p."')";
        }
    } else if(isset($_POST['ftr']) && $_POST['ftr']=='2'){
        if(isset($_POST['colName']) && isset($_POST['sTerm'])){
            $colName = $_POST['colName'];
            $sTerm = $_POST['sTerm'];
            $q_source_data .= " AND (".$colName."='".$sTerm."')";
        }
    }
    $sqlForCount = $q_source_data;
    /* Get the objects - per page */
    $q_source_data.= " ORDER BY Field5 ASC LIMIT $startFrom,$perPage";
    $query_source_data = $conn->query($q_source_data);
    $source_results = $query_source_data->fetchAll(PDO::FETCH_ASSOC);
    $paginationHtml = '';

    $foundObjects = false; // Flag 
    $objectCount = 0; 
    $totalPages = 0;

    if(!$source_results){
        $foundObjects = false;
        $paginationHtml.= '<p>No records found. <a class="reload_btn" href="court-records.php">Reload page <i class="fa-regular fa-arrow-rotate-left"></i></a></p>';
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
            $paginationHtml.= '<a class="link-box-color" href="../databases/source_details_LA.php?ObjectID='.$source_data['objectID'].'" target="_blank">';
            $paginationHtml.= '<h3 class="card-title">UID:'.$source_data['UI'] .'</h3>';
            $paginationHtml.= '<p class="card-text"><strong>Year: </strong>'.$source_data['Year'] .'</p>';
            $paginationHtml.= '<p class="card-text"><strong>Title: </strong>'.$source_data['Title'].'</p>';
            $paginationHtml.= '<p class="card-text"><strong>Court: </strong>'.$source_data['Court'].'</p>';
            $paginationHtml.= '<p class="card-text"><strong>Place: </strong>'.$source_data['Place'].'</p>';
            $paginationHtml.= '<p class="card-text"><strong>Description: </strong>'.$source_data['desc'].'</p>';
            $paginationHtml.= '</a>';
            $paginationHtml.= '</div>';
            $paginationHtml.= '<div class="card-footer">';
            $paginationHtml.= '<small class="text-muted"><a class="category_btn primary_text_color" href="../databases/source_details_LA.php?ObjectID='.$source_data['objectID'].'" target="_blank"><h6 class="cr">View Source Details</h6></a></small>';
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
        $paginationHtml.= '<th scope="col">Title</th>';
        $paginationHtml.= '<th scope="col">Year</th>';
        $paginationHtml.= '<th scope="col">Court</th>';
        $paginationHtml.= '<th scope="col">Place</th>';
        $paginationHtml.= '<th scope="col"></th>';
        $paginationHtml.= '</tr>';
        $paginationHtml.= '</thead>';
        $paginationHtml.= '<tbody class="pb-5">';
        foreach($source_results as $key => $source_data):
            /* Start new table row */
            $paginationHtml.='<tr>';
            $paginationHtml.='<td style=white-space:nowrap;>'.$source_data['UI'].'</td>';
            $paginationHtml.='<td>'.$source_data['Title'].'</td>';
            $paginationHtml.='<td>'.$source_data['Year'].'</td>';
            $paginationHtml.= '<td>'.$source_data['Court'].'</td>';
            $paginationHtml.= '<td>'.$source_data['Place'].'</td>';

            $button='<small class="text-muted"><a style="color:#AB4503;font-size:18px;" class="category_btn" target="_blank" href="../databases/source_details_LA.php?ObjectID='.$source_data['objectID'].'" target="_blank"><h6 class="text-center">View Source Details</h6></a></small>';
            $paginationHtml.='<td>'.$button.'</td>';
            $paginationHtml.='</tr>';

        endforeach;
        $paginationHtml.= '</tbody>';
        $paginationHtml.= '</table>';
        $paginationHtml.= '</div>';

    }

    echo json_encode(array($paginationHtml, $foundObjects, $objectCount, $startFrom+1,$toRow, $totalPages, $page, $q_source_data));
?>