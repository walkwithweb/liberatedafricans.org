<?php
$rowsCVMulti = array();
if(isset($_GET['ad_search']) && $_GET['ad_search']=='Y'){
    $isAdvancedSResults = true;
    // Handle the query 
    $lnCNO = 0; // initialize
    $originalQuery = "";
    $asreturnedTotal = 0;
    $row_op = array();
    $row_si = array();
    $row_st = array();
    $row_cnd = array();
    $cnamesOnly = array();
    $cdefinitions = array();

    // Put together the query 
    $originalQuery = $_SERVER['QUERY_STRING'];
    $ad_query = explode('&', $_SERVER['QUERY_STRING']);
    $params = array();

    foreach( $ad_query as $param )
    {
      // prevent notice on explode() if $param has no '='
      if (strpos($param, '=') === false) $param += '=';

      list($name, $value) = explode('=', $param, 2);
      $params[urldecode($name)][] = urldecode($value);
    }

    //Get the params for the query - four categories of each query row
    $row_op = $params['row_op'];
    $row_si = $params['row_si'];
    $row_st = $params['row_st'];
    $row_cnd = $params['row_cnd'];

    //Put together the query
    $rcnt = count($row_op);
    $advanced_query = "";
    for($i=0; $i<$rcnt; $i++):
      $op = $row_op[$i];
      $si = $row_si[$i];
      $scnd = $row_cnd[$i];
      $st = $row_st[$i];

      $isStNull = false;
      $isStCVMulti = false;
      $cv_flag = 0;

      // Check if the search text is an int
      $isInt = false;
      $isEng = false;
      $hasSpecialChar = false;
      $isSpecialUI = false;

      if($st==""){
        $isStNull = true;
      } else {
        // Handle single quotes in search term
        $st = str_replace("'", "''",$st); 
        // Is CV Multi 
        if(in_array($si,$rowsCVMulti)){
          $isStCVMulti = true;
          $st .= ';';
        }
        // Check for ints
        if(is_numeric($st)){
          $isInt = true;
        }
        // Check if is english no accents
        if(strlen($st) != strlen(utf8_decode($st))){
          $isEng = false;
        } else {
          $isEng = true;
        }
        // Check if string has special characters
        if((strpos($st,',')!==false) || (strpos($st,'-')!==false)){
          $hasSpecialChar = true;
        }
        // Check if is UI or special number
        $numbers = array('0','1','2','3','4','5','6','7','8','9');
        $qString = $st;
        for($n=0; $n<=strlen($qString)-1;$n++){
          if(in_array($qString[$n],$numbers)){
            $isSpecialUI = true;
            break;
          }
        }
      }

      // Check the conditions
      if($scnd=="1") {
        // equals
        $qCond = "='".$st."'";
      } else if($scnd=="2") {
        // not equals
        $qCond = "<>'".$st."'";
      } else if($scnd=="3") {
        // begins with
        $qCond = " LIKE '".$st."%'";
      } else if($scnd=="4") {
        // does not begins with
        $qCond = " NOT LIKE '".$st."%'";
      } else if($scnd=="5") {
        // ends with
        $qCond = " LIKE '%".$st."'";
      } else if($scnd=="6") {
        // contains
        if($isStCVMulti){
          $qCond = "(".$si." LIKE '".$st."' or ".$si." LIKE '".$st."%' or ".$si." LIKE '%;".$st."' or ".$si." LIKE '%;".$st."%')";
          $cv_flag = 1;
        } else {
          $qCond = " LIKE '%".$st."%'";
        }
      } else if($scnd=="7") {
        // does not contain
        if($isStCVMulti){
          $qCond = "(".$si." NOT LIKE '".$st."' or ".$si." NOT LIKE '".$st."%' or ".$si." NOT LIKE '%;".$st."' or ".$si." NOT LIKE '%;".$st."%')";
          $cv_flag = 1;
        } else {
          $qCond = " NOT LIKE '%".$st."%'";
        }
      } else if($scnd=="8") {
        // is blank
        $qCond = " IS NULL OR person.".$si."=''";
      } else if($scnd=="9") {
        // is not blank
        $qCond = " IS NOT NULL AND person.".$si."<>''";
      } else if($scnd=="10") {
        // sounds like
        if(!$isInt && !$hasSpecialChar && !$isSpecialUI){
          $qCond = " SOUNDS LIKE '".$st."'";
        } else {
          $qCond = "=".$st;
        }
      }

        $prefix = "";
        if($i>0){
        if($op == "AND"){
            if($isStNull){
            $prefix = " AND person." . $si . " IS NULL ";
            } else {
            if($cv_flag==0){
                $prefix = " AND (person." . $si . " " . $qCond . ")";
            } else {
                $prefix = " AND " . $qCond;
            }
            }
        } else if($op == "NOT"){
            if($isStNull){
            $prefix = " AND person." . $si . " IS NOT NULL ";
            } else {
            if($cv_flag==0){
                $prefix = " NOT (person." . $si . " " . $qCond . ")";
            } else {
                $prefix = " NOT " . $qCond;
            }
            
            }
        } else if($op == "OR"){
            if($isStNull){
            $prefix = " OR person." . $si . " IS NULL ";
            } else {
            if($cv_flag==0){
                $prefix = " OR (person." . $si . " " . $qCond . ")";
            } else {
                $prefix = " OR " . $qCond;
            }
            }
        }
        } else {
        if($isStNull){
            if($scnd=8 || $scnd==9){
            $prefix = "person." . $si . " " . $qCond;
            } else {
            $prefix = "person." . $si . " IS NULL";
            }
        } else {
            if($cv_flag==0){
            $prefix = "person." . $si . " " . $qCond;
            } else {
            $prefix = $qCond;
            }
        }
        }
      $advanced_query = $advanced_query . $prefix;
    endfor;
    $advanced_query = "AND " . $advanced_query;
  } else {
    $isAdvancedSResults = false;
  }
  ?>