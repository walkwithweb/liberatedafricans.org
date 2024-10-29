<?php
require '../database.php';

$perPage = 10;
if (isset($_POST['page'])) {
	$page  = $_POST['page'];
	$pmsg = "coming from post " . $page;
} else {
	$page=1;
	$pmsg = "nothing passed";
}

if (isset($_POST['cQuery'])){
	$cQuery = $_POST['cQuery'];
	$msg = "Found a cquery " . $cQuery;
} else {
	$cQuery = '';
	$msg = "Didn't find a cquery";
}
$startFrom = ($page-1) * $perPage;
$toRow = ($page * $perPage);
$sqlQuery = "SELECT UI,person.Name as pName,Field7,Field8,Field10,Field2,Field13,personID FROM person  left join CV_Places on (CV_Places.ID = person.Field13) where Field12!='0' and Field12 IS NOT NULL  and `online` = '1' ".$cQuery."  ORDER BY Field2 ASC LIMIT $startFrom, $perPage";

$result = $conn->query($sqlQuery);
$paginationHtml = '';

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	$paginationHtml.='<tr>';
	$paginationHtml.='<td style=white-space:nowrap;>'.$row["UI"].'</td>';
	$name=htmlspecialchars_decode($row["pName"]);
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

$perPage = 10;
$stmt = $conn->prepare("SELECT count(*) FROM person left join CV_Places on (CV_Places.ID = person.Field13) where `online` = '1' " . $cQuery);
$stmt->execute();
$returnedTotal = $stmt->fetchColumn();
$totalPages = ceil($returnedTotal/$perPage);

echo json_encode(array($jsonData, $startFrom+1,$toRow,$returnedTotal, $totalPages, $sqlQuery, $msg));
?>
