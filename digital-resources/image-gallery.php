<?php
  require_once("../public/head.php");
  require_once("../database.php");
  /* 
   * Listing of Images related to involuntary indenture of enslaved people from Africa 
   */
?>
  <body>
    <section>
      <?php require_once("../public/header.php"); ?>
      <div class="container-fluid content">
                <!--Page Title-->
                <div class="row justify-content-center">
                    <div id="pageTitleContainer" class="col-11 justify-content-start">
                        <h1 class="pageTitle">Image Gallery</h1>
                        <hr class="pageTitleBorder">
                    </div>
                </div>
                <div class="container p-3">
                    <div class="row card-body bg-light">
                      <p><b>Disclaimer:</b><br>
                      The "Liberated Africans" Image Gallery is currently under
                      construction and contains a small sample of images
                      related to the involuntary indenture of enslaved people
                      from Africa. There are many more images which will be
                      uploaded into this gallery in the near future.</p>
                    </div>
                  </div>
                <!--Start Page Content-->
                <div class="row justify-content-center align-items-center">
                    <div id="main-content" class="col-11 mb-5">
                        <!--Your code starts here-->
                        <div class="row py-4 px-3">
                            <div class="portfolio col-lg-12 d-flex justify-content-center">
                                <ul id="portfolio-flters">
                                    <li data-filter="all" class="filter-button">All</li>
                                    <?php
                                    $stmt = $conn->query('SELECT * FROM `CV_Countries` where `Name` !="unknown"');
                                    while ($row = $stmt->fetch()) {
                                        echo ' <li class="filter-button" data-filter="filter-' . str_replace(' ', '', $row['Name']) . '">' . $row['Name'] . '</li>';
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="row mb-5">
                            <!-- <div class="card-columns"> -->
                            <?php
                            $stmt = $conn->query('SELECT * FROM `Upload_RegID` U left join `Gallery` G
                                    on U.Refid = G.id;');
                            $images = $stmt->fetchAll();
                            foreach ($images as $key => $row) {
                                if ($row['FileType'] == 'Image File') {
                                    echo '
                                    <div class="col-md-4 col-lg-3 p-3 filter filter-' . str_replace(' ', '', $row['Country']) . '" >
                                    <a class="gallaryimage" href="#" data-image-id="" type="button" data-bs-toggle="modal" style="color: #4F040C;"
                                    data-title="' . $row['Title'] . '"
                                    data-main="' . htmlspecialchars_decode($row['Main_Text']) . '"
                                    data-more ="' . $row['Read_More'] . '"
                                    data-link="' . $row['HyperLink'] . '"
                                    data-image="' . $row['FileLink'] . '"
                                    data-bs-target="#image-gallery">';

                                    echo '<div class="card sub-card" >
                                            <img class="card-img-top igallery" src="' . $row['FileLink'] . '" alt="' . $row['Title'] . '"/>
                                            <div class="card-body">
                                                <h6 class="card-title"> ' . $row['Title'] . '</h6>
                                            </div>
                                    </div></a></div>';
                                }
                            }
                            ?>
                            <!-- </div> -->
                        </div>
                        <!--Your code ends here-->
                    </div>
                </div>
                <!-- End Page Content-->
                <div class="modal fade" id="image-gallery" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" >
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="btn btn-secondary float-left pr-2" id="show-previous-image"><i class="fa fa-arrow-left"> </i>
                                </button>

                                <button type="button" id="show-next-image" class="btn btn-secondary float-right" style="margin:0 16px 0 8px;"><i class="fa fa-arrow-right"> </i>
                                </button>
                                <h6 class="modal-title" id="image-gallery-title"><span id="title"></span></h6>

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <img id="image-gallery-image" class="img-responsive col-md-12" src="">
                                <div class="row">
                                    <div class="col-sm-12 p-3 pl-4 text-center">
                                        <a id="link" target="_blank" href="" class="btn btn-outline-danger">More Info</a>
                                        <a id="image-link" href="" target="_blank" ><button class="btn btn-outline-success">Click to Download</button></a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

      </div>
      <div class="">
        <?php require_once("../public/footer.php"); ?>
      </div>
    </section>
    <script src="../assets/js/gallery.js"></script>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {
            $("#load_more").click(function(event) {
                $.ajax({
                    url: 'more_gallery.php',
                    success: function(data) {
                        $('#image_gallery').append(data);
                        var newElements = $('.portfolio-new-item');
                        portfolioIsotope.append(newElements).isotope('appended', newElements);
                        $("#load_more").hide();
                        $('.venobox').venobox({
                            'share': false
                        });
                    }
                });
            });
        });
    </script>
  </body>
</html>
