<script type="text/javascript">
    $(document).ready(function(){
    var checkboxes = document.querySelectorAll('input.subOption');
    var checkall = document.querySelectorAll('input.tOption'); // document.getElementById('theID');
    
    /* Attach a listener and link to parent */
    for(var i = 0; i < checkboxes.length; i++){
        checkboxes[i].onclick = function(){
        var parentCheck = this.getAttribute("data-parent"); // get the parent key e.g d-11 or a-11
        var childChecks = document.querySelectorAll('[data-parent="'+parentCheck+'"]'); // get all the children of that parent
        var checkedCount = document.querySelectorAll('input.sub-'+parentCheck+':checked').length;
        var theParent = document.getElementById(parentCheck);
        //console.log("checked count: " + checkedCount);
        theParent.checked = checkedCount > 0;
        theParent.indeterminate = checkedCount > 0 && checkedCount < childChecks.length;
        if(theParent.checked){
            theParent.classList.add("checked");
        } else {
            theParent.classList.remove("checked");
        }
        if(theParent.indeterminate){
            // Add a class to handle for Safari browser when indeterminate
            theParent.dataset.flag = true;
            //theParent.classList.add("checked");
        } else {
            theParent.dataset.flag = false;
            //theParent.classList.remove("checked");
        }
        //console.log("P checeked: " + theParent.checked);
        //console.log("I indeterminate " + theParent.indeterminate);
        }
    }

    /* Update all the children - for all? */ 
    for(var i = 0; i < checkall.length; i++){
        checkall[i].onclick = function(){
            if(this.checked){
                this.dataset.flag = false; // Not indeterminate
                this.classList.add("checked");
            } else {
                this.classList.remove("checked");
                this.dataset.flag = false; // Not indeterminate
            }
            
            var regionID = this.getAttribute("id");// get id like d-11 or a-11
            var childChecks = document.querySelectorAll('[data-parent="'+regionID+'"]');
            for(var j=0; j<childChecks.length; j++){
                childChecks[j].checked = this.checked;
            }
        }
    }

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
        },
        pips: {
        mode: 'positions',
        values: [0,25,50,75,100],
        density: 4
        }
    }).on('slide', function(e){
        // console.log(e);
        // Update the values 
        $('#from').val(e[0]);
        $('#to').val(e[1]);
    });

    });
</script>