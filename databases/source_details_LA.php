<?php
  require_once("../public/head.php");
  require_once("../database.php");
  error_reporting(0);

  $sql = "SELECT * FROM object  where objectID=" . $_GET['ObjectID'] . " limit 1 ;";
  $query = $conn->query($sql);
  $Source = $query->fetch(PDO::FETCH_ASSOC);
?>
  <body>
    <section>
      <?php require_once("../public/header.php"); ?>
      <div class="container-fluid content">
        <!--Page Title-->
        <div class="row justify-content-center">
            <div id="pageTitleContainer" class="col-11 justify-content-start">
                <h1 class="pageTitle">Court Records</h1>
                <hr class="pageTitleBorder">
            </div>
        </div>
        <!--Start Page Content-->
        <div class="row justify-content-center align-items-center">
            <div id="main-content" class="col-11 mb-5">
                <!--Your code starts here-->
                <div class="row mb-5">
                    <div class="left-column col-lg-12">
                        <div class="event-details">
                        <div class="row" id="desktopview">
                            <div class="col-sm-12">
                            <div class="row">
                                <div class="col-lg-8 pb-4">
                                <iframe src="https://la.regeneratedidentities.org/project/project<?php echo $Source['File'] ?>" style="width:100%; height:100%; padding-top: 1.2rem;" noresize=yes frameborder=0 marginheight=0 marginwidth=0 scrolling="no"></iframe>
                                <?php //echo nl2br($Source['File']); ?>
                                </div>
                                <?php
                                $sql = "SELECT x.* FROM LA_Source_V1 x WHERE indexpage <> 0 ORDER BY indexpage ";
                                $regid_print=0;
                                $query = $conn->query($sql);
                                $ColumnDetails = $query->fetchAll(PDO::FETCH_ASSOC);
                                $total = count($ColumnDetails);
                                $avg = round(count($ColumnDetails) / 2);
                                $fix = 12;
                                $next = $fix + (round(($total - $fix) / 2));
                                foreach ($ColumnDetails as $key => $value) {
                                    if ($key == 0) { ?>
                                    <div class="col-lg-4 pt-3 table-responsive">
                                        <table class="table table-striped table-hover">
                                            <tbody>
                                            <?php
                                            } elseif ($key == $fix || $key ==  $next) { ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php if ($key == $fix) { ?>
                                    </div>
                                    <div class="row">
                                    <?php } ?>
                                    <div class="col-lg-6 table-responsive">
                                        <table class="table table-striped table-hover">
                                        <tbody>
                                            <?php }     
                                            if($regid_print==0){
                                                echo "<tr><th scope='row'>UID</th><td> " .$Source['UI'] . "</td> </tr>";
                                                $regid_print=$regid_print+1;
                                            }
                                            if ($value['FieldType'] == 'dropdown-CV') {
                                                if($Source[$value['ColumnName']]=='0'){
                                                    echo "<tr><th scope='row'>" . $value['display'] . " </th><td></td> </tr>";
                                                } else {
                                                    $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $Source[$value['ColumnName']] . " limit 1;";
                                                    $query = $conn->query($sql);
                                                    $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                    echo "<tr><th scope='row'>" . $value['display'] . " </th><td class=\"wrap\"> " . $dynamic['Name'] . "</td> </tr>";
                                                }
                                            } else if($value['FieldType']=="dropdown-CV-multi"){
                                                $display_terms= "";
                                                $selectedoptions=$Source[$value['ColumnName']];

                                                if($selectedoptions=='0'){
                                                    echo "<tr><th scope='row'>" . $value['display'] . " </th><td></td> </tr>";
                                                } else {
                                
                                                    //Loading Value for Key in Events Table
                                                    $selectedoptions_Array = explode(';', $selectedoptions);

                                                    foreach($selectedoptions_Array as $opt_selected){
                                                    // Loading Controlled Vocaublary
                                                        $q1="SELECT `ID`,`Name` FROM `".$value['Options']."` WHERE `ID` LIKE '".$opt_selected."'";
                                                        $query_CL = $conn->query($q1);
                                                        $selected_term_data = $query_CL->fetch(PDO::FETCH_ASSOC);

                                                        if($selected_term_data){
                                                            $display_terms = $display_terms.$selected_term_data['Name']."<br>";
                                                        }
                                                    }?>
                                                    <tr><th scope='row'><?php echo  $value['display'];?> </th><td class="wrap"> <?php echo rtrim($display_terms,"<br>");?></td> </tr>
                            
                                            <?php 
                                                }
                                            }else if($value['FieldType']=='dropdown' & $value['Options']=='Researcher' & $Source[$value['ColumnName']]!='Not Known'){
                                                $sql = "SELECT * FROM users WHERE `email` LIKE '".$Source[$value['ColumnName']]."' limit 1";
                                                $querydropdown = $conn->query($sql);
                                                $dropdown = $querydropdown->fetch(PDO::FETCH_ASSOC);
                                                $name=$dropdown['fname']." ".$dropdown['lname'];
                                                if($Source[$value['ColumnName']]=='0'){
                                                    echo "<tr><th scope='row'>" . ucfirst($value['display']) . " </th><td>Unknown</td>"; 
                                                }else{
                                                    echo "<tr><th scope='row'>" . ucfirst($value['display']) . " </th><td class=\"wrap\">" . ucfirst($name) . "</td>";
                                                    
                                                }
                                                }else {
                                                if (isset($Source[$value['ColumnName']])) {?>
                                                    <tr><th scope='row'><?php echo $value['display'];?> </th><td class="wrap"><?php if($Source[$value['ColumnName']]!='0'){ echo $Source[$value['ColumnName']];}else{echo "";}?></td> </tr>
                                                <?php }
                                                }
                                                }?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="row" id="mobileview">
                            <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 pt-4 pb-4">
                                <iframe src="https://la.regeneratedidentities.org/project/project<?php echo $Source['File'] ?>" style="width:100%; height:100%; padding-top: 1.2rem;" noresize=yes frameborder=0 marginheight=0 marginwidth=0 scrolling="no"></iframe>
                                <?php //echo nl2br($Source['File']); ?>
                                </div>
                                <?php
                                $sql = "SELECT x.* FROM LA_Source_V1 x WHERE indexpage <> 0 ORDER BY indexpage ";
                                $regid_print=0;
                                $query = $conn->query($sql);
                                $ColumnDetails = $query->fetchAll(PDO::FETCH_ASSOC);
                                $total = count($ColumnDetails);
                                $avg = round(count($ColumnDetails) / 2);
                                $fix = 12;
                                $next = $fix + (round(($total - $fix) / 2));
                                foreach ($ColumnDetails as $key => $value) {
                                    if ($key == 0) { ?>
                                    
                                    <?php if ($key == $fix) { ?>
                                    </div>
                                    <div class="row">
                                    <?php } ?>
                                    <div class="col-md-12 col-sm-12 table-responsive">
                                        <table class="table table-striped">
                                        <tbody>
                                            <?php }     
                                            if($regid_print==0){
                                                echo "<tr><th scope='row'>UID</th><td> " .$Source['UI'] . "</td> </tr>";
                                                $regid_print=$regid_print+1;
                                            }
                                            if ($value['FieldType'] == 'dropdown-CV') {
                                                if($Source[$value['ColumnName']]=='0'){
                                                    echo "<tr><th scope='row'>" . $value['display'] . " </th><td></td> </tr>";
                                                } else {
                                                    $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $Source[$value['ColumnName']] . " limit 1;";
                                                    $query = $conn->query($sql);
                                                    $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                    echo "<tr><th scope='row'>" . $value['display'] . " </th><td class=\"wrap\"> " . $dynamic['Name'] . "</td> </tr>";
                                                }
                                            } else if($value['FieldType']=="dropdown-CV-multi"){
                                                $display_terms= "";
                                                $selectedoptions=$Source[$value['ColumnName']];

                                                if($selectedoptions=='0'){
                                                    echo "<tr><th scope='row'>" . $value['display'] . " </th><td></td> </tr>";
                                                } else {
                                
                                                    //Loading Value for Key in Events Table
                                                    $selectedoptions_Array = explode(';', $selectedoptions);

                                                    foreach($selectedoptions_Array as $opt_selected){
                                                    // Loading Controlled Vocaublary
                                                        $q1="SELECT `ID`,`Name` FROM `".$value['Options']."` WHERE `ID` LIKE '".$opt_selected."'";
                                                        $query_CL = $conn->query($q1);
                                                        $selected_term_data = $query_CL->fetch(PDO::FETCH_ASSOC);

                                                        if($selected_term_data){
                                                            $display_terms = $display_terms.$selected_term_data['Name']."<br>";
                                                        }
                                                    }?>
                                                    <tr><th scope='row'><?php echo  $value['display'];?> </th><td class="wrap"> <?php echo rtrim($display_terms,"<br>");?></td> </tr>
                            
                                            <?php 
                                                }
                                            }else if($value['FieldType']=='dropdown' & $value['Options']=='Researcher' & $Source[$value['ColumnName']]!='Not Known'){
                                                $sql = "SELECT * FROM users WHERE `email` LIKE '".$Source[$value['ColumnName']]."' limit 1";
                                                $querydropdown = $conn->query($sql);
                                                $dropdown = $querydropdown->fetch(PDO::FETCH_ASSOC);
                                                $name=$dropdown['fname']." ".$dropdown['lname'];
                                                if($Source[$value['ColumnName']]=='0'){
                                                    echo "<tr><th scope='row'>" . ucfirst($value['display']) . " </th><td>Unknown</td>"; 
                                                }else{
                                                    echo "<tr><th scope='row'>" . ucfirst($value['display']) . " </th><td class=\"wrap\">" . ucfirst($name) . "</td>";
                                                    
                                                }
                                                }else {
                                                if (isset($Source[$value['ColumnName']])) {
                                                    echo "<tr><th scope='row'>" . $value['display'] . " </th><td class=\"wrap\">"  . $Source[$value['ColumnName']] . "</td> </tr>";
                                                }
                                                }
                                                }?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                <div class="row mb-5">
                    <div class="col-12 table-reponsive">
                    <?php 
                    /* Get the people */
                    /*
                    $AL_People = array();
                    $q_AL_People = "SELECT person.personID, person.Name as Name, person.Field1 as Title, person.Field2 as Nationality, person.Field3 as Language FROM person, objects_person WHERE person.personID = objects_person.personID AND objects_person.objectID ='".$_GET['ObjectID'] . "' GROUP BY personID";
                    $query_AL_People = $conn->query($q_AL_People);
                    while($row = $query_AL_People->fetch(PDO::FETCH_ASSOC)){
                        $id = $row['personID'];
                        $AL_People[$id] = $row;
                    }
                    if(count($AL_People)>0){?>
                        <h4>Signee(s)</h4>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Title</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($AL_People as $person): ?>
                                    <tr>
                                        <td><?php echo $person['Name'];?></td>
                                        <td><?php echo $person['Title'];?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php 
                    } */ ?>
                    
                    </div>
                </div>
                <!--Your code ends here-->
            </div>
        </div>
        <!-- End Page Content-->
      </div>
      <div class="">
        <?php require_once("../public/footer.php"); ?>
      </div>
    </section>
  </body>
</html>