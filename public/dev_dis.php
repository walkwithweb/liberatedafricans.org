
<?php header('Location: index.php'); ?>
<?php
  require_once("head.php");
?>
  <body>
    <section>
      <?php require_once("header.php");?>
      <div class="container-fluid content">
        <!--Page Title-->
        <div class="row justify-content-center">
            <div id="pageTitleContainer" class="col-11 justify-content-start">
                <h1 class="pageTitle">Development Disclaimer</h1>
                <hr class="pageTitleBorder">
            </div>
        </div>
        <!--Start Page Content-->
        <div class="row justify-content-center align-items-center">
            <div id="main-content" class="col-11 mb-5">
                <!--Your code starts here-->
                <div id="dev-disclaimer" class="row">
                    <div class="col-sm-10 mx-auto mt-5 pt-5" style="font-size:1.5rem; font-family:'Montserrat-Regular';">
                        <p>Due to ongoing developments on LiberatedAfricans.org, there may be technical difficulties in accessing databases and some sections of this version. We are seeking support to finish this stage and release all materials to the public. Please do not share or cite this link as content, data, and materials keep changing.</p>
                        <p>For any questions or concerns, please email our technical team at <a href="mailto:support@regid.ca">support@regid.ca</a></p>
                    </div>
                    <div class="col-sm-10 mx-auto mt-5" style="font-size:1.2rem; font-family:'Montserrat-Regular';">
                        <h4 style="font-style:italic;"><b>Related Links</b></h4>
                        <ol style="font-style:italic;">
                        <li><a href="https://liberatedafricans.org" target="_blank">Browse through the current online version of the Liberated Africans dataset (liberatedafricans.org)</a></li>
                        <li><a href="https://walkwithweb.org" target="_blank">Know more about our technical team and related projects (walkwithweb.org)</a></li>
                        <li><a href="https://journals.openedition.org/slaveries/2717" target="_blank">Read more about Liberated Africans (https://journals.openedition.org/slaveries/2717)</a></li>
                        </ol>
                    </div>
                </div>
                <!--Your code ends here-->
            </div>
        </div>
        <!-- End Page Content-->
      </div>
      <div class="">
        <?php require_once("footer.php"); ?>
      </div>
    </section>
  </body>
</html>
