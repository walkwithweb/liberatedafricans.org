<?php
  require_once("head.php");
  require_once("../database.php");
?>
  <body class="home">
    <section id="home" class="main">
      <?php require_once("header.php"); ?>
      <div class="container-fluid content">
        <!--Start Page Content-->
        <div id="landing" class="row">
          <!--Landing Page Video--> 
          <div id="landing-right" class="col-12 col-lg-12 px-0" style="margin-bottom:-10px;">
            <video autoplay muted playsinline id="laVideo" style="width:100%;">
              <source src="../assets/video/la_home_video.mp4" type="video/mp4">
            </video>
          </div>
          <div id="landing-left" class="col-12 col-lg-12">
            <!--Landing Page Site Description--> 
            <div class="row pb-2">
              <div class="col-md-10 p-5 mx-auto">
                <p>Between 1800 and 1920, the abolition of the slave trade disguised another type of slavery. The victims were classified as “Liberated Africans,” although they were not actually freed. This website is dedicated to the memory of over 700,000 enslaved people involuntarily indentured by governments claiming to bring an end to the slave trade from Africa.</p>
                <center><a id="landing-btn" href=" ../about/essays.php" class="btn">Learn More</a></center>
              </div>
            </div>
          </div>
        </div>
        <div id="news" class="row">
          <!--Updates section--> 
          <div class="col-10 px-5">
            <h5>Media & Events</h5>
            <hr>
            <?php
              $sql = "SELECT x.* FROM Home x WHERE Title = 'News & Press'";
              $query = $conn->query($sql);
              $row = $query->fetch(PDO::FETCH_ASSOC);
              if(!$row){
                  echo '<div align="center">No records found.</div>';
              } else { ?>
                  <div id="news-box" class="mt-5 mb-5">
                    <?php echo htmlspecialchars_decode($row['Main_Text']);?>
                  </div>
            <?php }
            ?>
          </div>
          <div class="col-2">
            <img id="landing-logo-yellow" src="../assets/img/logo-LA-yellow.png" alt="Liberated Africans logo in yellow">
          </div>
        </div>
        <!-- End Page Content-->
      </div>
      <div class="footer">
        <div class="row justify-content-center">
          <div class="col-6 col-md-2">
            <a rel="license" href="https://walkwithweb.org/" target="_blank"><img class="img-fluid" alt="Walk With Web Inc." src="../assets/img/www_logo.png"/></a>
          </div>
          <div class="col-6 col-md-2">
            <a rel="license" href="https://www.colorado.edu/lab/dsrl/" target="_blank"><img class="img-fluid" alt="Digital Slavery Research Lab" src="../assets/img/dsrl_logo.png" /></a>
          </div>
        </div>
        <div class="inline-imgs">
          <div class="col-8 col-md-4 mx-auto pt-5">
            <center><h6 class="text-white"><i><a href="../about/project-team.php" style="color: white !important;">Learn more about our team and sponsors</a></i></h6></center>
          </div>
        </div>
        <div class="container mt-3">
          <div class="row copyright text-center" style="color:var(--yellow);">
            <h6>Find us online!</h6>
            <div class="mt-1 social-links-home">
              <a href="https://www.facebook.com/walkwithweb/" class="facebook px-2" target="_blank" title="Facebook"><i class="fab fa-facebook-square fa-lg"></i></a>
              <a href="https://www.instagram.com/walkwithweb/" class="instagram px-2" target="_blank" title="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
              <a href="https://ca.linkedin.com/company/walkwithweb" class="linkedin px-2" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in fa-lg"></i></a>
              <a href="https://twitter.com/walkwithweb?lang=en" class="twitter px-2" target="_blank" title="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
            </div>
          </div>
        </div>
      </div>
    </section>
  </body>
</html>
