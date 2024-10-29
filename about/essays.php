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
                <h1 class="pageTitle">Essays</h1>
                <hr class="pageTitleBorder">
            </div>
        </div>
        <!--Start Page Content-->
        <div class="row justify-content-center align-items-center">
            <div id="main-content" class="col-11 mb-5">
                <!--Your code starts here-->
                <?php
                $query = $conn->query("SELECT distinct(Title), id, Main_Text, Read_More, List_Order FROM `Essays` ORDER BY `List_Order`");
                $fetched = $query->fetchAll(PDO::FETCH_ASSOC);
                if(!$fetched){
                    header("Location: ../public/index.php");
                } else { ?>
                    <div class="row">
                        <?php 
                        if(isset($_GET['id'])){
                            $essayID = $_GET['id'];
                        } else {
                            $essayID = 1;
                        }
                        ?>
                        <div  id="essays-nav" class=" col-md-3 col-lg-2 col-12 justify-content-center align-items-center">
                            <ul class="nav nav-list">
                                <?php 
                                $essays = array();
                                $e = $essayID - 1;
                                for($i=0; $i < count($fetched); $i++){
                                    $temp = $fetched[$i];
                                    $etitle = $temp['Title'];
                                    $eID = $temp['id'];
                                    $emainText = $temp['Main_Text'];
                                    $ereadMore = $temp['Read_More'];
                                    $essays[$eID] = [
                                        "Main_Text" => $emainText,
                                        "Read_More" => $ereadMore
                                    ];?>
                                    <li>
                                        <a href="essays.php?id=<?php echo $eID; ?>" class="<?php echo ($eID==($essayID) ? 'active' : ''); ?>"><?php echo $etitle; ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <div class="subpage col-md-9 col-lg-10 col-12 py-5 px-5">
                            <?php 
                            $essay = $essays[$essayID];
                            echo htmlspecialchars_decode($essay['Main_Text']);
                            ?>
                            <?php echo htmlspecialchars_decode($essay['Read_More']);?>
                        </div>
                    </div>
                <?php } ?>
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