<?php
    if (!isset($_GET['PersonUI']) || empty($_GET['PersonUI']) ) {
        if(!isset($_GET['PersonID']) || empty($_GET['PersonID'])) {
            header("Location: people.php");
            die;
        }
    }
    require_once("../public/head.php");
    require_once("../database.php");
    /* 
     * Digital Identity Listing of Libertated Africans from the Violence in Iron and Silver Dataset 
     */
?>
  <body class="home">
    <section id="courts-cases" class="main">
      <?php require_once("../public/header.php"); ?>
      <div class="container-fluid content courtcases">
        <!--Page Title-->
        <div class="row justify-content-center">
            <div id="pageTitleContainer" class="col-11 justify-content-start">
                <div class="row">
                    <div id="leftTitle" class="col-12 d-flex">
                        <h1 class="pageTitle">Digital Identity</h1>
                    </div>
                </div>
                <hr class="pageTitleBorder">
            </div>
        </div>
        <!--Start Page Content-->
        <div class="row justify-content-center align-items-center">
            <div id="main-content" class="col-11 mb-5" style="padding-left:0;padding-right:0;">
            <?php
            if (isset($_GET['PersonUI']) && !empty($_GET['PersonUI'])) {
                $sql = "SELECT `personID` FROM `person_VI` where `UI`=" . $_GET['PersonUI'] . " limit 1";
                $query = $conn->query($sql);
                $personData = $query->fetch(PDO::FETCH_ASSOC);
                $PersonID = $personData['personID'];
            } else {
                $PersonID = $_GET['PersonID'];
            }

            $sql = "SELECT * FROM person_VI where personID=" . $PersonID . " limit 1";
            $query = $conn->query($sql);
            $person = $query->fetch(PDO::FETCH_ASSOC);
            ?>

            <div class="container-fluid" id="body-container">
                <div class="row bg-white" style="padding-top:32px;">
                    <div class="row justify-content-center mt-2 mb-4">
                        <div class="col-12 col-md-10">
                            <h1><?php echo $person['Name'] ?></h1>
                        </div>
                    </div>
                    <div class="col-12">
                        <ul id="caseTabs" class="nav nav-tabs justify-content-center" role="tablist">
                            <?php
                            $sql = "SELECT distinct(SectionDisplay) FROM VI_Person_V1 WHERE `SectionDisplay` != 'Miscellaneous' ";
                            $query = $conn->query($sql);
                            $presults = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($presults as $key => $value){
                                if ($key == 0) { ?>
                                <li class="nav-item" role="presentation">
                                    <a id="<?php echo str_replace('&','',str_replace(' ', '', $value['SectionDisplay']));?>-tab" class="nav-link active show" data-bs-toggle="tab" data-bs-target="#<?php echo str_replace('&','',str_replace(' ', '', $value['SectionDisplay']));?>" type="button" role="tab" aria-controls="" aria-selected="true">Person Details</a>
                                </li>
                                <?php } else { ?>
                                <?php if($value['SectionDisplay']!='Miscellaneous'):?>
                                    <li class="nav-item" role="presentation">
                                    <a id="<?php echo str_replace('&','',str_replace(' ', '', $value['SectionDisplay']));?>-tab"  class="nav-link" data-bs-toggle="tab" data-bs-target="#<?php echo str_replace('&','',str_replace(' ', '', $value['SectionDisplay'])); ?>" type="button" role="tab" aria-controls="" aria-selected="true"><?php echo $value['SectionDisplay'];?></a>
                                    </li>
                                <?php endif; ?>
                                <?php }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <div id="caseTabsRow" class="row bg-white justify-content-center">
                    <div class="col-md-10">
                        <div class="card shadow-sm p-3 mb-5 bg-white rounded tab-content" id="caseTabsContent">
                            <?php
                            foreach ($presults as $key => $value){
                                if($key == 0) { ?>
                                <!--Key = 0 Deals with first tab - person details-->
                                <div id="<?php echo str_replace('&','',str_replace(' ', '', $value['SectionDisplay']));?>" class="tab-pane fade in show active" role="tabpanel" aria-labelledby="<?php echo str_replace('&','',str_replace(' ', '', $value['SectionDisplay']));?>-tab">
                                    <h5 class="card-header category_btn">Person Details</h5>
                                    <?php
                                    $sql = "SELECT * FROM VI_Person_V1 WHERE `SectionDisplay` = '".$value['SectionDisplay']."' AND status = 1 AND indexpage != '0' ORDER BY indexpage ";
                                    $query = $conn->query($sql);
                                    $ColumnDetails = $query->fetchAll(PDO::FETCH_ASSOC);?>
                                    <div class="card-body">
                                        <div class="table-responsive-md">
                                            <table class="table table-striped">
                                                <tbody>
                                                <?php 
                                                // Print the VI ID 
                                                echo "<tr><th scope='row' class='e-details'>UID </th><td> " . $person['UI'] . "</td> </tr>";

                                                foreach ($ColumnDetails as $key => $value) {
                                                    if ($value['FieldType'] == 'project-connect') { 
                                                        echo "<tr><th scope='row'>" . $value['display'] . " </th><td><a href='../databases/event_details.php?EventUI=\"".$person[$value['ColumnName']]."\"' target='_blank' style='color:var(--orange);'>"  . $person[$value['ColumnName']] . "</a></td> </tr>";
                                                    } else if ($value['FieldType'] == 'dropdown-CV') {
                                                        $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $person[$value['ColumnName']] . " limit 1;";
                                                        $query = $conn->query($sql);
                                                        $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                        if($dynamic){
                                                            $dname = $dynamic['Name'];
                                                        } else {
                                                            $dname = '';
                                                        }
                                                        echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td> " . $dname . "</td> </tr>";
                                                    } else if ($value['FieldType'] == 'dropdown-CV-multi') {
                                                        $temp = preg_split('/;/',$person[$value['ColumnName']],-1,PREG_SPLIT_NO_EMPTY);
                                                        $f = 0;
                                                        $dname = '';
                                                        foreach($temp as $tval){
                                                            $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $tval . " limit 1;";
                                                            $query = $conn->query($sql);
                                                            $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                            if($dynamic){
                                                                if($f == 0){
                                                                    $dname = $dynamic['Name'];
                                                                    $f = 1;
                                                                } else {
                                                                    $dname .= ", " . $dynamic['Name'];
                                                                }
                                                            }
                                                        }
                                                        echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td> " . $dname . "</td> </tr>";
                                                    } elseif ($value['display'] == 'Source') {
                                                        $HasSources = true;
                                                        if (isset($person[$value['ColumnName']])) {
                                                            echo "<tr><th scope='row'>" . $value['display'] . " </th><td>"  . $person[$value['ColumnName']] . "</td> </tr>";
                                                        }
                                                    } else {
                                                        if($value['ColumnName']=="Field41"){
                                                            if(isset($person[$value['ColumnName']])){
                                                                echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td><a href='https://www.slavevoyages.org/voyage/".str_replace('V','',$person[$value['ColumnName']])."/variables' target='_blank' style='color:var(--orange);'>"  . $person[$value['ColumnName']] . "</a></td> </tr>";
                                                            }
                                                        } else {
                                                            if (isset($person[$value['ColumnName']])) {
                                                                echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td>"  . $person[$value['ColumnName']] . "</td> </tr>";
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
                                <?php if($value['SectionDisplay']!='Miscellaneous'): ?>
                                    <div id="<?php echo str_replace('&','',str_replace(' ', '', $value['SectionDisplay']));?>" class="tab-pane fade" role="tabpanel" aria-labelledby="<?php echo str_replace('&','',str_replace(' ', '', $value['SectionDisplay']));?>-tab">
                                        <h5 class="card-header category_btn"><?php echo $value['SectionDisplay'];?></h5>
                                        <?php
                                        $sql = "SELECT * FROM VI_Person_V1 WHERE `SectionDisplay` = '".$value['SectionDisplay']."' AND status = 1 AND indexpage != '0' ORDER BY indexpage ";
                                        $query = $conn->query($sql);
                                        $ColumnDetails = $query->fetchAll(PDO::FETCH_ASSOC);?>
                                        <div class="card-body">
                                            <div class="table-responsive-md">
                                                <table class="table table-striped">
                                                    <tbody>
                                                    <?php foreach ($ColumnDetails as $key => $value) {
                                                        if ($value['FieldType'] == 'project-connect') { 
                                                            echo "<tr><th scope='row'>" . $value['display'] . " </th><td><a href='../databases/event_details.php?EventUI=\"".$person[$value['ColumnName']]."\"' target='_blank' style='color:var(--orange);'>"  . $person[$value['ColumnName']] . "</a></td> </tr>";
                                                        } else if ($value['FieldType'] == 'dropdown-CV') {
                                                            $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $person[$value['ColumnName']] . " limit 1;";
                                                            $query = $conn->query($sql);
                                                            $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                            if($dynamic){
                                                                $dname = $dynamic['Name'];
                                                            } else {
                                                                $dname = '';
                                                            }
                                                            echo "<tr><th scope='row'>" . $value['display'] . " </th><td> " . $dname . "</td> </tr>";
                                                        } else if ($value['FieldType'] == 'dropdown-CV-multi') {
                                                            $temp = preg_split('/;/',$person[$value['ColumnName']],-1,PREG_SPLIT_NO_EMPTY);
                                                            $f = 0;
                                                            $dname = '';
                                                            foreach($temp as $tval){
                                                                $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $tval . " limit 1;";
                                                                $query = $conn->query($sql);
                                                                $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                                if($dynamic){
                                                                    if($f == 0){
                                                                        $dname = $dynamic['Name'];
                                                                        $f = 1;
                                                                    } else {
                                                                        $dname .= ", " . $dynamic['Name'];
                                                                    }
                                                                }
                                                            }
                                                            echo "<tr><th scope='row'>" . $value['display'] . " </th><td> " . $dname . "</td> </tr>";
                                                        } elseif ($value['display'] == 'Source') {
                                                            $HasSources = true;
                                                            if (isset($person[$value['ColumnName']])) {
                                                                echo "<tr><th scope='row'>" . $value['display'] . " </th><td>"  . $person[$value['ColumnName']] . "</td> </tr>";
                                                            }
                                                        } else {
                                                            if($value['ColumnName']=="Field41"){
                                                                if(isset($person[$value['ColumnName']])){
                                                                    echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td><a href='https://www.slavevoyages.org/voyage/".str_replace('V','',$person[$value['ColumnName']])."/variables' target='_blank' style='color:var(--orange);'>"  . $person[$value['ColumnName']] . "</a></td> </tr>";
                                                                }
                                                            } else {
                                                                if (isset($person[$value['ColumnName']])) {
                                                                    echo "<tr><th scope='row' class='e-details'>" . $value['display'] . " </th><td>"  . $person[$value['ColumnName']] . "</td> </tr>";
                                                                }
                                                            }
                                                        }
                                                    }?>
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

                                    $sql = "SELECT * FROM objects_person_VI op left join `object_VI` o on op.objectID = o.objectID  where op.personID=" . $person['personID'] . " AND o.doctype != 'VI_Screenshots_V1';";
                                    $query = $conn->query($sql);
                                    $Sources = $query->fetchAll(PDO::FETCH_ASSOC);
                                    // If objectID in object_person = 0 - source is unavailable
                                    if($Sources){
                                        $testData = $Sources[0]; // Will always have atleast one results. Check first one.
                                        if($testData['objectID']==0 || $testData['objectID'] == NULL){
                                            echo '<p><b>Disclaimer:</b><br>There are no sources to display at this time. Ongoing efforts are being made to expand the digital archive and add files to this case.</p>';
                                        } else {
                                            $sql = "SELECT x.* FROM VI_Object_V1 x WHERE indexpage <> 0 ORDER BY indexpage ";
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
                                                                    <iframe src="https://vi.regeneratedidentities.org/project/project<?php echo $Source['File'] ?>" style="width:100%; height:100%; padding-top: 1.2rem;" noresize=yes frameborder=0 marginheight=0 marginwidth=0 scrolling="no"></iframe>
                                                                </div>
                                                                <div class="col-md-4 source_details">
                                                                    <h3> Source Details</h3>
                                                                    <ul>
                                                                        <?php
                                                                        echo "<li><strong>UID (Source)</strong>: " . $Source['UI'] . "</li>";
                                                                        foreach ($SourcesColumn as $key => $value) {
                                                                            if ($value['FieldType'] == 'dropdown-CV') {
                                                                                $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $Source[$value['ColumnName']] . " limit 1;";
                                                                                $query = $conn->query($sql);
                                                                                $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                                                if($dynamic){
                                                                                    $dname = $dynamic['Name'];
                                                                                } else {
                                                                                    $dname = '';
                                                                                }
                                                                                echo "<tr><th scope='row'>" . $value['display'] . " </th><td> " . $dname . "</td> </tr>";
                                                                            } else if ($value['FieldType'] == 'dropdown-CV-multi') {
                                                                                $temp = preg_split('/;/',$Source[$value['ColumnName']],-1,PREG_SPLIT_NO_EMPTY);
                                                                                $f = 0;
                                                                                $dname = '';
                                                                                foreach($temp as $tval){
                                                                                    $sql = "SELECT x.* FROM " . $value['Options'] . " x WHERE ID=" . $tval . " limit 1;";
                                                                                    $query = $conn->query($sql);
                                                                                    $dynamic = $query->fetch(PDO::FETCH_ASSOC);
                                                                                    if($dynamic){
                                                                                        if($f == 0){
                                                                                            $dname = $dynamic['Name'];
                                                                                            $f = 1;
                                                                                        } else {
                                                                                            $dname .= ", " . $dynamic['Name'];
                                                                                        }
                                                                                    }
                                                                                }
                                                                                echo "<tr><th scope='row'>" . $value['display'] . " </th><td> " . $dname . "</td> </tr>";
                                                                            } else {
                                                                                if (isset($Source[$value['ColumnName']])) {
                                                                                    $d = $Source[$value['ColumnName']];
                                                                                    if($d=='' || $d==NULL || $d=='0'){
                                                                                        //$d = 'Unspecified';
                                                                                        $d = '';
                                                                                    }
                                                                                    echo "<li><strong>" . $value['display'] . "</strong>: " . $d . "</li>";
                                                                                    }
                                                                                }
                                                                            }
                                                                         ?>
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
                                        echo '<p><b>Disclaimer:</b><br>There are no sources to display at this time. Ongoing efforts are being made to expand the digital archive and add files to this case.</p>';
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
