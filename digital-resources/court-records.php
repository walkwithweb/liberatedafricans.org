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
                    <h1 class="pageTitle">Court Records</h1>
                    <hr class="pageTitleBorder">
                </div>
            </div>
            <!--Start Page Content-->
            <div class="row justify-content-center align-items-center">
                <div id="main-content" class="col-11 mb-5">
                    <!--Your code starts here-->
                    <div class="row mt-4">
                        <div id="body-container" class="col-12 text-center">
                            <h1 class="text-center" style="font-weight:bold;">Court Records
                                <?php if (isset($_GET['mt'])) {
                                    echo ": " . $_GET['tl'] . " - " . $_GET['td'];
                                } ?>
                            </h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card cr-filter mb-5 mt-4 rounded">
                                <h5 class="card-header">Filter</h5>
                                <div class="card-body">
                                    <form id="filter" method="GET" action="court-records.php" autocomplete="off">
                                        <div class="c-padding-15px">
                                            <div class="form-group">
                                                <label for="publication">Set Event Date Range:</label>
                                                <?php
                                                $start_date = 1700;
                                                $end_date = 2000;
                                                $query = $conn->query("SELECT MIN(Field5) as `startdate`, MAX(Field5) as `enddate` FROM `object` WHERE Field5 != '' AND Field5 != '0';");
                                                while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                                                    $sdate = $row['startdate'];
                                                    $edate = $row['enddate'];

                                                    $sArr = explode("-", $sdate);
                                                    $eArr = explode("-", $edate);

                                                    $start_date = $sArr[0];
                                                    $end_date = $eArr[0];

                                                } ?>
                                                <div class="row px-3">
                                                    <div class="form-group col-12">
                                                        <input type="hidden" id="from" name="from"
                                                            value="<?php echo $start_date; ?>" />
                                                        <input type="hidden" id="to" name="to"
                                                            value="<?php echo $end_date; ?>" />
                                                        <div id="slider" class="lSlider mb-3"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="publication">Select Court Name</label>
                                                <select name="court" id="court" placeholder="Select Court" 
                                                class='form-control selectpicker' data-live-search="true">
                                                    <?php
                                                    $query = $conn->query("SELECT DISTINCT Field11 FROM object WHERE Field11 IS NOT NULL AND Field11 <> '';");

                                                    while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $select = "";
                                                        if (isset($_GET['court']) && !empty($_GET['court'] && in_array($data['Field11'], $_GET['court']))) {
                                                            $select = " selected";
                                                        }
                                                        echo "<option class='form-control' value='" . $data['Field11'] . "' $select>" . $data['Field11'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="form-group pt-3">
                                                <label for="publication">Select Place</label>
                                                <select name="place" id="place" placeholder="Select place"
                                                    class='form-control selectpicker' data-live-search="true">
                                                    <?php
                                                    $query = $conn->query("SELECT distinct Field27 FROM object WHERE Field27 IS NOT NULL AND Field27 <> '';");
                                                    
                                                    while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                                                        $select = "";
                                                        if (isset($_GET['place']) && !empty($_GET['place'] && in_array($data['Field27'], $_GET['place']))) {
                                                            $select = " selected";
                                                        }
                                                        echo "<option class='form-control' value='" . $data['Field27'] . "' $select>" . $data['Field27'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="c-align-right pt-3">
                                                <input class="btn reset-btn" type="reset" value="Reset" id="reset"
                                                    style="border:1px solid #ced4da" />
                                                <input class="btn LA_button_color search-btn" type="submit"
                                                    value="Search" />
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row" id="pageStats">
                                <div class="col-lg-6">
                                    <p id="pShowing">Showing <span id="byRowStart"></span> - <span
                                            id="recordsTo"></span> of <span id="recordsTotal"></span> Records</p>
                                </div>
                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="col-12 text-end">
                                            <p class="pagnumber">Page <span id="currentPage"></span> of <span
                                                    id="pagesTotal"></span></p>
                                        </div>
                                        <div class="col-12 text-end" style="padding-right: 25px;">
                                            <button id="crd-btn" class="btn view-btn mx-1"><i class="fas fa-th"></i>
                                                Card View</button>
                                            <button id="tbl-btn" class="btn view-btn mx-1"><i class="fas fa-list"></i>
                                                Table View</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="content"></div>
                        </div>
                        <div class="col-12">
                            <div id="paginator"></div>
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
    <script>
        $(document).ready(function () {
            $('.reset-btn').on('click', function (e) {
                window.location.reload();
            });

            var returnedTotal = 0;
            var foundObjects = false;
            var isFilteredClicked = false;
            var pCount = 1;
            var formData;
            var cPage;
            var startFrom = 0;
            var toRow = 0;

            var pag = $('#paginator').simplePaginator({
                totalPages: pCount,
                currentPage: 1,
                clickCurrentPage: true,
                pageChange: function (page) {
                    $('#currentPage').html(page);
                    <?php if (isset($_GET['mt'])) { ?>
                        var fData = {
                            'ftr': '2',
                            'colName': <?php echo '"' . $_GET['colName'] . '"'; ?>,
                            'sTerm': <?php echo "'" . $_GET['sTerm'] . "'"; ?>
                        }
                        $.ajax({
                            url: "pagination_courtrecords.php",
                            method: "POST",
                            dataType: "json",
                            data: fData
                        }).done(function (data) {
                            $('#content').html(data[0]);
                            foundObjects = data[1];
                            returnedTotal = data[2];
                            startFrom = data[3];
                            toRow = data[4];
                            pCount = data[5];
                            cPage = data[6];
                            //console.log(data[7]);
                            updatePage();
                        }).fail(function (data) {
                            alert("Could not reach server, please try again later.");
                            console.log(data[7]);
                        });
                    <?php } else { ?>
                        console.log("Default page");
                        if (isFilteredClicked) {
                            formData['page'] = page;
                            $.ajax({
                                url: "pagination_courtrecords.php",
                                method: "POST",
                                dataType: "json",
                                data: formData
                            }).done(function (data) {
                                $('#content').html(data[0]);
                                foundObjects = data[1];
                                returnedTotal = data[2];
                                startFrom = data[3];
                                toRow = data[4];
                                pCount = data[5];
                                cPage = data[6];
                                updatePage();
                                //console.log(data[7]);
                            }).fail(function (data) {
                                alert("Could not reach server, please try again later.");
                                console.log(data[7]);
                            });
                        } else {
                            $.ajax({
                                url: "pagination_courtrecords.php",
                                method: "POST",
                                dataType: "json",
                                data: { page: page }
                            }).done(function (data) {
                                $('#content').html(data[0]);
                                foundObjects = data[1];
                                returnedTotal = data[2];
                                startFrom = data[3];
                                toRow = data[4];
                                pCount = data[5];
                                cPage = data[6];
                                updatePage();
                                //console.log(data[7]);
                            }).fail(function (data) {
                                alert("Could not reach server, please try again later.");
                                console.log(data[7]);
                            });
                        }
                    <?php } ?>
                }
            });

            function scrollToTop() {
                document.body.scrollTop = 0; // For Safari
                document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
            }

            /* Default Page Load */
            <?php if (isset($_GET['mt'])) { ?>
                console.log("Sent here");
                var fData = {
                    'ftr': '2',
                    'colName': <?php echo '"' . $_GET['colName'] . '"'; ?>,
                    'sTerm': <?php echo "'" . $_GET['sTerm'] . "'"; ?>
                }
                $.ajax({
                    url: "pagination_courtrecords.php",
                    method: "POST",
                    dataType: "json",
                    data: fData
                }).done(function (data) {
                    $('#content').html(data[0]);
                    foundObjects = data[1];
                    returnedTotal = data[2];
                    startFrom = data[3];
                    toRow = data[4];
                    pCount = data[5];
                    cPage = data[6];
                    console.log(data[7]);
                    updatePage();
                }).fail(function (data) {
                    alert("Could not reach server, please try again later.");
                    console.log(data[7]);
                });
            <?php } else { ?>
                console.log("Default page");
                $.ajax({
                    url: "pagination_courtrecords.php",
                    method: "POST",
                    dataType: "json",
                    data: {}
                }).done(function (data) {
                    $('#content').html(data[0]);
                    foundObjects = data[1];
                    returnedTotal = data[2];
                    startFrom = data[3];
                    toRow = data[4];
                    pCount = data[5];
                    cPage = data[6];
                    console.log(data[7]);
                    updatePage();
                }).fail(function (data) {
                    alert("Could not reach server, please try again later.");
                    console.log(data[7]);
                });
            <?php } ?>

            function updatePage() {
                if (foundObjects) {
                    /* Update the page indicators at the top of results */
                    if (toRow > returnedTotal) {
                        $("#recordsTo").html(returnedTotal);
                    } else {
                        $('#recordsTo').html(toRow);
                    }
                    $('#byRowStart').html(startFrom);
                    $('#recordsTotal').html(returnedTotal);
                    $('#currentPage').html(cPage);
                    $('#pagesTotal').html(pCount);
                    pag.simplePaginator('setTotalPages', pCount);
                    $('#pageStats').css('display', 'flex');
                    $('#crd-view').css('display', 'flex');
                    $('#tbl-view').css('display', 'none');
                    $('#paginator').css('display', 'block');
                } else {
                    $('#pageStats').css('display', 'none');
                    $('#crd-view').css('display', 'none');
                    $('#tbl-view').css('display', 'none');
                    $('#paginator').css('display', 'none');
                }
                scrollToTop();
            }

            $('#tbl-btn').on('click', function (e) {
                $('#crd-view').css('display', 'none');
                $('#tbl-view').css('display', 'flex');
            });

            $('#crd-btn').on('click', function (e) {
                $('#tbl-view').css('display', 'none');
                $('#crd-view').css('display', 'flex');
            });

            /* Initiate slider for dates */
            var minV = <?php echo $start_date; ?>;
            var maxV = <?php echo $end_date; ?>;
            var slider = document.getElementById('slider');
            noUiSlider.create(slider, {
                start: [minV, maxV],
                connect: true,
                tooltips: true,
                step: 1,
                format: wNumb({
                    decimals: 0
                }),
                range: {
                    'min': minV,
                    'max': maxV
                }
            }).on('slide', function (e) {
                // console.log(e);
                // Update the values 
                $('#from').val(e[0]);
                $('#to').val(e[1]);
            });

            $("form").submit(function (e) {
                var fromDate;
                var toDate;

                formData = {
                    'ftr': '1',
                    'fromDate': $('#from').val(),
                    'toDate': $('#to').val(),
                    'court': $('#court').val(),
                    'place': $('#place').val()
                }

                console.log(formData);

                isFilteredClicked = true;
                pag.simplePaginator('changePage', 1);
                scrollToTop();

                e.preventDefault(); // Prevent default form submit behaviour
            });

        });
    </script>
</body>

</html>