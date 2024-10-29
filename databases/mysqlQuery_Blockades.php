<?php
require '../database.php';?>

<?php
if(isset($_POST['perPage'])) {
  $perPage = $_POST['perPage']; 
} else {
  $perPage = 10;
}

$Latitude = array();
$Longitude = array(); 
$Names = array();
$Years = array();
$CaptureLocations = array();
$CaptureVesselNames = array();
$CaptureVesselTypes = array();
$Flags = array();
$Captains = array();
$PersonIDs = array();
$PersonUIs = array();
$LiberatedTot = array();
$ShipStatus = array();

/* Summary Stats */
$Stats = array("cases_tot"=>"0", "enslaved_tot"=>"0", "liberated_tot"=>"0", "courts_tot"=>"0", "registered_tot"=>"0");

/* Queries for Map, Summary and Timeline sections */
$sqlregion = "SELECT person.Field16 as Latitude, person.Field17 as Longitude, Name, Field2, Field15, Field18, Field20, Field19, Field21, Field22, personID, UI, Field9, Field47 from person ";  

/* sql for the summary and timeline sections */
require 'mysqlQuery_Summary.php';

$where = " WHERE Field16 !='' AND Field17 !='' AND Field47 !='0' AND `online` = '1'"; // 16, 17 is x,y coords

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
}

$cQuery = $where;

/* Apply the filter on the different queries */
$sqlregion .= $where . " ORDER BY Field9 DESC";

$query = $conn->query($sqlregion);
while($row = $query->fetch(PDO::FETCH_ASSOC)){
    /* Map */
    $Latitude[]=round(floatval($row['Latitude']),2);
    $Longitude[]=round(floatval($row['Longitude']),2);
    $Names[]=$row['Name'];
    $Years[]=$row['Field2'];
    $CaptureLocations[]=$row['Field15'];
    $CaptureVesselNames[]=$row['Field18'];
    $CaptureVesselTypes[]=$row['Field20'];
    $Flags[]=$row['Field19'];
    if($row['Field21']=='' && $row['Field22']==''){
        $Captains[]='';
    } else if($row['Field21']==''){
        $Captains[]=$row['Field22'];
    } else if($row['Field22']==''){
        $Captains[]=$row['Field21'];
    } else {
        $Captains[]=$row['Field21'] . "," . $row['Field22'];
    }
    $PersonIDs[]=$row['personID'];
    $PersonUIs[]=$row['UI'];
    $LiberatedTot[]=$row['Field9'];
    $ShipStatus[]=$row['Field47'];
}

/* Finish the sql summary queries, Fetch the data */
require 'mysqlQuery_SummaryFetch.php';

if(!$Names){
  // empty array - no results 
  $noResults = true;
}

/* Put together the summary ui */
require 'mysqlQuery_SummaryContent.php';

echo json_encode(array($Latitude,$Longitude,$Names,$Years,$CaptureLocations,$CaptureVesselNames,$CaptureVesselTypes,$Flags,$Captains,$PersonIDs,$PersonUIs,$LiberatedTot,$ShipStatus,$sqlregion,$Stats,$data,$entries,$cQuery,$noResults,$regionHtml,$deptsHtml,$returnedTotal,$totalPages)); 
?>

