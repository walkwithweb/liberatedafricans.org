<?php 
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

?>