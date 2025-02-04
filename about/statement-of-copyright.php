<?php
  require_once("../public/head.php");
  require_once("../database.php");
?>
  <body>
    <section>
      <?php require_once("../public/header.php"); ?>
      <div class="container-fluid content">
        <?php
            $sql = "SELECT x.* FROM About x WHERE Title = 'Ethics'";
            $query = $conn->query($sql);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if(!$row){
                header("Location: ../public/index.php");
            } else { ?>
                <!--Page Title-->
                <div class="row justify-content-center">
                    <div id="pageTitleContainer" class="col-11 justify-content-start">
                        <h1 class="pageTitle"><?php echo htmlspecialchars_decode($row['Title']);?></h1>
                        <hr class="pageTitleBorder">
                    </div>
                </div>
                <!--Start Page Content-->
                <div class="row justify-content-center align-items-center">
                    <div id="main-content" class="col-11 mb-5">
                        <!--Your code starts here-->
                        <div id="statement" class="px-3 py-4">
                            <?php echo htmlspecialchars_decode($row['Main_Text']);?>
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
  </body>
</html>
