<?php
    if (!isset($_GET['EventUI']) || empty($_GET['EventUI']) ) {
        if(!isset($_GET['EventID']) || empty($_GET['EventID'])) {
            header("Location: ./cases.php");
            die;
        }
    }
    require_once("../public/head.php");
    require_once("../database.php");
?>
  <body class="home">
    <section id="courts-cases" class="main">
      <?php require_once("../public/header.php"); ?>
      <div class="container-fluid content courtcases">
        <!--Page Title-->
        <div class="row justify-content-center">
            <div id="pageTitleContainer" class="col-11 justify-content-start">
                <div class="row">
                    <div id="leftTitle" class="col-xxl-5 d-flex">
                        <h1 class="pageTitle">Courts & Cases</h1>
                    </div>
                    <div class="col-xxl-7 d-flex btnContainer pr-2">
                        <ul class="nav">
                            <li class="nav-item">
                                <a href="cases-departures.php" class="btn">DEPARTURES</a>
                            </li>
                            <li class="nav-item">
                                <a href="cases-blockades.php" class="btn">BLOCKADES</a>
                            </li>
                            <li class="nav-item">
                                <a href="cases.php" class="btn activeNav">LIBERATED AFRICANS</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <hr class="pageTitleBorder">
            </div>
        </div>
        <!--Start Page Content-->
        <div class="row justify-content-center align-items-center">
            <div id="main-content" class="col-11 mb-5" style="padding-left:0;padding-right:0;">
            <?php
            if (isset($_GET['EventUI']) && !empty($_GET['EventUI'])) {
                $sql = "SELECT `personID` FROM `person` where `UI`=" . $_GET['EventUI'] . " limit 1";
                $query = $conn->query($sql);
                $eventData = $query->fetch(PDO::FETCH_ASSOC);
                $eventID = $eventData['personID'];
            } else {
                $eventID = $_GET['EventID'];
            }

            $sql = "SELECT * FROM person where personID=" . $eventID . " limit 1";
            $query = $conn->query($sql);
            $event = $query->fetch(PDO::FETCH_ASSOC);
            $courtName = "SELECT Name FROM  CV_Court_Names WHERE ID=" . $event['Field7'] . " limit 1";
            $query2 = $conn->query($courtName);
            $cName = $query2->fetch(PDO::FETCH_ASSOC);
            ?>


            <div class="container-fluid" id="body-container">
                <div class="row bg-white" style="padding-top:32px;">
                    <div class="row justify-content-center mt-2 mb-4">
                        <div class="col-12 col-md-11">
                            <h1><?php echo $event['Name'] ?></h1>
                            <p>In <?php echo $event['Field2']; ?>, <?php echo $event['Field9']; ?> enslaved African(s) were “liberated” in a  state-run scheme usually resulting in involuntary indentures, conscription, or re-enslavement. Under the jurisdiction of <?php echo $event['Field6']; ?>, Case ID <?php echo $event['UI']; ?>  occurred via the <?php echo $cName['Name']; ?>.</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <ul id="caseTabs" class="nav nav-tabs justify-content-center" role="tablist">
                            <?php
                            $sql = "SELECT distinct(SectionDisplay) FROM LA_CourtForm_V1 WHERE `SectionDisplay` != 'G Drive Loaded Data' ";
                            $query = $conn->query($sql);
                            $eresults = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($eresults as $key => $value){
                                if ($key == 0) { ?>
                                <li class="nav-item" role="presentation">
                                    <a id="<?php echo str_replace(' ', '', $value['SectionDisplay']);?>-tab" class="nav-link active show" data-bs-toggle="tab" data-bs-target="#<?php echo str_replace(' ', '', $value['SectionDisplay']);?>" type="button" role="tab" aria-controls="" aria-selected="true"><?php echo $value['SectionDisplay']; ?></a>
                                </li>
                                <?php } else { ?>
                                <?php if($value['SectionDisplay']!='Additional Details'):?>
                                    <li class="nav-item" role="presentation">
                                    <a id="<?php echo str_replace(' ', '', $value['SectionDisplay']);?>-tab"  class="nav-link" data-bs-toggle="tab" data-bs-target="#<?php echo str_replace(' ', '', $value['SectionDisplay']); ?>" type="button" role="tab" aria-controls="" aria-selected="true"><?php echo $value['SectionDisplay'];?></a>
                                    </li>
                                <?php endif; ?>
                                <?php }
                            }
                            ?>
                            <!--Add People List--> 
                            <li class="nav-item" role="presentation">
                                <a id="<?php echo 'PeopleDetails-tab'; ?>"  class="nav-link" data-bs-toggle="tab" data-bs-target="#PeopleDetails" type="button" role="tab" aria-controls="" aria-selected="true">People Details</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="caseTabsRow" class="row bg-white justify-content-center">
                    <div class="col-md-10">
                        <div class="card shadow-sm p-3 mb-5 bg-white rounded tab-content" id="caseTabsContent">
                            <?php
                            $captainLabel = "Captain";
                            $captainName = "";
                            $captainGivenName = "Field22"; //indexpage
                            $captainSurname = "Field21"; //indexpage
                            foreach ($eresults as $key => $value){
                                if($key == 0) { ?>
                                <!--Key = 0 Deals with first tab - case details-->
                                <div id="<?php echo str_replace(' ', '', $value['SectionDisplay']);?>" class="tab-pane fade in show active" role="tabpanel" aria-labelledby="<?php echo str_replace(' ', '', $value['SectionDisplay']);?>-tab">
                                    <h5 class="card-header category_btn"><?php echo $value['SectionDisplay'];?></h5>
                                    <?php
                                    $sql = "SELECT * FROM LA_CourtForm_V1 WHERE `SectionDisplay` = '".$value['SectionDisplay']."' AND status = 1 AND indexpage != '0' ORDER BY indexpage ";
                                    $query = $conn->query($sql);
                                    $ColumnDetails = $query->fetchAll(PDO::FETCH_ASSOC);?>
                                    <div class="card-body">
                                        <div class="table-responsive-md">
                                            <table class="table table-striped">
                                                <tbody>
                                                <?php foreach ($ColumnDetails as $key => $value) {
                                                    if ($value['FieldType'] == 'dropdown-CV') {
                                                    $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $event[$value['ColumnName']] . " limit 1;";
                                                    $query = $conn->query($sql);
                                                    $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                    echo "<tr><th scope='row'>" . $value['display'] . " </th><td> " . $dynamic['Name'] . "</td> </tr>";
                                                    } elseif ($value['ColumnName']==$captainSurname) {
                                                    $captainName = $event[$value['ColumnName']];
                                                    } elseif ($value['ColumnName']==$captainGivenName) {
                                                    $captainName = $event[$value['ColumnName']] . " " . $captainName;
                                                    echo "<tr><th scope='row'>" . $captainLabel . " </th><td>"  . $captainName . "</td></tr>";
                                                    } elseif ($value['display'] == 'Sources') {
                                                    $HasSources = true;
                                                    if (isset($event[$value['ColumnName']])) {
                                                        echo "<tr><th scope='row'>" . $value['display'] . " </th><td>"  . $event[$value['ColumnName']] . "</td> </tr>";
                                                    }
                                                    } elseif ($value['display'] == 'Cite as') {
                                                        echo "<tr><th scope='row'>Cite as: </th><td>"  . "<p>Lovejoy, H. B. (2024). Conceptualizing ‘Liberated Africans’ and Slave Trade Abolition: Government Schemes to Indenture Enslaved People Captured from Slavery, 1800–1920. Past & Present, 1-69. <a href=\"https://doi.org/10.1093/pastj/gtae019\" target=\"_blank\">https://doi.org/10.1093/pastj/gtae019</a></p>" . "<p>Chadha, K. (2024). Regenerated Identities: A Collaborative Web-based Content Management System for Digital Humanities. International Journal of Computer Applications, 186(29), 28–33. <a href=\"https://ijcaonline.org/archives/volume186/number29/regenerated-identities-a-collaborative-web-based-content-management-system-for-digital-humanities/\" target=\"_blank\">https://ijcaonline.org/archives/volume186/number29/regenerated-identities-a-collaborative-web-based-content-management-system-for-digital-humanities/</a></p>" . "</td></tr>";
                                                    } else {
                                                        if($value['display'] == 'Case ID') {
                                                            // Don't print the Case ID use RegID instead
                                                            echo "<tr><th scope='row'>UID </th><td> " . $event['UI'] . "</td> </tr>";
                                                        } else {
                                                            if (isset($event[$value['ColumnName']])) {
                                                            echo "<tr><th scope='row'>" . $value['display'] . " </th><td>"  . $event[$value['ColumnName']] . "</td> </tr>";
                                                            }
                                                        }
                                                    }
                                                } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <?php } else { ?>
                                <?php if($value['SectionDisplay']!='Additional Details'): ?>
                                    <div id="<?php echo str_replace(' ', '', $value['SectionDisplay']);?>" class="tab-pane fade" role="tabpanel" aria-labelledby="<?php echo str_replace(' ', '', $value['SectionDisplay']);?>-tab">
                                    <h5 class="card-header category_btn"><?php echo $value['SectionDisplay'];?></h5>
                                    <?php
                                    $isEventDetails = false;
                                    if($value['SectionDisplay']=='Event Details'){
                                        $isEventDetails = true;
                                    }
                                    $sql = "SELECT * FROM LA_CourtForm_V1 WHERE `SectionDisplay` = '".$value['SectionDisplay']."' AND status = 1 AND indexpage != '0' ORDER BY indexpage ";
                                    $query = $conn->query($sql);
                                    $ColumnDetails = $query->fetchAll(PDO::FETCH_ASSOC);?>
                                    <div class="card-body">
                                        <div class="table-responsive-md">
                                            <table class="table table-striped">
                                                <tbody>
                                                <?php echo "<tr><th scope='row' class='e-details'>UID</th><td> " . $event['UI'] . "</td></tr>";?>
                                                <?php echo "<tr><th scope='row' class='e-details'>Case Name</th><td> " . $event['Name'] . "</td></tr>";?>
                                                <?php //echo "<tr><th scope='row' colspan='2'>Departure from Africa</th></tr>";?>
                                                <?php foreach ($ColumnDetails as $key => $value) {
                                                    if(($value['indexpage'] == '1')){
                                                        echo '</tbody>';
                                                        echo '</table>';
                                                        echo '<h6 class="mt-5 mb-3 t-section">Departure from Africa</h6>';
                                                        echo '<table class="table table-striped">';
                                                        echo '<tbody>';
                                                    } else if(($value['indexpage'] == '4')){
                                                        echo "<tr><th scope='row' class='e-details'>Enslaved Total</th><td> " . $event['Field8'] . "</td></tr>";
                                                        echo '</tbody>';
                                                        echo '</table>';
                                                        echo '<h6 class="mt-5 mb-3 t-section">Capture</h6>';
                                                        echo '<table class="table table-striped">';
                                                        echo '<tbody>';
                                                    } else if(($value['indexpage'] == '11')){
                                                        echo '</tbody>';
                                                        echo '</table>';
                                                        echo '<h6 class="mt-5 mb-3 t-section">Trial</h6>';
                                                        echo '<table class="table table-striped">';
                                                        echo '<tbody>';
                                                    }
                                                if ($value['FieldType'] == 'dropdown-CV') {
                                                    if($event[$value['ColumnName']]==''){
                                                    $event[$value['ColumnName']]=0; }
                                                    $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $event[$value['ColumnName']] . " limit 1;";
                                                    $query = $conn->query($sql);
                                                    $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                    if($dynamic){
                                                        $dname = $dynamic['Name'];
                                                    } else {
                                                        $dname = '';
                                                    }
                                                    echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td> " . $dname . "</td> </tr>";
                                                } elseif ($value['ColumnName']==$captainSurname) {
                                                    $captainName = $event[$value['ColumnName']];
                                                } elseif ($value['ColumnName']==$captainGivenName) {
                                                    $captainName = $event[$value['ColumnName']] . " " . $captainName;
                                                    echo "<tr><th scope='row' class='e-details'>" . $captainLabel . " </th><td>"  . $captainName . "</td></tr>";
                                                } elseif ($value['display'] == 'Sources') {
                                                    $HasSources = true;
                                                    if (isset($event[$value['ColumnName']])) {
                                                    echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td>"  . $event[$value['ColumnName']] . "</td> </tr>";
                                                    }
                                                } elseif ($value['display'] == 'Citation') {
                                                    echo "<tr><th scope='row'>Cite as: </th><td>"  . "<p>Lovejoy, H. B. (2024). Conceptualizing ‘Liberated Africans’ and Slave Trade Abolition: Government Schemes to Indenture Enslaved People Captured from Slavery, 1800–1920. Past & Present, 1-69.<a href=\"https://doi.org/10.1093/pastj/gtae019\" target=\"_blank\">https://doi.org/10.1093/pastj/gtae019</a></p>" . "<p>Chadha, K. (2024). Regenerated Identities: A Collaborative Web-based Content Management System for Digital Humanities. International Journal of Computer Applications, 186(29), 28–33. <a href=\"https://ijcaonline.org/archives/volume186/number29/regenerated-identities-a-collaborative-web-based-content-management-system-for-digital-humanities/\" target=\"_blank\">https://ijcaonline.org/archives/volume186/number29/regenerated-identities-a-collaborative-web-based-content-management-system-for-digital-humanities/</a></p>" . "</td></tr>";
                                                } else {
                                                    if (isset($event[$value['ColumnName']])) {
                                                        echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td>"  . $event[$value['ColumnName']] . "</td> </tr>";
                                                        if($value['ColumnName']=='Field29') {
                                                            // Get the govt. dept
                                                            $sql = "SELECT x.* FROM CV_Govt_Departments x WHERE ID=" . $event['Field6'] . " limit 1;";
                                                            $query = $conn->query($sql);
                                                            $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                            echo "<tr><th scope='row' class='e-details'>Government Department</th><td> " . $dynamic['Name'] . "</td></tr>";
                                                            // Get the court
                                                            $sql = "SELECT x.* FROM CV_Court_Names x WHERE ID=" . $event['Field7'] . " limit 1;";
                                                            $query = $conn->query($sql);
                                                            $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                            echo "<tr><th scope='row' class='e-details'>Court </th><td> " . $dynamic['Name'] . "</td> </tr>";
                                                            // Trial outcome
                                                            echo "<tr><th scope='row' class='e-details'>Trial Outcome</th><td> " . $event['Field28'] . "</td></tr>";

                                                        }
                                                    }
                                                }
                                                }?>
                                                <?php
                                                // Liberated Tot
                                                echo "<tr><th scope='row' class='e-details'>Liberated Africans</th><td> " . $event['Field9'] . "</td></tr>";
                                                // Registered Tot
                                                echo "<tr><th scope='row' class='e-details'>Registered </th><td> " . $event['Field10'] . "</td></tr>";
                                                ?>
                                            </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php } ?>
                            <!--End for each -->
                            <?php }
                            ?>
                            <div id="<?php echo 'PeopleDetails'; ?>" class="tab-pane fade" role="tabpanel" aria-labelledby="<?php echo 'PeopleDetails-tab'; ?>">
                                <h5 class="card-header category_btn">People Details</h5>
                                <?php 
                                $sql = "SELECT * FROM `person_VI` WHERE `Field1` = '".$event['UI']."'";
                                $query = $conn->query($sql);
                                $people_list = $query->fetchAll(PDO::FETCH_ASSOC);?>
                                <div class="card-body">
                                    <?php 
                                    if($people_list){ ?> 
                                        <div class="table-responsive-md">
                                            <table class="table table-striped">
                                                <tbody>
                                                    <thead class="thead-dark">
                                                        <tr>
                                                        <th scope="col">UI</th>
                                                        <!--<th scope="col">Case Name</th>--> 
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Age</th>
                                                        <th scope="col">Sex</th>
                                                        <th scope="col">Height (ft)</th>
                                                        <th scope="col"></th>
                                                        </tr>
                                                    </thead>
                                                    <?php 
                                                    foreach($people_list as $person){ ?>
                                                        <tr>
                                                            <td><?php echo $person['UI']?></td>
                                                            <!--<td><?php //echo $person['Field2']?></td>-->
                                                            <td><?php echo $person['Name']?></td>
                                                            <td><?php echo $person['Field8']?></td>
                                                            <td><?php echo$person['Field10']?></td>
                                                            <td><?php echo $person['Field12']?></td>
                                                            <td><?php echo '<a style="color:var(--orange)" target="_blank" href="../digital-resources/people_details.php?PersonID='.$person['personID'].'">View Details</a>'?></td>
                                                        </tr>
                                                    <?php 
                                                    }?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php 
                                    } else {
                                        echo '<h6>No people found.</h6>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <!--Sources-->
                            <?php
                            if ($HasSources) {
                            ?>
                        </div>
                    </div>
                    <div class="col-11">
                        <div class="card shadow-sm p-3 mb-5 bg-white rounded tab-content" id="caseTabsContent">
                            <div class="accordion mt-3" id="accordionExample">
                                <div class="card">
                                <div class="card-header" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <h5 class="card-header category_btn">List of Source(s)</h5>
                                </div>
                                <div class="card-body">
                                    <?php

                                    $sql = "SELECT * FROM objects_person op left join `object` o on op.objectID = o.objectID  where op.personID=" . $event['personID'] . " ;";
                                    $query = $conn->query($sql);
                                    $Sources = $query->fetchAll(PDO::FETCH_ASSOC);
                                    // If objectID in object_person = 0 - source is unavailable
                                    if($Sources){
                                        $testData = $Sources[0]; // Will always have atleast one results. Check first one.
                                        if($testData['objectID']==0 || $testData['objectID'] == NULL){
                                            echo '<p><b>Disclaimer:</b><br>There are currently no digital resources available for this particular case. The intention is to provide access to digitized archival materials relevant to each case, as well as link each case to the appropriate legislation. We are currently seeking more support to add these records, which require much time and effort to input and create metadata on a document-by-document basis. For examples of available digital materials, please refer to the cases related to the courts of Freetown, Sierra Leone between 1808 and c. 1850. For further access, please refer to the sources cited for this particular case.</p>';
                                        } else {
                                            $sql = "SELECT x.* FROM LA_Source_V1 x WHERE indexpage <> 0 ORDER BY indexpage ";
                                            $query = $conn->query($sql);
                                            $SourcesColumn = $query->fetchAll(PDO::FETCH_ASSOC);
    
                                            $activeClass = 'active'; ?>
                                            <header>
    
                                            <div id="carouselExampleControls" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
                                                <div class="carousel-inner">
                                                    <?php
                                                    foreach ($Sources as $key => $Source) {
                                                        $activeClass = $key == 0 ? "active" : "";?>
                                                        <!-- Slide One - Set the background image for this slide in the line below -->
                                                        <div class="carousel-item <?php echo $activeClass; ?>">
                                                            <div class="row">
                                                                <div class="col-md-8">
                                                                    <iframe src="https://la.regeneratedidentities.org/project/project<?php echo $Source['File'] ?>" style="width:100%; height:100%; padding-top: 1.2rem;" noresize=yes frameborder=0 marginheight=0 marginwidth=0 scrolling="no"></iframe>
                                                                </div>
                                                                <div class="col-md-4 source_details">
                                                                    <h3> Source Details</h3>
                                                                    <ul>
                                                                        <?php
                                                                        echo "<li><strong>UID (Sources)</strong>: " . $Source['UI'] . "</li>";
                                                                        foreach ($SourcesColumn as $key => $value) {
                                                                        $v = $value['ColumnName'];
                                                                        if($v!='Field21' && $v!='Field22' && $v!='Field23' && $v!='Field26'){
                                                                            if (isset($Source[$value['ColumnName']])) {
                                                                                $d = $Source[$value['ColumnName']];
                                                                                if($d=='' || $d==NULL){
                                                                                    $d = 'Unspecified';
                                                                                }
                                                                                echo "<li><strong>" . $value['display'] . "</strong>: " . $d . "</li>";
                                                                                }
                                                                            }
                                                                        } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <button id="controlP" class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                                    <span><i class="fa fa-chevron-circle-left iconCustomFa" aria-hidden="true"></i></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </button>
                                                <button id="controlN" class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                                    <i class="fa fa-chevron-circle-right iconCustomFa" aria-hidden="true"></i>
                                                    <span class="visually-hidden">Next</span>
                                                </button>
                                            </div>
                                            </header>
                                        <?php }

                                    } else {
                                        echo '<p><b>Disclaimer:</b><br>There are currently no digital resources available for this particular case. The intention is to provide access to digitized archival materials relevant to each case, as well as link each case to the appropriate legislation. We are currently seeking more support to add these records, which require much time and effort to input and create metadata on a document-by-document basis. For examples of available digital materials, please refer to the cases related to the courts of Freetown, Sierra Leone between 1808 and c. 1850. For further access, please refer to the sources cited for this particular case.</p>';
                                    } ?>
                                    
                                    </div>
                                </div>

                            <?php }
                            ?>
                            <!--End Sources-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page Content-->
      </div>
      <div class="">
        <?php require_once("../public/footer.php"); ?>
      </div>
    </section>
    <script type="text/javascript">
      $(document).ready(function(){
        <?php if(isset($hideControls)):?>
            $('#controlP').css('display','none');
            $('#controlN').css('display','none');
        <?php endif; ?>
      });
    </script>
  </body>
</html>
