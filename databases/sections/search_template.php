<!--Input Template-->
<script type="text/html" id="inputTemplate">
    <input type="text" class="form-control mb-3 mt-2" id="row_st" name="row_st" placeholder="Enter text here">
</script>
<!--Radio Template-->
<script type="text/html" id="radioTemplate">
    <!--Radio Input-->
</script>
<!--Single Dropdown Select Template-->
<script type="text/html" id="sinSelectTemplate">
    <div id="sSinSelect_" class="select-container">
    <select id="row_st" name="row_st" class="row_st form-select mb-3 mt-2" aria-label=".form-select-lg">
        <!--<option></option> updated by javascript-->
    </select>
    </div>
</script>
<!--Multiple Dropdown Select Template-->
<script type="text/html" id="mulSelectTemplate">
    <div id="sMulSelect_" class="select-container">
    <select id="row_st" name="row_st[]" class="row_st form-select mb-3 mt-2" multiple>
        <!--<option></option> updated by javascript-->
    </select>
    </div>
</script>
<!--Advanced Search Default Row Template-->
<script type="text/html" id="advancedSearchRowTemplate">
    <div class="row">
    <div class="col-lg-1">
        <div class="form-group">
        <label for="row_op" class="fltr-hd">Operator</label>
        <div class="select-container">
            <select id="row_op" name="row_op" class="form-select mb-3 mt-2">
            <option>AND</option>
            <option>OR</option>
            <option>NOT</option>
            </select>
        </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
        <label for="row_si" class="fltr-hd">Search Index</label>
        <div class="select-container">
            <select id="row_si" name="row_si" class="row_si form-select mb-3 mt-2">
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
                <select id="row_cnd" name="row_cnd" class="row_cnd form-select mb-3 mt-2">
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
        <div id="fg_st" class="form-group">
        <label for="row_st" class="fltr-hd">Search Term</label>
        <input type="text" class="form-control mb-3 mt-2" id="row_st" name="row_st" placeholder="Enter text here">
        </div>
    </div>
    
    </div>
</script>