<div class="row justify-content-center align-items-center">
    <div id="main-content" class="col-11 mb-5" style="padding-left:0;padding-right:0;">
        <!--Content Tabs - Map, Table, Timeline, Summary, Filter--> 
        <ul class="nav nav-tabs" id="map-nav" role="tablist">
            <li id="lnk2" class="nav-item" role="presentation">
                <button class="nav-link active" id="place-tab" data-bs-toggle="tab" data-bs-target="#place" type="button" role="tab" aria-controls="place" aria-selected="true"><i class="fas fa-map-marker-alt"></i> <span class="mobile-hide">Map</span></button>
            </li>
            <li id="lnk1" class="nav-item" role="presentation">
                <button class="nav-link" id="event-tab" data-bs-toggle="tab" data-bs-target="#event" type="button" role="tab" aria-controls="event" aria-selected="false"><i class="fas fa-list"></i> <span class="mobile-hide">Table</span></button>
            </li>
            <li id="lnk3" class="nav-item" role="presentation">
                <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab" aria-controls="timeline" aria-selected="false"><i class="fas fa-chart-line"></i> <span class="mobile-hide">Timeline</span></button>
            </li>
            <li id="lnk5" class="nav-item" role="presentation">
                <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab" aria-controls="summary" aria-selected="false"><i class="far fa-file"></i> <span class="mobile-hide">Summary</span></button>
            </li>
            <li id="lnk4" class="nav-item" role="presentation">
                <button class="nav-link" id="filters-tab" data-bs-toggle="tab" data-bs-target="#filters" type="button" role="tab" aria-controls="filters" aria-selected="false"><i class="fas fa-filter"></i> <span class="mobile-hide">Filter</span></button>
            </li>
        </ul>

        <div class="tab-content DatasetName timeline-body">
            <!--Map Pane--> 
            <div class="tab-pane fade show active" id="place" role="tabpanel" aria-labelledby="place-tab">
                <div id="map"></div>
            </div>
            <div class="tab-pane fade pt-4" id="event" role="tabpanel" aria-labelledby="event-tab">
                <!--Table Pane--> 
                <h4 class="ps-4">Dataset For :&nbsp;  <span id="LocationName"> All</h4>
                <div id="dynamic-content">
                <?php 
                if($isAdvancedSResults): 
                    if($current_page == '2'){
                    $stmt = $conn->prepare("SELECT count(*) FROM person where ".$field_for_totals." != '' AND `online` = '1' " .$advanced_query);
                    } else {
                    $stmt = $conn->prepare("SELECT count(*) FROM person left join CV_Places on (CV_Places.ID = person.".$field_for_totals.") where `online` = '1' " .$advanced_query);
                    }
                else:
                    if($current_page == '2'){
                    $stmt = $conn->prepare("SELECT count(*) FROM person where ".$field_for_totals." != '' AND `online` = '1'");
                    } else {
                    $stmt = $conn->prepare("SELECT count(*) FROM person left join CV_Places on (CV_Places.ID = person.".$field_for_totals.") where `online` = '1'");
                    }
                endif;
                $perPage = 10;
                $stmt->execute();
                $returnedTotal = $stmt->fetchColumn();
                if($returnedTotal=="0"){
                    $totalPages = 1;
                } else {
                    $totalPages = ceil($returnedTotal/$perPage);
                }
                ?>

                <div class="row">
                    <div class="col-lg-6">
                    <p style="padding-left:1.5rem!important;" id="pShowing">Showing <span id="byRowStart"></span> - <span id="recordsTo"></span> of <span id="recordsTotal"></span> Records</p>
                    </div>
                    <div class="col-lg-6">
                    <p class="pagnumber">Page <span id="currentPage"></span> of <span id="pagTotal"> <?php echo $totalPages;?></span></p>
                    </div>
                </div>
                <div class="table-responsive p-3 pt-5 ">
                    <table class="table">
                        <thead>
                            <tr>
                            <th scope="col">UID</th>
                            <th scope="col">Case Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Court Name</th>
                            <th scope="col">Liberated Africans</th>
                            <th scope="col">Registered</th>
                            <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody class="pb-5" id="content"></tbody>
                    </table>
                    <div class="pt-5 pb-3" id="pagination"></div>
                    <div class="row">
                        <div class="col-12 text-end">
                            <label for="pCnt-select">Entries per page:</label>
                            <select id="pCnt-select" class="form-select" autocomplete="off" style="padding:0.375rem 0.75rem; max-width:80px;display:inline;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="250">250</option>
                                <option value="500">500</option>
                                <option value="1000">1000</option>
                            </select>
                            <button id="perPageBtn" class="btn btn-light"><i class="fas fa-sync-alt"></i></button>
                        </div>
                    </div>
                    <input type="hidden" id="totalPages" value="<?php echo $totalPages; ?>">
                </div>
                </div>
            </div>
            <div class="tab-pane fade pt-4" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
                <!--Timeline Pane--> 
                <h1>
                <?php 
                if($current_page=='1'){
                    require_once("timelineOrigins.php");
                } else if($current_page=='2'){
                    require_once("timelineBlockades.php");
                } else if($current_page=='3'){
                    require_once("timeline.php");
                }
                ?>
                </h1>
            </div>
            <div class="tab-pane fade pt-4" id="summary" role="tabpanel" aria-labelledby="summary-tab">
                <!--Summary Pane--> 
                <h4 class="ps-4 pt-5">Summary Statistics</h4>
                <div id="dynamic-content-summary" class="table-responsive p-3 pt-0">
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col"></th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Sum of Cases</th>
                                <td><span id="cases-tot"></span></td>
                            </tr>
                            <!--
                            <tr>
                                <th scope="row">Enslaved Africans</th>
                                <td><span id="enslaved-tot"></span></td>
                            </tr>
                            -->
                            <tr>
                                <th scope="row">Sum of Liberated Africans</th>
                                <td><span id="liberated-tot"></span></td>
                            </tr>
                            <tr>
                                <th scope="row">Sum of Registered Africans</th>
                                <td><span id="registered-tot"></span></td>
                            </tr>
                            <!--
                            <tr>
                                <th scope="row">Unique Courts</th>
                                <td><span id="courts-tot"></span></td>
                            </tr>-->
                        </tbody>
                    </table>
                </div>
                <h5 class="ps-4" style="font-weight:bold;">Summary by Govt. Department - Tot. Liberated Africans</h5>
                <div class="row justify-content-center p-3 pt-1">
                    <div class="col-12">
                        <div class="accordion" id="accordionStatsCourts"></div>
                    </div>
                </div>
                <h5 class="ps-4 mt-3" style="font-weight:bold;">Summary by African Region - Tot. Liberated Africans</h5>
                <div class="row justify-content-center p-3 pt-1">
                    <div class="col-12">
                        <div class="accordion" id="accordionStats"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade pt-4 mx-4" id="filters" role="tabpanel" aria-labelledby="filters-tab">
                <!--Filters Pane--> 
                <ul class="nav nav-tabs" id="subTabs" role="tablist">
                    <li class="nav-item sub-nav" role="presentation">
                        <button class="mb-3 mb-md-0 nav-link sub-nav active" id="search-filter-tab" data-bs-toggle="tab" data-bs-target="#search-filter" type="button" role="tab" aria-controls="search-filter" aria-selected="true">FILTER</button>
                    </li>
                    <li class="nav-item sub-nav" role="presentation">
                        <button class="nav-link sub-nav" id="search-advanced-tab" data-bs-toggle="tab" data-bs-target="#search-advanced" type="button" role="tab" aria-controls="search-advanced" aria-selected="false">ADVANCED SEARCH</button>
                    </li>
                </ul>
                <div class="tab-content" id="subTabsContent">
                    <div class="tab-pane fade show active" id="search-filter" role="tabpanel" aria-labelledby="search-filter-tab">
                        <!--Simple Search Filter UI--> 
                        <?php require 'combined_search_ui.php'; ?>
                    </div>
                    <div class="tab-pane fade" id="search-advanced" role="tabpanel" aria-labelledby="search-advanced-tab">
                        <!--Advanced Search Filter UI--> 
                        <?php 
                        if($current_page=='1'){
                            $current_page_link = "cases-departures.php";
                        } else if($current_page=='2'){
                            $current_page_link = "cases-blockades.php";
                        } else if($current_page=='3'){
                            $current_page_link = "cases.php";
                        }
                        ?>
                        <?php require 'advanced_search_ui.php'; ?>
                    </div>
                </div>
            </div>
        </div>       
    </div>
</div>