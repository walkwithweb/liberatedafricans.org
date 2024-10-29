<!--Filter Start-->
<div class="pt-4">
    <div class="card py-2 p-3 mb-5 bg-white rounded">
        <h5 class="card-header">Advanced Search</h5>
        <div class="card-body">
        <form id="advancedsearch" action="<?php echo $current_page_link; ?>" method="GET" autocomplete="off">

            <div id="pageEnd">
            <!--Fetch from the LA_CourtForm_V1 Table-->
            <?php
                $sql = "SELECT * FROM `LA_CourtForm_V1` WHERE LA_CourtForm_V1.indexpage !='0'";
                $query = $conn->query($sql);
                $resultsH = array();
                $rowsCV = array();
                
                while ($row = $query->fetch(PDO::FETCH_ASSOC)){
                $resultsH[] = $row;
                if($row['FieldType']=="dropdown-CV"){
                    $tID = $row['ColumnName'];
                    $rowsCV[$tID] = $row;
                } else if($row['FieldType']=="dropdown-CV-multi" ){
                    $tID = $row['ColumnName'];
                    $rowsCVMulti[] = $tID;
                }
                }
            ?>

            <!-- Find the controlled vocabularies -->
            <?php
                $controlledVocab = array();
                $lnCV = count($rowsCV);

                foreach($rowsCV as $trow){
                $colName = $trow['ColumnName'];
                $fieldType = $trow['FieldType'];
                if($fieldType!="radio"){
                    // For dropdown-CV and dropdown-CV-multi types
                    $cvTable = $trow['Options']; // Get the table name for the new query
                    // Fetch the CV from each CV table...
                    $sql = "SELECT * FROM " . $cvTable  . " ORDER BY listorder ASC";
                    $query = $conn->query($sql);
                    $tempCV = array();
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)){
                        $tID = $row['ID'];
                        $tempCV[$tID] = $row;
                    }
                    $controlledVocab[$colName] = $tempCV; // combine CV data with field as array key
                    } else {
                    // TO-DO : Explode the yes;no here and set it to be the temp in place of trow
                    $controlledVocab[$colName] = $trow; // handle radio - no CV Table
                    }
                }?>

            <!--Check whether the advanced search param is set or not-->
            <?php
                $lnCNO = 0; // initialize
                $originalQuery = "";
                $asreturnedTotal = 0;
                $row_op = array();
                $row_si = array();
                $row_st = array();
                $row_cnd = array();
                $cnamesOnly = array();
                $cdefinitions = array();
            ?>

                <div id="ad-form">
                <div class="row">
                    <div class="col-lg-1">
                        <div class="form-group">
                            <input id="row_op_1" name="row_op" type="hidden" value="">
                        </div>
                    </div>
                    <div class="col-lg-3">
                    <div class="form-group">
                        <label for="row_si" class="fltr-hd">Search Index</label>
                        <div class="select-container">
                        <select id="row_si_1" name="row_si" class="row_si form-select mb-3 mt-2">
                            <?php
                            $lnH = count($resultsH);
                            for($i=0; $i<$lnH; $i++):
                                $tempH = $resultsH[$i];?>
                                <?php if($i==0):?>
                                <option id="UI" value="UI" class="selected-item">RegID</option>
                                <?php endif; ?>
                                <option id="<?php echo htmlspecialchars_decode($tempH['ColumnName']); ?>" value="<?php echo htmlspecialchars_decode($tempH['ColumnName']); ?>" class="selected-item"><?php echo htmlspecialchars_decode($tempH['display']); ?></option>
                            <?php endfor;?>
                        </select>
                        </div>
                    </div>
                    </div>
                    <div class="col-lg-3">
                    <div class="form-group">
                        <label class="fltr-hd" for="row_cnd">Search Condition</label>
                        <div class="select-container">
                            <select id="row_cnd_1" name="row_cnd" class="row_cnd form-select mb-3 mt-2">
                                <option class="selected-item" id="1" value="1">equals</option>
                                <option class="selected-item" id="2" value="2">not equals</option>
                                <option class="selected-item" id="3" value="3">begins with</option>
                                <option class="selected-item" id="4" value="4">does not begin with</option>
                                <option class="selected-item" id="5" value="5">ends with</option>
                                <option class="selected-item" id="6" value="6">contains</option>
                                <option class="selected-item" id="7" value="7">does not contain</option>
                                <option class="selected-item" id="8" value="8">is blank</option>
                                <option class="selected-item" id="9" value="9">is not blank</option>
                                <option class="selected-item" id="10" value="10">sounds like</option>
                            </select>
                        </div>
                    </div>
                    </div>
                    <div class="col-lg-5">
                    <div id="fg_st_1" class="form-group">
                        <label for="row_st" class="fltr-hd">Search Term</label>
                        <input type="text" class="form-control mt-2 mb-3" id="row_st_1" name="row_st" placeholder="Enter text here">
                    </div>
                    </div>
                    
                </div>
                <!--More Rows Go Here-->
                </div>
                <input id="ad_search" name="ad_search" type="hidden" value="Y">
                <div class="row">
                <div class="col-12 text-end">
                    <button id="removeARowBtn" type="button" class="btn btn-light mt-2 mb-2"><i class="fas fa-minus"></i> Remove a Row</button>
                    <button id="addARowBtn" type="button" class="btn btn-light mt-2 mb-2"><i class="fas fa-plus"></i> Add a Row</button>
                    <button type="reset" class="btn btn-light mt-2 mb-2" style="margin-left:15px;"><i class="fas fa-sync-alt"></i> Clear</button>
                </div>
                </div>

            </div>

            <!--Filter Buttons-->
            <hr style="border: 0.5px solid var(--orange);">
            <div class="c-align-right mt-5 py-3">
            <input class="btn reset-btn" type="reset" value="Reset" id="refreshForm" />
            <button class="btn search-btn" type="submit" id="searchForm">Search</button>
            </div>
        </form>
        </div>
    </div> 
</div>
<!--End Filter Tab-->