<?php
  require_once("../public/head.php");
  require_once("../database.php");
?>
  <body>
    <section>
      <?php require_once("../public/header.php"); ?>
      <?php
        if(isset($_GET['section'])){ $section=$_GET['section']; } else { $section=0; }
        if(isset($_GET['cv'])){ $cv=$_GET['cv']; } else { $cv=0; }
        if(isset($_GET['colName'])) { $colName=$_GET['colName']; } else { $colName=0; }
      ?>
      <div class="container-fluid content">
        <?php
            $sql = "SELECT * FROM `Research` WHERE Section = 'Digital Resources'";
            $query = $conn->query($sql);
            $row = $query->fetchAll(PDO::FETCH_ASSOC);
            if(!$row){
                header("Location: ../public/index.php");
            } else { ?>
                <!--Page Title-->
                <div class="row justify-content-center">
                    <div id="pageTitleContainer" class="col-11 justify-content-start">
                        <h1 class="pageTitle">Research</h1>
                        <hr class="pageTitleBorder">
                    </div>
                </div>
                <!--Start Page Content-->
                <div class="row justify-content-center align-items-center">
                    <div id="main-content" class="col-11 mb-5 pb-5">
                        <!--Your code starts here-->
                        <div id="research" class="px-3 pt-4">
                          <h4>Digital Resources</h4>
                            <hr>
                            <p>During data collection, the following open-source archives were consulted and contain primary sources surrounding "Liberated Africans." Materials from some of these resources are reorganized and presented herein.</p>
                        </div>
                        <div id="digital-resources" class="container-fluid">
                          <div class="row">
                            <?php $cnt = count($row); $stop = intval($cnt/2);?>
                            <div class="col-xl-6 px-1">
                              <?php
                                for($i=0; $i<$stop; $i++){
                                  if($row[$i]['Flag']==1){
                                    echo $row[$i]['Title'].', <a href="'.$row[$i]['URL'].'" target="_blank"><b>'.$row[$i]['URL'].'</b></a><br>';
                                  } else {
                                    echo $row[$i]['Title'].', <a href="'.$row[$i]['URL'].'" target="_blank">'.$row[$i]['URL'].'</a><br>';
                                  }
                                }
                              ?>
                            </div>
                            <div class="col-xl-6 px-1">
                            <?php
                                for($i=$stop; $i<$cnt; $i++){
                                  if($row[$i]['Flag']==1){
                                    echo $row[$i]['Title'].', <a href="'.$row[$i]['URL'].'" target="_blank"><b>'.$row[$i]['URL'].'</b></a><br>';
                                  } else {
                                    echo $row[$i]['Title'].', <a href="'.$row[$i]['URL'].'" target="_blank">'.$row[$i]['URL'].'</a><br>';
                                  }
                                }
                              ?>
                            </div>
                          </div>
                        </div>
                        <div id="research" class="px-3 pt-4">
                          <h4>Key Outputs</h4>
                          <hr>
                        </div>
                        <div id="digital-resources" class="container-fluid">
                          <div class="row">
                            <?php
                              $sql = "SELECT * FROM `Research` WHERE Section = 'Key Outputs'";
                              $query = $conn->query($sql);
                              $results = $query->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <?php $cnt = count($results);?>
                            <div class="col-12 px-1">
                              <?php
                                for($i=0; $i<$cnt; $i++){
                                  echo htmlspecialchars_decode($results[$i]['Main_Text']);
                                }
                              ?>
                            </div>
                          </div>
                        </div>
                        <div id="research" class="px-3 pt-4">
                          <h4>Donated Spreadsheets</h4>
                          <hr>
                          <p>Beyond the above digital resources, the following researchers graciously donated raw spreadsheets, which were modified and integrated into this metadata scheme:</p>
                        </div>
                        <div id="digital-resources" class="container-fluid">
                          <div class="row">
                            <?php
                              $sql = "SELECT * FROM `Research` WHERE Section = 'Donated Datasets'";
                              $query = $conn->query($sql);
                              $results = $query->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <?php $cnt = count($results);?>
                            <div class="col-12 px-1">
                              <?php
                                for($i=0; $i<$cnt; $i++){
                                  echo $results[$i]['Title'].'<br>';
                                }
                              ?>
                            </div>
                          </div>
                        </div>
                        <div id="teamTable" class="px-3 pt-4">
                          <h4>Metadata</h4>
                          <hr>
                          <p>Between 2016 and 2017, the people, places, events, and sources <a href="https://cloudshare.regeneratedidentities.org/LA/Documents/PDF01_Liberated_Africans_Metadata_Documentation.pdf" target="_Blank">metadata</a> scheme and controlled vocabulary was initially developed. The following section provides a list of all controlled vocabulary terms currently in use:</p>
                          <div class="row pt-3 pb-5">
                            <div class="col-lg-4 col-md-6 col-sm-12 border-right">
                              <!--Person / Events / Sources-->
                              <div class="col-10 p-4">
                                <div class="card <?php if(strcmp($section,"LA_CourtForm_V1")){ echo 'bg-light';}else { echo 'bg-metadata';}?> shadow">
                                  <a style="color:#000;" href="?section=LA_CourtForm_V1">
                                  <div class="card-body" >
                                    <h6 class="text-black">CASES</h6>
                                  </div>
                                  </a>
                                </div>
                              </div>
                              <div class="col-10 p-4">
                                <div class="card <?php if(strcmp($section,"EO_EventForm_V1")){ echo 'bg-light';}else { echo 'bg-metadata';}?> shadow">
                                  <a style="color:#000;" href="?section=EO_EventForm_V1">
                                  <div class="card-body" >
                                    <h6 class="text-black">EVENTS</h6>
                                  </div>
                                  </a>
                                </div>
                              </div>
                              <div class="col-10 p-4">
                                <div class="card <?php if(strcmp($section,"LA_Source_V1")){ echo 'bg-light';}else { echo 'bg-metadata';}?> shadow">
                                  <a style="color:#000;" href="?section=LA_Source_V1">
                                  <div class="card-body" >
                                    <h6 class="text-black">SOURCES</h6>
                                  </div>
                                  </a>
                                </div>
                              </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 <?php if($section!="0"){ echo 'border-right';} ?>">
                              <?php
                              // Display All CV Meta-fields for the categories
                              if($section!="0"){
                                $q1="SELECT * FROM `".$section."` ORDER BY `indexpage`";
                                $query1 = $conn->query($q1);
                                while ($person_metafields = $query1->fetch(PDO::FETCH_ASSOC)) {
                                  if(strcmp($person_metafields['FieldType'],"dropdown-CV")==0 || strcmp($person_metafields['FieldType'],"dropdown-CV-multi")==0){ ?>
                                    <div class="col-10 p-4">
                                      <div class="card <?php if(strcmp($colName,$person_metafields['ColumnName'])){ echo 'bg-light';} else { echo 'bg-metadata';}?> shadow">
                                        <a style="color:#000;" href="?section=<?php echo $section;?>&colName=<?php echo $person_metafields['ColumnName'];?>&cv=<?php echo $person_metafields['Options'];?>">
                                        <div class="card-body" >
                                          <h6 class="text-black"><?php echo $person_metafields['display'];?></h6>
                                        </div>
                                        </a>
                                      </div>
                                    </div>
                                  <?php
                                  }}
                                }
                              ?>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 wrap-links">
                            <?php
                            if($cv!="0"){
                              $q3="SELECT * FROM `".$cv."` where Status!=0  ORDER BY `listorder` ";
                              $query3 = $conn->query($q3);

                              $q4="SELECT * FROM `".$section."` where `Options`='".$cv."' AND `ColumnName`='".$colName."' ";
                              $query4 = $conn->query($q4);
                              $defination = $query4->fetch(PDO::FETCH_ASSOC);
                              ?>
                              <h4 class="text-uppercase border-bottom "><?php echo $defination['display'];?></h4>
                              <p class="pb-2"><?php echo $defination['Definitions'];?></p>
                              <div class="table-wrapper-scroll-y my-custom-scrollbar">
                              <table  class="table table-bordered table-striped pt-5 " id="dataTable" width="100%" cellspacing="0">
                                <tbody style="height: 50px;">
                                <?php
                                if($section=="LA_CourtForm_V1"){
                                  if(isset($_GET['colName']))  {  $colName=$_GET['colName']; }
                                  while ($entries = $query3->fetch(PDO::FETCH_ASSOC)) {
                                    if($cv=='CV_Multi_Table_Name'){
                                      // Handle dropdown-CV-multi needs ; in url
                                      $id=$entries['ID'].'%3B';
                                    } else {
                                      $id=$entries['ID'];
                                    }
                                    echo "<tr>";
                                      if($entries['Name']=='0'){?>
                                        <td width="150"></td>
                                      <?php }else{?>
                                        <td ><?php echo  htmlspecialchars_decode($entries['Name']); ?></td>
                                        <td><small class="text-muted">
                                          <!--
                                          <a class="category_btn"
                                            href="../digital-resources/court-records.php?mt=1&colName=<?php echo $colName; ?>&sTerm=<?php echo $id; ?>&tl=<?php echo $defination['display']; ?>&td=<?php echo $entries['Name']; ?>" target="_blank">
                                            <h6 class="text-center">View Details</h6>
                                          </a>
                                          -->
                                        </small></td>
                                      <?php }?>
                                    <?php
                                    echo "</tr>";

                                  }
                                } else
                                if($section=="LA_Source_V1"){
                                  $aValues=$cv.'%5B%5D';

                                  if(isset($_GET['colName']))  {  $colName=$_GET['colName']; }
                                  while ($entries = $query3->fetch(PDO::FETCH_ASSOC)) {
                                    $id=$entries['ID'];
                                    echo "<tr>";
                                      if($entries['Name']=='0'){?>
                                        <td width="150"></td>
                                      <?php }else{?>
                                        <td ><?php echo  htmlspecialchars_decode($entries['Name']); ?></td>
                                        <td><small class="text-muted">
                                          <!--
                                          <a class="category_btn"
                                            href="source_list.php?<?php echo $aValues;?>=<?php echo $id;?>">
                                            <h6 class="text-center">View Details</h6>
                                          </a>
                                          -->
                                        </small></td>
                                      <?php }?>
                                    <?php
                                    echo "</tr>";

                                  }
                                } else {
                                  while ($entries = $query3->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                              if($entries['Name']=='0'){?>
                                                  <td width="150"></td>
                                              <?php }else{?>
                                                <td ><?php echo  htmlspecialchars_decode($entries['Name']); ?></td>
                                              <?php }
                                                ?>
                                                <?php
                                    echo "</tr>";
                                  }
                                }
                                  echo " </tbody></table>";
                                }?>
                            </div>
                          </div>
                        </div>
                        <!--Your code ends here-->
                    </div>
                </div>
                <!-- End Page Content-->
            <?php }
        ?>

      </div>
      <div class="">
        <?php require_once("../public/footer.php"); ?>
      </div>
    </section>
    <script type="text/javascript">
      // Handle maintaining scroll position when age reloads
      document.addEventListener("DOMContentLoaded", function (event) {
        var scrollpos = sessionStorage.getItem('scrollpos');
        if (scrollpos) {
          window.scrollTo(0, scrollpos);
          sessionStorage.removeItem('scrollpos');
        }
      });

      window.addEventListener("beforeunload", function (e) {
          sessionStorage.setItem('scrollpos', window.scrollY);
      });
    </script>
  </body>
</html>
