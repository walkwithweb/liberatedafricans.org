<?php
  require '../database.php';

  // Collect the data for all the AR Polygons
  /* Broad Regions */
  $broadregions = array();
  $broadregionsN = array();
  /* Sub Regions */
  $subregions = array();
  $subregionsN = array();

  // Find the broad regions -->
  $sql = "SELECT ID, Name FROM `CV_Broad_Regions_AR`";
  $query = $conn->query($sql);
  while ($row = $query->fetch(PDO::FETCH_ASSOC)){
    $rID = $row['ID'];
    $rName = $row['Name'];
    $broadregions[$rID] = $row;
    $broadregionsN[$rName] = $row;
  }

  // Find the sub regions -->
  $sql = "SELECT personID, Name, Field1 FROM `person_AR`";
  $query = $conn->query($sql);
  while ($row = $query->fetch(PDO::FETCH_ASSOC)){
    $rID = $row['personID'];
    $rName = $row['Name'];
    $subregions[$rID] = $row;
    $subregionsN[$rName] = $row;
  }
?>

<?php 
  if(isset($_POST['perPage'])) {
    $perPage = $_POST['perPage']; 
  } else {
    $perPage = 10;
  }

  /* Distinct Regions - Field12 of LA person*/
  $distinctregions = array();
  $regionTotals = array(); /* Key is ID from Field12 that matches CV_Places */
  $isViewDetails = false;
  $ar = ''; // African regions id 
  $dr = ''; // Departure region id - Field12 

  /* Queries for Map, Table, Summary and Timeline sections */
  $sqldistinctregions = "SELECT distinct(Field12), SUM(Field8) as tot, CV_Places.ID, CV_Places.Subregion, CV_Places.Broadregion FROM `person` LEFT JOIN CV_Places on (CV_Places.ID = person.Field12) ";

  $sqlsummary = "SELECT count(*) as cases_tot, count(distinct(Field7)) as courts_tot, SUM(Field8) AS enslaved_tot, SUM(Field9) AS liberated_tot, SUM(Field10) as registered_tot FROM `person` LEFT JOIN CV_Places on (CV_Places.ID = person.Field26) ";

  $sqlstatsdepts = "SELECT Field6, CV_Govt_Departments.Name as govt_dept, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Govt_Departments on (CV_Govt_Departments.ID = person.Field6) LEFT JOIN CV_Places on (CV_Places.ID = person.Field26) ";

  $sqlstatscourts = "SELECT MIN(Field6) as Field6, Field7, CV_Court_Names.Name as court_name, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot from person left join CV_Court_Names on (CV_Court_Names.ID = person.Field7) LEFT JOIN CV_Places on (CV_Places.ID = person.Field26) ";

  $sqlstatsregion = "SELECT Field12, CV_Places.Name as Region, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot, CV_Places.ID, CV_Places.Subregion, CV_Places.Broadregion from person left join CV_Places on (CV_Places.ID = person.Field12) ";

  $sqlstatsplaces = "SELECT Field12, Field13, CV_Places.Name as Place, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot, CV_Places.ID, CV_Places.Subregion, CV_Places.Broadregion from person left join CV_Places on (CV_Places.ID = person.Field13) ";

  $sqltimeline = "SELECT Field2 AS Decade, SUM(Field8) AS amt from person LEFT JOIN CV_Places on (CV_Places.ID = person.Field12) ";

  if(isset($_POST['fltr']) && $_POST['fltr']=="VD"){
    $isViewDetails = true;
    $where = ""; // Constraints already in current query
  } else {
    $where = "WHERE person.Field12!='0' AND person.Field12!='' AND `online` = '1'"; // Constrained by Field12 - region 
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
    $ar = $_POST['sid'];
    $view_details_query = $_POST['cQuery'];
    $where .= $view_details_query;
  }

  $cQuery = $where; 

  /* Apply the filter on the different queries */
  if($isViewDetails){
    $sqldistinctregions .= $where . " AND CV_Places.Subregion ='".$ar."' GROUP BY person.Field12";
  } else {
    $sqldistinctregions .= $where . " GROUP BY person.Field12;";
  }
  
  /* Get numbers per region */
  $query = $conn->query($sqldistinctregions);
  while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Distinct Regions - For Map Polygons */
    $rID = $row['Field12'];
    $sReg = $row['Subregion'];
    if($sReg == ''){
      $sReg = "Unknown";
    }
    $bReg = $row['Broadregion'];
    if($bReg == ''){
      $bReg = "Unknown";
    }
    $tot = $row['tot'];
    $distinctregions[] = $rID;
    $regionTotals[$rID] = array('ID'=>$rID, 'Subregion'=>trim($sReg), 'Broadregion'=>trim($bReg), 'totEnslaved'=>$tot);
    if($isViewDetails){
      $dr = $row['Field12'];
    }
  }

  /* Get Data */
  $totEnslaved = 0;
  if($isViewDetails){
    $sqlsummary .= $where . " AND Field12 ='".$dr."' GROUP BY person.Field12";
  } else {
    $sqlsummary .= $where;
  }

  if($isViewDetails){
    $sqlstatsdepts .= $where . " AND CV_Places.Subregion = '".$ar."' GROUP BY Field6 ORDER BY liberated_tot DESC";
  } else {
    $sqlstatsdepts .= $where . " GROUP BY Field6 ORDER BY liberated_tot DESC";
  }

  if($isViewDetails){
    $sqlstatscourts .= $where . " AND CV_Places.Subregion = '".$ar."' GROUP BY Field7 ORDER BY liberated_tot DESC";
  } else {
    $sqlstatscourts .= $where . " GROUP BY Field7 ORDER BY liberated_tot DESC";
  }

  if($isViewDetails){
    $sqltimeline .= $where . " AND CV_Places.Subregion = '".$ar."' GROUP by Field2";
  } else {
    $sqltimeline .= $where . " GROUP by Field2";
  }

  if($isViewDetails){
    $sqlstatsregion .= $where . " AND CV_Places.Subregion = '".$ar."' GROUP BY Field12 ORDER BY liberated_tot DESC";
  } else {
    $sqlstatsregion .= $where . " GROUP BY Field12 ORDER BY liberated_tot DESC";
  }

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
      $Regs[$row['Field12']] = array('name'=>$row['Region'],'enslaved_tot'=>$row['enslaved_tot'],'liberated_tot'=>$row['liberated_tot'],'registered_tot' => $row['registered_tot']);
  }

  foreach($Regs as $key => $val){
    $placesForThisRegion = array();

    $sqlstatsplacesbyr = "SELECT Field12, Field13, CV_Places.Name as Place, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot, CV_Places.ID, CV_Places.Subregion, CV_Places.Broadregion from person left join CV_Places on (CV_Places.ID = person.Field13) " . $where . " AND Field12 = '".$key."' GROUP BY Field13 ORDER BY liberated_tot DESC"; 

    $query = $conn->query($sqlstatsplacesbyr);
    while($row = $query->fetch(PDO::FETCH_ASSOC)){
      $placesForThisRegion[] = array('name'=>$row['Place'],'enslaved_tot'=>$row['enslaved_tot'],'liberated_tot'=>$row['liberated_tot'],'registered_tot' => $row['registered_tot']); 
    }
    $Regs_Places[$key] = $placesForThisRegion;
  }

  $query = $conn->query($sqltimeline);
  while($row = $query->fetch(PDO::FETCH_ASSOC)){
      /* Get Data for line graph - Timeline */
      $data[]=$row['Decade'];
      $entries[]=$row['amt'];
  }

  $totEnslaved = $Stats['enslaved_tot'];

  if(!$distinctregions){
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
        $places_arr = $Regs_Places[$rid];
        $placeHtml = '<div class="table-responsive-md"><table class="table table-striped"><thead><tr><th>Place</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
        foreach($places_arr as $pdata){
          $placeHtml.='<tr>';
          $placeHtml.='<td>'.$pdata['name'].'</td>';
          $placeHtml.='<td>'.number_format($pdata['liberated_tot']).'</td>';
          $placeHtml.='<td>'.number_format($pdata['registered_tot']).'</td>';
          $placeHtml.='</tr>';
        }
        $placeHtml.='</tbody></table></div>';

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

  echo json_encode(array($broadregions, $broadregionsN, $subregions, $subregionsN, $distinctregions, $regionTotals, $totEnslaved,$sqldistinctregions,$Stats,$data,$entries,$cQuery,$noResults,$regionHtml,$deptsHtml,$distinctregions,$returnedTotal,$totalPages,$Regs,$Regs_Places,$Places,$dr));
?>
