<?php
require '../database.php';

$perPage = 10;
$page = 0;
if (isset($_POST['page'])) {
	$page  = $_POST['page'];
} else {
	$page=1;
}
$startFrom = ($page-1) * $perPage;
$toRow = ($page * $perPage);

if(isset($_POST['advsearch']) && $_POST['advsearch']!=''){
	$advsearch = $_POST['advsearch'];
	$isAdvancedSResults = true;
} else {
	$advsearch = '';
	$isAdvancedSResults = false;
}

$data = array();
$entries = array();
$arrIDs = array();

/*SQL - Timeline*/
$sqltimeline="Select Field2 AS Decade, SUM(Field9) AS amt from person where `online` = '1' ";

/*SQL - Summary*/
$sqlstats = "select count(*) as cases_tot, SUM(Field8) AS enslaved_tot, SUM(Field9) AS liberated_tot, count(distinct(Field7)) as courts_tot, SUM(Field10) as registered_tot from person where `online` = '1'";

if($_POST['columnName']=='Field12'){
	
	if(isset($_POST['reg'])){
		$reg = $_POST['reg'];
		if($reg == 1){
			// Broad Regions
			$bid = $_POST['id'];
			$sql = "SELECT * FROM `CV_Places` WHERE Broadregion = " .$bid. "";
  			$query = $conn->query($sql);
			while ($row = $query->fetch(PDO::FETCH_ASSOC)){
				$arrIDs[] = $row['ID'];
			}
			if(isset($_POST['startDate'])){
				$sDate = $_POST['startDate'];
				$eDate = $_POST['EndDate'];
				$sqlQuery = "SELECT UI, Name, Field8, Field10, Field2, Field12, Field7, personID FROM person where Field12 " . " in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
				$sqltimeline .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch;
				$sqlstats .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch;

			} else if(isset($_POST['selectedDropdown'])){
				$sqlQuery = "SELECT UI, Name, Field8, Field10, Field2, Field12, Field7, personID FROM person where Field12 " . " in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']) . ") ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
				$sqltimeline .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']).") ".$advsearch;
				$sqlstats .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']).") ".$advsearch;

			} else {
				$sqlQuery = "SELECT UI, Name, Field8, Field10, Field2, Field12, Field7, personID FROM person where Field12 " . " in (" . implode(",", $arrIDs) .") ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
				$sqltimeline .= " and `Field12` in ("  .implode(",",$arrIDs).") " . $advsearch;
				$sqlstats .= " and `Field12` in ("  .implode(",",$arrIDs).") " . $advsearch;
			}
		} else if ($reg == 2){
			// Sub Regions
			$sid = $_POST['id'];
			$sql = "SELECT * FROM `CV_Places` WHERE Subregion = " .$sid. "";
  			$query = $conn->query($sql);
			while ($row = $query->fetch(PDO::FETCH_ASSOC)){
				$arrIDs[] = $row['ID'];
			}
			if(isset($_POST['startDate'])){
				$sDate = $_POST['startDate'];
				$eDate = $_POST['EndDate'];
				$sqlQuery = "SELECT UI, Name, Field8, Field10, Field2, Field12, Field7, personID FROM person where Field12 " . " in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
				$sqltimeline .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch;
				$sqlstats .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch;
			} else if(isset($_POST['selectedDropdown'])){
				$sqlQuery = "SELECT UI, Name, Field8, Field10, Field2, Field12, Field7, personID FROM person where Field12 " . " in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']) . ") ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
				$sqltimeline .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']).") ".$advsearch;
				$sqlstats .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']).") ".$advsearch;
			} else {
				$sqlQuery = "SELECT UI, Name, Field8, Field10, Field2, Field12, Field7, personID FROM person where Field12 " . " in (" . implode(",", $arrIDs) .") ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
				$sqltimeline .= " and `Field12` in ("  .implode(",",$arrIDs).") " . $advsearch;
				$sqlstats .= " and `Field12` in ("  .implode(",",$arrIDs).") " . $advsearch;
			}
		}
	} else {
     $sqlQuery = "SELECT  UI,Name,Field8,Field10,Field2,Field12,Field7,personID
     FROM person  where Field12='".$_POST['id']."' ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
	 $sqltimeline .= " and `Field12`='".$_POST['id']."' " . $advsearch;
	 $sqlstats .= " and `Field12`='".$_POST['id']."' ". $advsearch;
    }
}else{
 if(isset($_POST['startDate'])){
   $sDate = $_POST['startDate'];
   $eDate = $_POST['EndDate'];
   $sqlQuery = "SELECT UI, Name, Field8, Field10, Field2, Field13, Field7, personID FROM person where Field13 " . " in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
   $sqltimeline .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch;
   $sqlstats .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field2` BETWEEN $sDate AND $eDate ".$advsearch;
 }else if(isset($_POST['selectedDropdown'])){
	 $sqlQuery = "SELECT UI, Name, Field8, Field10, Field2, Field13, Field7, personID FROM person where Field13 " . " in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']) . ") ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
	 $sqltimeline .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']).") ".$advsearch;
	 $sqlstats .= " and Field12 in (" . implode(",", $arrIDs) .") AND `Field7` in (" . implode(",",$_POST['selectedDropdown']).") ".$advsearch;
 }else{
    $sqlQuery = "SELECT  UI,Name,Field8,Field10,Field2,Field12,Field7,personID
     FROM person  where Field13='".$_POST['id']."' ORDER BY Field2 ASC LIMIT $startFrom, $perPage";
	 $sqltimeline .= " and `Field12`='".$_POST['id']."' " . $advsearch;
	 $sqlstats .= " and `Field12`='".$_POST['id']."' ". $advsearch;
 }
}

//$sqltimeline .= " GROUP by FLOOR(Field2/ 10)";
$sqltimeline .= " GROUP by Field2";

$result = $conn->query($sqlQuery);
$paginationHtml = '';

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$paginationHtml.='<tr>';
	$paginationHtml.='<td style=white-space:nowrap;>'.$row["UI"].'</td>';
	$name=htmlspecialchars_decode($row["Name"]);
	$paginationHtml.='<td>'.$name.'</td>';
	$paginationHtml.='<td>'.$row["Field2"].'</td>';
	$sqlport = "SELECT Name FROM  CV_Court_Names WHERE ID=" . $row['Field7'] . " limit 1;";
    $query2 = $conn->query($sqlport);
    $dynamicport = $query2->fetch(PDO::FETCH_ASSOC);
	$paginationHtml.='<td>'.$dynamicport['Name'].'</td>';
	$paginationHtml.='<td>'.$row["Field8"].'</td>';
	$paginationHtml.='<td>'.$row["Field10"].'</td>';
	$personid=$row['personID'];
	$button='<small class="text-muted">
            <a style="color:#AB4503;font-size:18px;" class="category_btn" target="_blank"
             href="event_details.php?EventID='.$personid.'">
            <h6 class="text-center">View Details</h6>
            </a></small>';
	$paginationHtml.='<td>'.$button.'</td>';
	$paginationHtml.='</tr>';
}
$jsonData = array(
	"html"	=> $paginationHtml,
);

$query = $conn->query($sqltimeline);
while($total = $query->fetch(PDO::FETCH_ASSOC)){
	$data[]=$total['Decade'];
	$entries[]=$total['amt'];
}

$query = $conn->query($sqlstats);
$Stats = $query->fetch(PDO::FETCH_ASSOC);

/*SQL - Regions*/ 
$Regs_arr = array();
$Depts_arr = array();
$val = $_POST['id'];

$sqlstatsregions = "SELECT CV_Places.Name as region_name, `Field26` as region, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot FROM `person` LEFT JOIN `CV_Places` ON person.Field26 = CV_Places.ID WHERE `online` = '1' AND `Field12` IN (".implode(',',$arrIDs).") ";

$sqlstatsregions .= $advsearch . " GROUP BY Field26 ORDER BY liberated_tot DESC";
$query = $conn->query($sqlstatsregions);

while($Regs = $query->fetch(PDO::FETCH_ASSOC)){
  $Regs['q'] = $advsearch;

  if(array_key_exists($Regs['region'],$Regs_arr)){
	$tempArr = $Regs_arr[$Regs['region']];
	$tempArr['enslaved_tot'] += $Regs['enslaved_tot'];
	$tempArr['liberated_tot'] += $Regs['liberated_tot'];
	$tempArr['registered_tot'] += $Regs['registered_tot'];
	$tempArr['r'] = $tempArr['r'].','.$val;
	$Regs_arr[$Regs['region']] = $tempArr;
  } else {
	$Regs['r'] = $val;
	$Regs_arr[$Regs['region']] = $Regs;
  }
}

/* Get Data for stats by region - Summary */
if($Regs_arr){
	$regionHtml = '';
	foreach($Regs_arr as $key => $row){
		$region_name = $row['region_name'];
		$region_id = $row['region'];
		$region_query = $row['q'];
		$region_liberated = number_format($row['liberated_tot']);
		$regionHtml .= '<div class="accordion-item">';
		$regionHtml .= '<h2 class="accordion-header" id="heading'.$region_id.'">';
		$regionHtml .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$region_id.'" aria-expanded="true" aria-controls="collapse'.$region_id.'">'.$region_name.' - '.$region_liberated.'</button>';
		$regionHtml .= '</h2>';
		$regionHtml .= '<div id="collapse'.$region_id.'" class="accordion-collapse collapse" aria-labelledby="heading'.$region_id.'" data-bs-parent="#accordionStats">';
		$regionHtml .= '<div class="accordion-body">';
		
		/*SQL - Places*/ 
		$sqlstatsplaces = "SELECT CV_Places.Name as place_name, `Field26` as region, `Field27` as place, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot FROM `person` LEFT JOIN `CV_Places` ON person.Field27 = CV_Places.ID WHERE `online` = '1' AND `Field26` ='".$region_id."' AND `Field12` IN (".implode(',',$arrIDs).") ";
		$sqlstatsplaces .= $region_query . " GROUP BY Field27 ORDER BY liberated_tot DESC";
		$query_p = $conn->query($sqlstatsplaces);
		$placeHtml = '<table class="table table-striped"><thead><tr><th>Place</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
		while($place = $query_p->fetch(PDO::FETCH_ASSOC)){
		$placeHtml.='<tr>';
		$placeHtml.='<td>'.$place['place_name'].'</td>';
		$placeHtml.='<td>'.number_format($place['liberated_tot']).'</td>';
		$placeHtml.='<td>'.number_format($place['registered_tot']).'</td>';
		$placeHtml.='</tr>';
		}
		$placeHtml.='</tbody></table>';

		$regionHtml .= $placeHtml;
		$regionHtml .= '</div></div></div>';
	}
} else {
	$regionHtml = 'No Regions Found';
}

/*SQL - Govt. Department */
$sqlstatsdepts = "SELECT CV_Govt_Departments.Name as govt_dept, `Field6` as govt, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot FROM `person` LEFT JOIN `CV_Govt_Departments` ON person.Field6 = CV_Govt_Departments.ID WHERE `online` = '1' AND `Field12` IN (".implode(',',$arrIDs).") ";
$sqlstatsdepts .= $advsearch . " GROUP BY Field6 ORDER BY liberated_tot DESC";
$query = $conn->query($sqlstatsdepts);

while($Depts = $query->fetch(PDO::FETCH_ASSOC)){
	$Depts['q'] = $advsearch;

	if(array_key_exists($Depts['govt'],$Depts_arr)){
		$tempArr = $Depts_arr[$Depts['govt']];
		$tempArr['enslaved_tot'] += $Depts['enslaved_tot'];
		$tempArr['liberated_tot'] += $Depts['liberated_tot'];
		$tempArr['registered_tot'] += $Depts['registered_tot'];
		$tempArr['r'] = $tempArr['r'].','.$val;
		$Depts_arr[$Depts['govt']] = $tempArr;
	  } else {
		$Depts['r'] = $val;
		$Depts_arr[$Depts['govt']] = $Depts; 
	  }
}

/* Get Data for stats by govt departments - Summary */
if($Depts_arr){
    $deptsHtml = '';
    foreach($Depts_arr as $key => $row){
      $dept_name = $row['govt_dept'];
      $dept_id = $row['govt'];
      $dept_liberated = number_format($row['liberated_tot']);
      $dept_registered = number_format($row['registered_tot']);
      $dept_query = $row['q'];

      $deptsHtml .= '<div class="accordion-item">';
      $deptsHtml .= '<h2 class="accordion-header" id="heading'.$dept_id.'">';
      $deptsHtml .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$dept_id.'" aria-expanded="true" aria-controls="collapse'.$dept_id.'">'.$dept_name.' - '.$dept_liberated.'</button>';
      $deptsHtml .= '</h2>';
      $deptsHtml .= '<div id="collapse'.$dept_id.'" class="accordion-collapse collapse" aria-labelledby="heading'.$dept_id.'" data-bs-parent="#accordionStatsCourts">';
      $deptsHtml .= '<div class="accordion-body">';
      /*SQL - Courts*/ 
      $sqlstatscourts = "SELECT CV_Court_Names.Name as court_name, `Field6` as govt, `Field7` as court, SUM(Field8) as enslaved_tot, SUM(Field9) as liberated_tot, SUM(Field10) as registered_tot FROM `person` LEFT JOIN `CV_Court_Names` ON person.Field7 = CV_Court_Names.ID WHERE `online` = '1' AND `Field6` ='".$dept_id."' AND `Field12` IN (".implode(',',$arrIDs).") ";
      $sqlstatscourts .= $dept_query . " GROUP BY Field7 ORDER BY liberated_tot DESC";
      $query_p = $conn->query($sqlstatscourts);
      $courtHtml = '<table class="table table-striped"><thead><tr><th>Court</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
      while($court = $query_p->fetch(PDO::FETCH_ASSOC)){
        $courtHtml.='<tr>';
        $courtHtml.='<td>'.$court['court_name'].'</td>';
        $courtHtml.='<td>'.number_format($court['liberated_tot']).'</td>';
        $courtHtml.='<td>'.number_format($court['registered_tot']).'</td>';
        $courtHtml.='</tr>';
      }
      $courtHtml.='</tbody></table>';

      $deptsHtml .= $courtHtml;
      $deptsHtml .= '</div></div></div>';
    }
  } else {
    $deptsHtml = 'No Govt. Departments Found';
  }

echo json_encode(array($jsonData, $startFrom+1,$toRow,$data,$entries,$Stats,$regionHtml,$deptsHtml));
?>
