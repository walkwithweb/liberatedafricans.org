<?php
require '../database.php';?>

<?php
if(isset($_POST['perPage'])) {
    $perPage = $_POST['perPage']; 
} else {
    $perPage = 10;
}

// REGIONS -------------------------------------------------------------------------------------------------------------------
$Field26 = array();
$cityname = array();
$regional = array();
$Latitude = array();
$Longitude = array();

// PORTS ---------------------------------------------------------------------------------------------------------------------
$Field27 = array();
$cityname1 = array();
$port = array();
$Latitude1 = array();
$Longitude1 = array();

/* Summary Stats */
$Stats = array("cases_tot"=>"0", "enslaved_tot"=>"0", "liberated_tot"=>"0", "courts_tot"=>"0", "registered_tot"=>"0");

/* Queries for Map, Table, Summary and Timeline sections */
$sqlport = "SELECT Field27, p.Name as Place, p.X as Latitude, p.Y as Longitude, SUM(Field9) AS liberated_tot 
from person 
left join CV_Places as p on (p.ID = person.Field27) ";

$sqlsummary = "SELECT count(*) as cases_tot, count(distinct(Field7)) as courts_tot, SUM(Field8) AS enslaved_tot, SUM(Field9) AS liberated_tot, SUM(Field10) as registered_tot from person ";

$sqlstatsdepts = "SELECT Field6, CV_Govt_Departments.Name as govt_dept, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Govt_Departments on (CV_Govt_Departments.ID = person.Field6) ";

$sqlstatscourts = "SELECT MIN(Field6) as Field6, Field7, CV_Court_Names.Name as court_name, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Court_Names on (CV_Court_Names.ID = person.Field7) ";

$sqlstatsregion = "SELECT Field26, CV_Places.Name as Region, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Places on (CV_Places.ID = person.Field26) ";

$sqlstatsplaces = "SELECT MIN(Field26) as Field26, Field27, CV_Places.Name as Place, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Places on (CV_Places.ID = person.Field27) ";

$sqltimeline = "SELECT Field2 AS Decade, SUM(Field9) AS amt from person ";

if(isset($_POST['fltr']) && $_POST['fltr']=="VD"){
    $where = ""; // Constraints already in current query
} else {
    $where = "WHERE person.Field27!='0' AND `online` = '1'"; // Constrained by Field27 - Place and only online data 
}

if(isset($_POST['fltr']) && $_POST['fltr']=="All"){
    if(isset($_POST['startDate'])){
        $where .= " and Field2 BETWEEN '".$_POST['startDate']."' AND '".$_POST['EndDate']."'";
    }
    $allClicked = array(); // regions, govt. depts
    $cvIDs = array(); // ports, courts 
    if(isset($_POST['allClicked'])){
        $allClicked = $_POST['allClicked'];
    }
    if(isset($_POST['cvIDs'])){
        $cvIDs = $_POST['cvIDs'];
    }
    // Departure Place - 0
    if(!empty($allClicked[0]) && !empty($cvIDs[0])){
        // When both entire regions and partially selected ports are available 
        $where .= " and ( Field12 in (" . implode(",", $allClicked[0]) . ") or Field13 in (" . implode(",", $cvIDs[0]) . "))";
      } else if(!empty($allClicked[0]) && empty($cvIDs[0])){
        // Only entire regions
        $where .= " and Field12 in (" . implode(",", $allClicked[0]) . ")";
      } else if(empty($allClicked[0]) && !empty($cvIDs[0])){
        // Only specific ports 
        $where .= " and Field13 in (" . implode(",", $cvIDs[0]) . ")";
      } 
    // Arrival Place - 1
    if(!empty($allClicked[1]) && !empty($cvIDs[1])){
        // When both entire regions and partially selected ports are available 
        $where .= " and ( Field26 in (" . implode(",", $allClicked[1]) . ") or Field27 in (" . implode(",", $cvIDs[1]) . "))";
      } else if(!empty($allClicked[1]) && empty($cvIDs[1])){
        // Only entire regions
        $where .= " and Field26 in (" . implode(",", $allClicked[1]) . ")";
      } else if(empty($allClicked[1]) && !empty($cvIDs[1])){
        // Only specific ports 
        $where .= " and Field27 in (" . implode(",", $cvIDs[1]) . ")";
      } 
    // Courts - 2
    if(!empty($allClicked[2]) && !empty($cvIDs[2])){
        // When both entire govt. depts and partially selected courts are available 
        $where .= " and Field6 in (" . implode(",", $allClicked[2]) . ") or Field7 in (" . implode(",", $cvIDs[2]) . ")";
      } else if(!empty($allClicked[2]) && empty($cvIDs[2])){
        // Only entire govt. depts
        $where .= " and Field6 in (" . implode(",", $allClicked[2]) . ")";
      } else if(empty($allClicked[2]) && !empty($cvIDs[2])){
        // Only specific courts 
        $where .= " and Field7 in (" . implode(",", $cvIDs[2]) . ")";
      }
} else if(isset($_POST['fltr']) && $_POST['fltr']=="AS") {
  $advanced_query = $_POST['adv-search'];
  $where .= $advanced_query;
} else if(isset($_POST['fltr']) && $_POST['fltr']=="VD") {
  $id = $_POST['pid'];
  $view_details_query = $_POST['cQuery'];
  $where .= $view_details_query . " AND Field27 = '".$id."'";
}

$cQuery = $where;

/* Apply the filter on the different queries */
$sqlport .= $where . " GROUP BY person.Field27 order by liberated_tot DESC;";
$sqlsummary .= $where;
$sqlstatsdepts .= $where . " GROUP BY Field6 ORDER BY liberated_tot DESC";
$sqlstatscourts .= $where . " GROUP BY Field7 ORDER BY liberated_tot DESC";
$sqlstatsregion .= $where . " GROUP BY Field26 ORDER BY liberated_tot DESC";
$sqlstatsplaces .= $where . " GROUP BY Field27 ORDER BY liberated_tot DESC";
$sqltimeline .= $where . " GROUP by Field2";

/* Get Data */ 
$Govts = array(); 
$Courts = array(); 
$Govts_Courts = array(); 
$Regs = array();
$Places = array();
$Regs_Places = array();
$data = array();
$entries = array();
$noResults = false;

$query = $conn->query($sqlport);
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Map */
    $Field27[]=$row['Field27'];
    $cityname1[]=$row['Place'];
    $port[]=$row['liberated_tot'];
    $Latitude1[]=round(floatval($row['Latitude']),2);
    $Longitude1[]=round(floatval($row['Longitude']),2);
}

$query = $conn->query($sqlsummary);
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Summary */ 
    $Stats['cases_tot'] = $row['cases_tot'];
    $Stats['courts_tot'] = $row['courts_tot'];
    $Stats['enslaved_tot'] = $row['enslaved_tot'];
    $Stats['liberated_tot'] = $row['liberated_tot'];
    $Stats['registered_tot'] = $row['registered_tot'];
}

$query = $conn->query($sqlstatsdepts);
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Unique Govt. Department*/
    $Govts[$row['Field6']] = array('name'=>$row['govt_dept'],'enslaved_tot'=>$row['enslaved_tot'],'liberated_tot'=>$row['liberated_tot'],'registered_tot' => $row['registered_tot']);
}

$query = $conn->query($sqlstatscourts);
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Unique Govt. Department */
    $Courts[$row['Field7']] = array('name'=>$row['court_name'],'enslaved_tot'=>$row['enslaved_tot'],'liberated_tot'=>$row['liberated_tot'],'registered_tot' => $row['registered_tot']); 

    /* Govts, Courts connector */
    if(array_key_exists($row['Field6'],$Govts_Courts)){
        $tempArr = $Govts_Courts[$row['Field6']];
        if(!in_array($row['Field7'],$tempArr)){
            $tempArr[] = $row['Field7'];
            $Govts_Courts[$row['Field6']] = $tempArr;
        }
    } else {
        $tempArr = array();
        $tempArr[] = $row['Field7']; 
        $Govts_Courts[$row['Field6']] = $tempArr; 
    }
}

$query = $conn->query($sqlstatsregion);
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Unique Region */ 
    $Regs[$row['Field26']] = array('name'=>$row['Region'],'enslaved_tot'=>$row['enslaved_tot'],'liberated_tot'=>$row['liberated_tot'],'registered_tot' => $row['registered_tot']);
}

$query = $conn->query($sqlstatsplaces);
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Unique Places */
    $Places[$row['Field27']] = array('name'=>$row['Place'],'enslaved_tot'=>$row['enslaved_tot'],'liberated_tot'=>$row['liberated_tot'],'registered_tot' => $row['registered_tot']); 

    /* Regions, Place connector */
    if(array_key_exists($row['Field26'],$Regs_Places)){
        $tempArr = $Regs_Places[$row['Field26']];
        if(!in_array($row['Field27'],$tempArr)){
            $tempArr[] = $row['Field27'];
            $Regs_Places[$row['Field26']] = $tempArr;
        }
    } else {
        $tempArr = array();
        $tempArr[] = $row['Field27']; 
        $Regs_Places[$row['Field26']] = $tempArr; 
    }
}

$query = $conn->query($sqltimeline);
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Get Data for line graph - Timeline */
    $data[]=$row['Decade'];
    $entries[]=$row['amt'];
}

if(!$port){
    // empty array - no results 
    $noResults = true;
}

/* Get Data for stats by region - Summary */
if(!$noResults){
    $regionHtml = '';
    foreach($Regs as $rid => $rdata){
        $region_name = $rdata['name'];
        $region_id = $rid;
        $region_liberated = number_format($rdata['liberated_tot']);

        $regionHtml .= '<div class="accordion-item">';
        $regionHtml .= '<h2 class="accordion-header" id="heading'.$region_id.'">';
        $regionHtml .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$region_id.'" aria-expanded="true" aria-controls="collapse'.$region_id.'">'.$region_name.' - '.$region_liberated.'</button>';
        $regionHtml .= '</h2>';
        $regionHtml .= '<div id="collapse'.$region_id.'" class="accordion-collapse collapse" aria-labelledby="heading'.$region_id.'" data-bs-parent="#accordionStats">';
        $regionHtml .= '<div class="accordion-body">';

        /*SQL - Places*/ 
        if(array_key_exists($rid, $Regs_Places)){
            $places_arr = $Regs_Places[$rid];
            $placeHtml = '<div class="table-responsive-md"><table class="table table-striped"><thead><tr><th>Place</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
            foreach($places_arr as $pid):
            $pdata = $Places[$pid];
            $placeHtml.='<tr>';
            $placeHtml.='<td>'.$pdata['name'].'</td>';
            $placeHtml.='<td>'.number_format($pdata['liberated_tot']).'</td>';
            $placeHtml.='<td>'.number_format($pdata['registered_tot']).'</td>';
            $placeHtml.='</tr>';
            endforeach;
            $placeHtml.='</tbody></table></div>';
        } else {
            $placeHtml = '<div class="table-responsive-md"><table class="table table-striped"><thead><tr><th>Place</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
            $placeHtml.='<tr>';
            $placeHtml.='<td></td>';
            $placeHtml.='<td></td>';
            $placeHtml.='<td></td>';
            $placeHtml.='</tr>';
            $placeHtml.='</tbody></table></div>';
        }

        $regionHtml .= $placeHtml;
        $regionHtml .= '</div></div></div>';
    }
} else {
// No results found
}

/* Get Data for stats by govt departments - Summary */
if(!$noResults){
    $deptsHtml = '';
    foreach($Govts as $gid =>$gdata){
        $dept_name = $gdata['name'];
        $dept_id = $gid;
        $dept_liberated = number_format($gdata['liberated_tot']);
        $dept_registered = number_format($gdata['registered_tot']);

        $deptsHtml .= '<div class="accordion-item">';
        $deptsHtml .= '<h2 class="accordion-header" id="heading'.$dept_id.'">';
        $deptsHtml .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$dept_id.'" aria-expanded="true" aria-controls="collapse'.$dept_id.'">'.$dept_name.' - '.$dept_liberated.'</button>';
        $deptsHtml .= '</h2>';
        $deptsHtml .= '<div id="collapse'.$dept_id.'" class="accordion-collapse collapse" aria-labelledby="heading'.$dept_id.'" data-bs-parent="#accordionStatsCourts">';
        $deptsHtml .= '<div class="accordion-body">';

        /*SQL - Courts*/ 
        $courts_arr = $Govts_Courts[$gid];
        $courtHtml = '<div class="table-responsive-md"><table class="table table-striped"><thead><tr><th>Court</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
        foreach($courts_arr as $cid){
        $cdata = $Courts[$cid];
        $courtHtml.='<tr>';
        $courtHtml.='<td>'.$cdata['name'].'</td>';
        $courtHtml.='<td>'.number_format($cdata['liberated_tot']).'</td>';
        $courtHtml.='<td>'.number_format($cdata['registered_tot']).'</td>';
        $courtHtml.='</tr>';
        }
        $courtHtml.='</tbody></table></div>';

        $deptsHtml .= $courtHtml;
        $deptsHtml .= '</div></div></div>';
    }
} else {
// No results found
}

if(!$noResults){
    $returnedTotal = $Stats['cases_tot'];
    $totalPages = ceil($returnedTotal/$perPage);
} else {
    $returnedTotal = 0;
    $totalPages = 1;
}

echo json_encode(array($Field26,$cityname,$regional,$Latitude,$Longitude,$Field27,$cityname1,$port,$Latitude1,$Longitude1,$sqlport,$Stats,$data,$entries,$cQuery,$noResults,$regionHtml,$deptsHtml,$returnedTotal,$totalPages)); 
?>

