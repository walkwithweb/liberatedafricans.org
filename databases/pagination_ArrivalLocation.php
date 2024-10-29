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

/*SQL - Timeline*/
$sqltimeline="Select Field2 AS Decade, SUM(Field9) AS amt from person where `online` = '1'";

/*SQL - Summary*/
$sqlstats = "select count(*) as cases_tot, SUM(Field8) AS enslaved_tot, SUM(Field9) AS liberated_tot, count(distinct(Field7)) as courts_tot from person where `online` = '1'";

if($_POST['columnName']=='Field26'){
	if(isset($_POST['startDate'])){
		$sqlQuery = "SELECT  UI,Name,Field9,Field10,Field2,Field26,Field7,personID
		FROM person  where Field26='".$_POST['id']."' and Field2 BETWEEN '".$_POST['startDate']."'  AND '".$_POST['EndDate']."' ".$advsearch."  ORDER BY Field2 ASC LIMIT $startFrom, $perPage";

		$sqltimeline .= " and Field26='".$_POST['id']."' and Field2 BETWEEN '".$_POST['startDate']."' AND '".$_POST['EndDate']."' ".$advsearch; 
		$sqlstats .= " and Field26='".$_POST['id']."' and Field2 BETWEEN '".$_POST['startDate']."' AND '".$_POST['EndDate']."' ".$advsearch;
    } else if(isset($_POST['selectedDropdown'])){
		$sqlQuery = "SELECT  UI,Name,Field9,Field10,Field2,Field26,Field7,personID
		FROM person  where Field26='".$_POST['id']."' and Field7 in (" . implode(",", $_POST['selectedDropdown']) . ") ".$sdvsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";

		$sqltimeline .= " and Field26='".$_POST['id']."' and Field7 in (" . implode(",", $_POST['selectedDropdown']) . ") ".$sdvsearch;
		$sqlstats .= " and Field26='".$_POST['id']."' and Field7 in (" . implode(",", $_POST['selectedDropdown']) . ") ".$sdvsearch;
    } else {
		$sqlQuery = "SELECT  UI,Name,Field9,Field10,Field2,Field26,Field7,personID
		FROM person  where Field26='".$_POST['id']."' ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";

		$sqltimeline .= " and Field26='".$_POST['id']."' ".$advsearch;
		$sqlstats .= " and Field26='".$_POST['id']."' ".$advsearch;
    }
} else {
	if(isset($_POST['startDate'])){
		$sqlQuery = "SELECT  UI,Name,Field9,Field10,Field2,Field27,Field7,personID
		FROM person  where Field27='".$_POST['id']."' and Field2 BETWEEN '".$_POST['startDate']."'  AND '".$_POST['EndDate']."' ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";

		$sqltimeline .= " and Field27='".$_POST['id']."' and Field2 BETWEEN '".$_POST['startDate']."'  AND '".$_POST['EndDate']."' ".$advsearch;
		$sqlstats .= " and Field27='".$_POST['id']."' and Field2 BETWEEN '".$_POST['startDate']."'  AND '".$_POST['EndDate']."' ".$advsearch;
	} else if(isset($_POST['selectedDropdown'])){
		$sqlQuery = "SELECT  UI,Name,Field9,Field10,Field2,Field27,Field7,personID
		FROM person  where Field27='".$_POST['id']."' and Field7 in (" . implode(",", $_POST['selectedDropdown']) . ") ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";

		$sqltimeline .= " and Field27='".$_POST['id']."' and Field7 in (" . implode(",", $_POST['selectedDropdown']) . ") ".$advsearch;
		$sqlstats .= " and Field27='".$_POST['id']."' and Field7 in (" . implode(",", $_POST['selectedDropdown']) . ") ".$advsearch;
	} else {
		$sqlQuery = "SELECT  UI,Name,Field9,Field10,Field2,Field27,Field7,personID FROM person  where Field27='".$_POST['id']."' ".$advsearch." ORDER BY Field2 ASC LIMIT $startFrom, $perPage";

		$sqltimeline .= " and Field27='".$_POST['id']."' ".$advsearch;
		$sqlstats .= " and Field27='".$_POST['id']."' ".$advsearch;
	}
}

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
	$paginationHtml.='<td>'.$row["Field9"].'</td>';
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

echo json_encode(array($jsonData,$startFrom+1,$toRow,$data,$entries,$Stats));
?>
