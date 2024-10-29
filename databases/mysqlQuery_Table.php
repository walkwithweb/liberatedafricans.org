<?php
require '../database.php';?>

<?php
if(isset($_POST['perPage'])) {
    $perPage = $_POST['perPage']; 
} else {
    $perPage = 10;
}

if (isset($_POST['page'])) {
	$page  = $_POST['page'];
} else {
	$page = 1;
}

if (isset($_POST['cQuery'])) {
    $cQuery = $_POST['cQuery'];
} else {
    if(isset($_POST['key'])){
        if($_POST['key']=="cases"){
            $cQuery = "WHERE person.Field27!='0' AND `online` = '1'";
        } else if($_POST['key']=="blockades") {
            $cQuery = "WHERE person.Field16!='' AND person.Field17!='' AND person.Field47 !='0' AND `online` = '1'";
        } else if($_POST['key']=="departures") {
            $cQuery = "WHERE person.Field12!='' AND person.Field12!='0' AND `online` = '1'";
        }
    } else {
        $cQuery = " AND `online` = '1'";
    }
}

if (isset($_POST['returnedTotal'])) {
    $returnedTotal = $_POST['returnedTotal'];
} else {
    $returnedTotal = 1;
}

if (isset($_POST['totalPages'])) {
    $totalPages = $_POST['totalPages'];
} else {
    $totalPages = 1;
}

$startFrom = ($page-1) * $perPage;
$toRow = ($page * $perPage);

if($_POST['key']=="departures"){
    $sqltable = "SELECT UI, person.Name as pName, Field2, CV_Court_Names.Name, Field9, Field10, personID, CV_Places.Subregion FROM person left join CV_Court_Names on (CV_Court_Names.ID = person.Field7) LEFT JOIN CV_Places on (CV_Places.ID = person.Field12) ";
} else {
    $sqltable = "SELECT UI, person.Name as pName, Field2, CV_Court_Names.Name, Field9, Field10, personID FROM person left join CV_Court_Names on (CV_Court_Names.ID = person.Field7) ";
}

$where = $cQuery;

$sqltable .= $where . " ORDER BY Field2 ASC LIMIT " . $startFrom . "," . $perPage;

$query = $conn->query($sqltable);
$paginationHtml = '';
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Table */
    $paginationHtml.='<tr>';
	$paginationHtml.='<td style=white-space:nowrap;>'.$row["UI"].'</td>';
	$name=htmlspecialchars_decode($row["pName"]);
	$paginationHtml.='<td>'.$name.'</td>';
	$paginationHtml.='<td>'.$row["Field2"].'</td>';
	$paginationHtml.='<td>'.$row['Name'].'</td>';
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

echo json_encode(array($paginationHtml, $startFrom+1,$toRow, $returnedTotal, $totalPages, $sqltable));
?>