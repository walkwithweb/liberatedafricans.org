<!--Filter Start-->
<div class="pt-4">
    <div class="card py-2 p-3 mb-5 bg-white rounded">
        <h5 class="card-header">Filter</h5>
        <div class="card-body">
            <form id="filter" method="GET" autocomplete="off">
                <h5 class="fltr-hd">Set Event Date Range:</h5>
                <div class="c-padding-15px mb-5">
                    <div class="form-group">
                        <?php
                        $start_date = 1800;
                        $end_date = 2000;
                        $query = $conn->query("SELECT MIN(Field2) as `startdate`, MAX(Field2) as `enddate` FROM `person` WHERE Field2 != '' AND Field2 != '0' and `online` = '1';");
                        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        $start_date = $row['startdate'];
                        $end_date = $row['enddate'];
                        } ?>
                        <div class="row px-3">
                            <div class="form-group col-12">
                                <input type="hidden" id="from" name="from" value="<?php echo $start_date; ?>" />
                                <input type="hidden" id="to" name="to" value="<?php echo $end_date; ?>"/>
                                <div id="slider" class="lSlider mb-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Filter Court-->
                <hr style="border: 0.5px solid var(--orange);">
                <h5 class="fltr-hd pt-3">Select Court(s):</h5>
                <div class="c-padding-15px">
                    <div class="form-group">
                        <?php
                        $gdepartments = array();
                        $governmentFilter = array();
                        $query = $conn->query("SELECT Field6, CV_Govt_Departments.Name as Dept FROM person left join CV_Govt_Departments on (CV_Govt_Departments.ID = person.Field6) where `online` = '1' group by Field6 order by Dept");
                        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                            $gid = $data['Field6'];
                            $dept = $data['Dept'];
                            $gdepartments[$gid] = $dept; 
                        } 
                        $query = $conn->query("SELECT MIN(Field6) as Field6 , Field7, CV_Court_Names.Name as cName FROM person 
                        left join CV_Court_Names on (CV_Court_Names.ID = person.Field7) where `online` = '1' Group by Field7 Order by cName ASC;");
                        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                            $theArr = array();
                            $gid = $data['Field6'];
                            $cid = $data['Field7'];
                            $cname = $data['cName'];
                            $temp = array('cid' => $cid, 'cname' => $cname); 
                            if(array_key_exists($gid,$governmentFilter)){
                            // check for key and add to existing group for that dept.
                            $theArr = $governmentFilter[$gid];
                            $theArr[] = $temp;
                            $governmentFilter[$gid] = $theArr;
                            } else {
                            // or add a new group 
                            $theArr[] = $temp;
                            $governmentFilter[$gid] = $theArr;
                            }
                        }
                        ?>
                        <div class="row">
                            <div class="col-12">
                                <ul id="governmentFilter" class="case-filters parentFltr">
                                <?php 
                                    foreach($gdepartments as $key => $val){
                                ?>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input tOption" type="checkbox" value="<?php echo $key; ?>" id="<?php echo 'g-' . $key; ?>" data-key="gFltr" data-flag="false">
                                            <label class="form-check-label tOption" for="<?php echo 'g-' . $key; ?>">
                                            <?php echo $val; ?>
                                            </label>
                                            <ul class="case-filters">
                                            <?php 
                                                $thisDeptsCourts = array();
                                                $thisDeptsCourts = $governmentFilter[$key];
                                                if(is_countable($thisDeptsCourts)):
                                                $gln = count($thisDeptsCourts);
                                                for($i = 0; $i < $gln; $i++){
                                                    $arr = $thisDeptsCourts[$i];
                                                    $arrCID = $arr['cid'];
                                                    $arrcName = $arr['cname'];   
                                                    ?>
                                                    <li>
                                                        <div class="form-check">
                                                        <input class="form-check-input subOption <?php echo 'sub-g-'.$key; ?>" type="checkbox" value="<?php echo $arrCID; ?>" id="<?php echo 'pg-' . $arrCID; ?>" data-parent="<?php echo 'g-' . $key; ?>">
                                                        <label class="form-check-label subOption" for="<?php echo 'pg-' . $arrCID; ?>">
                                                            <?php echo $arrcName; ?>
                                                        </label>
                                                        </div>
                                                    </li>
                                                    <?php
                                                }
                                                endif;
                                            ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php
                                    }
                                ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Place Filter-->
                <hr style="border: 0.5px solid var(--orange);">
                <div class="c-padding-15px pt-3">
                    <div class="form-group">
                        <?php
                        // Departure Filter -------------------------------------------------------------------------------
                        $dregions = array();
                        $departureFilter = array();
                        $query = $conn->query("SELECT Field12, CV_Places.Name as Region FROM person left join CV_Places on (CV_Places.ID = person.Field12) where `online` = '1' group by Field12 order by Region ASC");
                        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                            $rid = $data['Field12'];
                            $region = $data['Region'];
                            $dregions[$rid] = $region;
                        } 
                        $query = $conn->query("SELECT MIN(Field12) as RID, Field13 as PID, CV_Places.Name as Place FROM person left join CV_Places on (CV_Places.ID = person.Field13) where `online` = '1' group by Field13 Order by Place ASC");
                        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                            $theArr = array();
                            $rid = $data['RID'];
                            $pid = $data['PID'];
                            $place = $data['Place'];
                            $temp = array('pid' => $pid, 'place' => $place); 
                            if(array_key_exists($rid,$departureFilter)){
                            $theArr = $departureFilter[$rid];
                            $theArr[] = $temp;
                            $departureFilter[$rid] = $theArr;
                            } else {
                            $theArr[] = $temp;
                            $departureFilter[$rid] = $theArr;
                            }
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="fltr-hd">Departure From:</h5>
                                <ul id="departFilter" class="case-filters parentFltr">
                                    <?php 
                                        foreach($dregions as $key => $val){
                                            if($val!=''):
                                    ?>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input tOption" type="checkbox" value="<?php echo $key; ?>" id="<?php echo 'd-' . $key; ?>" data-key="dFltr" data-flag="false">
                                            <label class="form-check-label tOption" for="<?php echo 'd-' . $key; ?>">
                                            <?php echo $val; ?>
                                            </label>
                                            <ul class="case-filters">
                                            <?php 
                                                $thisRegionsPorts = array();
                                                if(array_key_exists($key,$departureFilter)){
                                                    $thisRegionsPorts = $departureFilter[$key];
                                                    if(is_countable($thisRegionsPorts)):
                                                        for($i = 0; $i < count($thisRegionsPorts); $i++){
                                                            $arr = $thisRegionsPorts[$i];
                                                            $arrPID = $arr['pid'];
                                                            $arrPlace = $arr['place'];  
                                                            if($arrPlace!=''): 
                                                            ?>
                                                            <li>
                                                                <div class="form-check">
                                                                <input class="form-check-input subOption <?php echo 'sub-d-'.$key; ?>" type="checkbox" value="<?php echo $arrPID; ?>" id="<?php echo 'pd-' . $arrPID; ?>" data-parent="<?php echo 'd-' . $key; ?>">
                                                                <label class="form-check-label subOption" for="<?php echo 'pd-' . $arrPID; ?>">
                                                                    <?php echo $arrPlace; ?>
                                                                </label>
                                                                </div>
                                                            </li>
                                                            <?php
                                                            endif;
                                                        }
                                                    endif;
                                                }
                                                
                                            ?>
                                            </ul>
                                        </div>
                                    </li>
                                <?php
                                            endif;
                                    }
                                ?>
                                </ul>
                            </div>
                            <?php
                            // Arrival Filter -----------------------------------------------------------------------------
                            $aregions = array();
                            $arrivalFilter = array();
                            $query = $conn->query("SELECT Field26, CV_Places.Name as Region FROM person left join CV_Places on (CV_Places.ID = person.Field26)  where `online` = '1' group by Field26 order by Region ASC");
                            while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                                $rid = $data['Field26'];
                                $region = $data['Region'];
                                $aregions[$rid] = $region;
                            } 
                            $query = $conn->query("SELECT MIN(Field26) as RID, Field27 as PID, CV_Places.Name as Place FROM person left join CV_Places on (CV_Places.ID = person.Field27)  where `online` = '1' group by Field27 order by Place ASC");
                            while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                                $theArr = array();
                                $rid = $data['RID'];
                                $pid = $data['PID'];
                                $place = $data['Place'];
                                $temp = array('pid' => $pid, 'place' => $place); 
                                if(array_key_exists($rid,$arrivalFilter)){
                                $theArr = $arrivalFilter[$rid];
                                $theArr[] = $temp;
                                $arrivalFilter[$rid] = $theArr;
                                } else {
                                $theArr[] = $temp;
                                $arrivalFilter[$rid] = $theArr;
                                }
                            }
                            ?>
                            <div class="col-md-6">
                                <h5 class="fltr-hd">Arrival At:</h5>
                                <ul id="departFilter" class="case-filters parentFltr">
                                    <?php 
                                        foreach($aregions as $key => $val){
                                            if($val!=''):
                                    ?>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input tOption" type="checkbox" value="<?php echo $key; ?>" id="<?php echo 'a-' . $key; ?>" data-key="aFltr" data-flag="false">
                                            <label class="form-check-label tOption" for="<?php echo 'a-' . $key; ?>">
                                            <?php echo $val; ?>
                                            </label>
                                            <ul class="case-filters">
                                            <?php 
                                                $thisRegionsPorts = array();
                                                if(array_key_exists($key,$arrivalFilter)){
                                                    $thisRegionsPorts = $arrivalFilter[$key];
                                                    if(is_countable($thisRegionsPorts)):
                                                        for($i = 0; $i < count($thisRegionsPorts); $i++){
                                                            $arr = $thisRegionsPorts[$i];
                                                            $arrPID = $arr['pid'];
                                                            $arrPlace = $arr['place'];   
                                                            if($arrPlace!=''):
                                                            ?>
                                                            <li>
                                                                <div class="form-check">
                                                                <input class="form-check-input subOption <?php echo 'sub-a-'.$key; ?>" type="checkbox" value="<?php echo $arrPID; ?>" id="<?php echo 'pa-' . $arrPID; ?>" data-parent="<?php echo 'a-' . $key; ?>">
                                                                <label class="form-check-label subOption" for="<?php echo 'pa-' . $arrPID; ?>">
                                                                    <?php echo $arrPlace; ?>
                                                                </label>
                                                                </div>
                                                            </li>
                                                            <?php
                                                            endif;
                                                        }
                                                    endif;
                                                }
                                            ?>
                                            </ul>
                                        </div>
                                    </li>
                                    <?php
                                        endif;
                                        }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Filter Buttons-->
                <hr style="border: 0.5px solid var(--orange);">
                <div class="c-align-right mt-5 py-3">
                    <input class="btn reset-btn" type="reset" value="Reset" id="resetForm" />
                    <input class="btn search-btn" type="button" value="filter" id="filterForm" />
                </div>
            </form>
        </div>
    </div> 
</div>
<!--End Filter Tab-->