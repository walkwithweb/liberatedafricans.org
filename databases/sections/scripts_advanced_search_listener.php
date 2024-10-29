<script type="text/javascript">
    $(document).ready(function(){
        /* 
         * Handle adding rows to the advanced search 
         */ 
        var cnt = <?php echo $addRowsCount; ?>;
        $("#addARowBtn").on("click", function() {
          if(cnt < 10){
            cnt += 1;
            var template = $("#advancedSearchRowTemplate").html();
            $("#ad-form").append(template);
            $("select#row_si").attr("id","row_si_"+cnt); // Update the counter and id
            $("select#row_op").attr("id","row_op_"+cnt); // Update the counter and id
            $("select#row_cnd").attr("id","row_cnd_"+cnt); // Update the counter and id

            $("input#row_st").attr("id","row_st_"+cnt); // Update the counter and id
            $("div#fg_st").attr("id","fg_st_"+cnt); // Update the counter and id

            $("#addARowBtn").prop('disabled',false);
            if(cnt>1){
              $("#removeARowBtn").prop('disabled',false);
            }
          } else if(cnt == 10){
            $("#addARowBtn").prop('disabled',true);
          }
          console.log("count is now " + cnt);
        });

        $("#removeARowBtn").on("click", function() {
          if(cnt > 1 && cnt <= 10){
            cnt -= 1;
            var template = $("#advancedSearchRowTemplate").html();
            $("div#ad-form").children().last().remove();
            $("#removeARowBtn").prop('disabled',false);
            $("#addARowBtn").prop('disabled',false);
          } else if(cnt == 1){
            $("#removeARowBtn").prop('disabled',true);
          }
          console.log("count is now "+cnt);
        });

        /*
          Handle updating dropdowns for single selects - first get the ID...
         */
        <?php
          $jsCVs = json_encode($controlledVocab);
          $jsrowsCVs = json_encode($rowsCV);
          echo "var jsControlledVocab = " . $jsCVs . ";\n"; // All the CVs
          echo "var jsMap = " . $jsrowsCVs . ";\n"; // The map table
        ?>

        // When clicked update the select if it is a CV...
        $(document).on('change','.row_si',function(){
        //$('.row_si').on("change", function(){
          var theID = $(this).attr('id');
          var theInd = theID.slice(7); // row_si_1 ... row_si_10
          var selectedID = $(this).val(); // the colName ... check if in $controlledVocab
          if(selectedID in jsControlledVocab){
            $('input#row_st_'+theInd).remove();
            var cvArr = jsControlledVocab[selectedID]; // Get the CVs for e.g for the table related to 'Field3'
            var cvMap = jsMap[selectedID]; // Get the row from map table e.g where ColumnName is 'Field3'
            var fType = cvMap['FieldType'];
            if(fType=="dropdown-CV" || fType=="dropdown-CV-multi"){
              // Check the select isn't there before adding
              if($("#sSinSelect_"+theInd).length == 0){
                // the select doesn't exit...add it...
                // Attach the select
                var template = $("#sinSelectTemplate").html();
                $("div#fg_st_"+theInd).append(template); // Try with just the select div#fg_st_
                $("#sSinSelect_").attr("id","sSinSelect_"+theInd); // Update the counter and id
                $("select#row_st").attr("id","row_st_"+theInd); // Update the counter and id
              } else {
                // the select exists ... clear it and update
                $("select#row_st_"+theInd).children().remove();
              }
              // Single Select
              for(var key in cvArr){
                var temp = cvArr[key];
                var id = temp['ID'];
                var name = temp['Name'];
                var tOption = '<option id="'+id+'" class="selected-item" value="'+id+'">'+name+'</option>';
                $("select#row_st_"+theInd).append(tOption);
              }
              showInputs("sin",theInd);
            } 
          } else {
            initiate_search_box(theInd);
            showInputs("def",theInd); // TO-DO also pass the index.
          }
        });

        /*
          Set the default value for dropdowns
        */
        function initiate_dropdown_value(theInd){
          $('input#row_st_'+theInd).attr('value','0');
        }

        /*
        Reset the default value for search box
        */
        function initiate_search_box(theInd){
          // Check the select isn't there before adding
          if($("input#row_st_"+theInd).length == 0){
            // the input doesn't exit...add it...
            // Remove the select
            $("#sSinSelect_"+theInd).remove();
            $("#sMulSelect_"+theInd).remove();
            // Re-attach the input
            var template = $("#inputTemplate").html();
            $("div#fg_st_"+theInd).append(template); // Try with just the select div#fg_st_
            $("input#row_st").attr("id","row_st_"+theInd); // Update the counter and id
          }
        }

        /*
          Show Inputs
        */
        function showInputs(stat,theInd){
          // Hide everything
          $("input#row_st_"+theInd).css('display','none');
          $("select#row_st_"+theInd).css('display','none'); $("#sSinSelect_"+theInd).css('display','none');
          $("select#row_st_"+theInd).css('display','none'); $("#sMulSelect_"+theInd).css('display','none');
          // Get what needs to be shown
          switch(stat){
            case "sin":
              $("select#row_st_"+theInd).css('display','flex'); $("#sSinSelect_"+theInd).css('display','flex');
              break;
            case "mul":
              $("select#row_st_"+theInd).css('display','flex'); $("#sMulSelect_"+theInd).css('display','flex');
              break;
            default:
              $("input#row_st_"+theInd).css('display','flex');
          }
        }

    });
</script>