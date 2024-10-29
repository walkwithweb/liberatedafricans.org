<?php
  require_once("../public/head.php");
  require_once("../database.php")
  /* 
   * Listing of Libertated Africans from the Violence in Iron and Silver Dataset 
   */
?>
  <body>
    <section>
      <?php require_once("../public/header.php"); ?>
      <div class="container-fluid content">
        <!--Page Title-->
        <div class="row justify-content-center">
            <div id="pageTitleContainer" class="col-11 justify-content-start">
                <h1 class="pageTitle">Liberated Africans</h1>
                <hr class="pageTitleBorder">
            </div>
        </div>
        <!--Start Page Content-->
        <div class="container pt-3">
          <div class="row">
            <div class="col-8 mx-auto" id="table-top">
              <?php
              if(isset($_GET['start'])){
                  $start=$_GET['start'];
                } else{
                  $start=0;
                }
              $end=100;
              $query = $conn->query("SELECT COUNT(`personID`) as `Count` FROM `person_VI`");
              $row_number = $query->fetch(PDO::FETCH_ASSOC);
              $max_count=$row_number['Count'];
                  ?>
              <center><b><p class="p-2 bg-white">Viewing <?php echo $start+1;?> - <?php echo $start+$end;?></center></b></p>
            </div>
            <nav aria-label="Page navigation example table-dark">
              <ul class="pagination">
                <?php if($start!=0){?>
                <li class="page-item"><a class="page-link" href="?start=<?php echo $start-$end;?>"><b>&#x2190;</b></a></li>
              <?php } ?>
              <?php if($max_count>=$start+$end){?>
                <li class="page-item"><a class="page-link" href="?start=<?php echo $start+$end;?>"><b>&rarr;</b></a></li>
              <?php } ?>
              </ul>
            </nav>
            <table class="table table-dark mb-5">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">UI</th>
                  <th scope="col">Case Name</th>
                  <th scope="col">Name</th>
                  <th scope="col">Age</th>
                  <th scope="col">Sex</th>
                  <th scope="col">Height (ft)</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $query = $conn->query("SELECT * FROM `person_VI` Limit ".$start.", ".$end.";");
                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                  <td><?php echo $row['UI']?></td>
                  <td><?php echo $row['Field2']?></td>
                  <td><?php echo $row['Name']?></td>
                  <td><?php echo $row['Field8']?></td>
                  <td><?php echo$row['Field10']?></td>
                  <td><?php echo $row['Field12']?></td>
                  <td><?php echo '<a style="color:var(--orange)" target="_blank" href="people_details.php?PersonID='.$row['personID'].'">View Details</a>'?></td>
                </tr>
              <?php }?>
              </tbody>
            </table>
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
