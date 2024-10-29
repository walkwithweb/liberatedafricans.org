<?php
  require_once("../public/head.php");
  require_once("../database.php");
?>
  <body>
    <section>
      <?php require_once("../public/header.php"); ?>
      <div class="container-fluid content">
                <!--Page Title-->
                <div class="row justify-content-center">
                    <div id="pageTitleContainer" class="col-11 justify-content-start">
                        <h1 class="pageTitle">Team</h1>
                        <hr class="pageTitleBorder">
                    </div>
                </div>
                <!--Start Page Content-->
                <div class="row justify-content-center align-items-center">
                    <div id="main-content" class="col-11 mb-5 pb-5">
                        <!--Your code starts here-->
                        <div id="teamTable" class="px-3 pt-4 table-responsive-md">
                          <?php 
                          $sql = "SELECT * FROM `Team` WHERE Title LIKE 'Overview'";
                          $query = $conn->query($sql);
                          while($res = $query->fetch(PDO::FETCH_ASSOC)){
                            echo htmlspecialchars_decode($res['Main_Text']);
                          }
                          ?>
                        </div>
                        <div id="teamTable" class="px-3 py-4 table-responsive-md">
                          <?php 
                          $sql = "SELECT * FROM `Team` WHERE Title LIKE 'Programmers & Designers'";
                          $query = $conn->query($sql);
                          while($res = $query->fetch(PDO::FETCH_ASSOC)){
                            echo htmlspecialchars_decode($res['Main_Text']);
                          }
                          ?>
                            <?php
                              $sql = "SELECT * FROM `Team_Developers` ORDER BY `Last_Name`";
                              $query = $conn->query($sql);
                              $res = $query->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <div class="row">
                              <?php $cnt = count($res); $stop = ceil($cnt/3); $stopTwo = $stop * 2;?>
                              <div class="col-md-4">
                                <?php
                                  for($i=0; $i<$stop; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . ' ('. $res[$i]['Year'] . ') <br>';
                                  }
                                ?>
                              </div>
                              <div class="col-md-4">
                                <?php
                                  for($i=$stop; $i<$stopTwo; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . ' ('. $res[$i]['Year'] . ') <br>';
                                  }
                                ?>
                              </div>
                              <div class="col-md-4">
                                <?php
                                  for($i=$stopTwo; $i<$cnt; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] .' ('. $res[$i]['Year'] . ') <br>';
                                  }
                                ?>
                              </div>
                            </div>
                        </div>
                        <div id="teamTable" class="px-3 py-4 table-responsive-md">
                            <?php 
                            $sql = "SELECT * FROM `Team` WHERE Title LIKE 'Advisors & Collaborators'";
                            $query = $conn->query($sql);
                            while($res = $query->fetch(PDO::FETCH_ASSOC)){
                              echo htmlspecialchars_decode($res['Main_Text']);
                            }
                            ?>
                            <?php
                              $sql = "SELECT * FROM `Team_Experts` ORDER BY `Last_Name`";
                              $query = $conn->query($sql);
                              $res = $query->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <div class="row">
                              <?php $cnt = count($res); $stop = ceil($cnt/3); $stopTwo = $stop * 2;?>
                              <div class="col-md-4">
                                <?php
                                  for($i=0; $i<$stop; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . '<br>';
                                  }
                                ?>
                              </div>
                              <div class="col-md-4">
                                <?php
                                  for($i=$stop; $i<$stopTwo; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . '<br>';
                                  }
                                ?>
                              </div>
                              <div class="col-md-4">
                                <?php
                                  for($i=$stopTwo; $i<$cnt; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . '<br>';
                                  }
                                ?>
                              </div>
                            </div>
                        </div>
                        <div id="teamTable" class="px-3 py-4 table-responsive-md">
                            <?php 
                            $sql = "SELECT * FROM `Team` WHERE Title LIKE 'Research Assistants'";
                            $query = $conn->query($sql);
                            while($res = $query->fetch(PDO::FETCH_ASSOC)){
                              echo htmlspecialchars_decode($res['Main_Text']);
                            }
                            ?>
                            <?php
                              $sql = "SELECT * FROM `Team_Assistants` ORDER BY `Last_Name`";
                              $query = $conn->query($sql);
                              $res = $query->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <div class="row">
                              <?php $cnt = count($res); $stop = ceil($cnt/3); $stopTwo = $stop * 2;?>
                              <div class="col-md-4">
                                <?php
                                  for($i=0; $i<$stop; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] .' ('. $res[$i]['Year'] . ')<br>';
                                  }
                                ?>
                              </div>
                              <div class="col-md-4">
                                <?php
                                  for($i=$stop; $i<$stopTwo; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . ' ('. $res[$i]['Year']  . ')<br>';
                                  }
                                ?>
                              </div>
                              <div class="col-md-4">
                                <?php
                                  for($i=$stopTwo; $i<$cnt; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . ' ('. $res[$i]['Year']  . ')<br>';
                                  }
                                ?>
                              </div>
                            </div>
                        </div>
                        <div id="teamTable" class="px-3 py-4 table-responsive-md">
                            <?php 
                            $sql = "SELECT * FROM `Team` WHERE Title LIKE 'Archiving & Sustainability'";
                            $query = $conn->query($sql);
                            while($res = $query->fetch(PDO::FETCH_ASSOC)){
                              echo htmlspecialchars_decode($res['Main_Text']);
                            }
                            ?>
                            <?php
                              $sql = "SELECT * FROM `Team_Archiving` ORDER BY `Last_Name`";
                              $query = $conn->query($sql);
                              $res = $query->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <div class="row">
                              <?php $cnt = count($res); $stop = ceil($cnt/3); $stopTwo = $stop * 2;?>
                              <div class="col-md-4">
                                <?php
                                  for($i=0; $i<$stop; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] .'<br>';
                                  }
                                ?>
                              </div>
                              <div class="col-md-4">
                                <?php
                                  for($i=$stop; $i<$stopTwo; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . '<br>';
                                  }
                                ?>
                              </div>
                              <div class="col-md-4">
                                <?php
                                  for($i=$stopTwo; $i<$cnt; $i++){
                                    echo $res[$i]['First_Name'] . ' ' . $res[$i]['Last_Name'] . '<br>';
                                  }
                                ?>
                              </div>
                            </div>
                        </div>
                        <div id="research" class="px-3 pt-4">
                          <h4>Sponsors</h4>
                            <hr>
                        </div>
                        <div id="statement-resources"  class="px-3 pb-4">
                          <div class="row justify-content-center">
                            <div class="col-3 col-sm-2 col-md-1 d-flex">
                              <img src="../assets/img/sponsors/british_library.png" class="img-fluid my-auto" alt="Endangered Archive Program, British Library">
                            </div>
                            <div class="col-9 col-sm-5 col-md-3 d-flex">
                              <img src="../assets/img/sponsors/harriet_tubman_institute.png" class="img-fluid my-auto" alt="The Harriet Tubman Institute, York University">
                            </div>
                            <div class="col-sm-5 col-md-2 d-flex">
                              <img src="../assets/img/sponsors/hutchins_center.png" class="img-fluid my-auto" alt="The Hutchins Center, Harvard University">
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex">
                              <img src="../assets/img/sponsors/mellon_foundation.png" class="img-fluid my-auto" alt="Mellon Foundation">
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex">
                              <img src="../assets/img/sponsors/neh.jpg" class="img-fluid my-auto" alt="National Endowment for the Humanities">
                            </div>

                            <div class="col-sm-6 col-md-3 d-flex py-md-2">
                              <img src="../assets/img/sponsors/slpa.png" class="img-fluid my-auto" alt="Sierra Leone Public Archives">
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex py-md-2">
                              <img src="../assets/img/sponsors/nationaal_Archief_2018.png" class="img-fluid my-auto" alt="Nationaal Archief">
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex">
                              <img src="../assets/img/sponsors/SSHRC.png" class="img-fluid my-auto" alt="Social Sciences and Humanities Research Council of Canada">
                            </div>
                            <div class="col-sm-6 col-md-3 d-flex">
                              <img src="../assets/img/sponsors/cu_boulder.png" class="img-fluid my-auto" alt="The University of Colorado, Boulder">
                            </div>
                            <div class="col-6 col-md-2 d-flex">
                              <img src="../assets/img/sponsors/www.png" class="img-fluid my-auto" alt="Walk With Web Inc.">
                            </div>
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
