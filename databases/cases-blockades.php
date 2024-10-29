<?php
  require_once("../public/head.php");
  require_once("../database.php");
  require_once("advancedsearch.php");
?>
  <body class="home">
    <section id="courts-cases" class="main">
      <?php $addRowsCount = 1; ?>
      <?php require_once("../public/header.php"); ?>
      <div class="container-fluid content courtcases">
        <!--Page Title-->
        <?php 
        $current_page = '2';
        require 'sections/page_title.php'; 
        ?>
        <!--Generate Map Colours--> 
        <?php 
            $fcolors = ["#f28482","#f7b267","#bc4749","#b298dc","#9ceaef","#E9D8A6","#7AB69E","#b1a7a6","#2D00F7","#717744","#99582a", "#5c677d","#95b8d1","#b8b8aa"];            
            $flagColors = array();
            $sql = "SELECT distinct(Field19) FROM `person`";
            $query = $conn->query($sql);
            $cnum = count($fcolors);
            $c = 0;
            while ($row = $query->fetch(PDO::FETCH_ASSOC)){
            $rFlag = $row['Field19'];
            if($rFlag != ''){
                if($c < $cnum){
                    $flagColors[$rFlag] = $fcolors[$c];
                } else {
                    /* If we're out of colors, generate random color */
                    $r = strval(sprintf('#%06X', mt_rand(0,0xFFFFFF)));
                    $flagColors[$rFlag] = $r;
                }
                $c++;
            }
            }
        ?>
        <!--Start Page Content-->
        <?php 
        $field_for_totals = 'Field16'; // X-Capture co-ordinate, should not be blank
        require 'sections/page_content.php';
        ?>
        <!-- End Page Content-->
      </div>
      <div class="">
        <?php require_once("../public/footer.php"); ?>
      </div>
    </section>
    <!--Checkboxes, slider--> 
    <?php require 'sections/scripts_checkboxes_update.php'; ?>

    <script type="text/javascript">
      $(document).ready(function(){
//----------------------------------------------------------------------------------------------------------A[Variables]
        var perPage = 10;        
        var totalPage = parseInt($('#totalPages').val());
        var returnedTotal = <?php echo $returnedTotal;?>;
        var curQuery = '';
        <?php
          if($isAdvancedSResults){
            $get_query = json_encode($advanced_query);
            echo "var get_query = " . $get_query . ";\n"; // The Advanced Query String 
          } else { ?>
            var get_query = '';
        <?php } ?> 
        curQuery = get_query;
        var cPg = 1;
        var isFirstPageLoad = true;

        var pag = $('#pagination').simplePaginator({
          totalPages: totalPage,
          maxButtonsVisible: 5,
          currentPage: 1,
          nextLabel: 'Next',
          prevLabel: 'Prev',
          clickCurrentPage: true,
          pageChange: function(page) {
            cPg = page;
            $('#currentPage').html(page);
            //console.log("current query - page change is: " + curQuery);
            if(page!=1){
              updateTable(curQuery,returnedTotal,totalPage);
            } else {
              if(!isFirstPageLoad){
                updateTable(curQuery,returnedTotal,totalPage);
              } else {
                // For default page load
                updateTable(curQuery,returnedTotal,totalPage);
              }
            }
          }
        });

        // Filter ----------
        var startDate;
        var EndDate;
        var selectedDropdown;

        // Map begins -------
        var latitude;
        var longitude;
        var stats;
        var names;
        var years;
        var captureLocations;
        var captureVesselNames;
        var captureVesselTypes;
        var flags;
        var captains;
        var personIDs;
        var personUIs;
        var liberatedTot;
        var shipStatus;
        var maxLiberated;
        var minEnslaved;

        var flagColors = <?php echo json_encode($flagColors); ?>;
        var flagCountries = {};
        //console.log(flagCountries);
        /* Create layerGroups */
        for(const key in flagColors){
          if(flagColors.hasOwnProperty(key)){
            flagCountries[key] = new L.layerGroup();
          }
        }

        var statusInbound = new L.layerGroup();
        var statusOutbound = new L.layerGroup();
        var placeLayerGrp;
        var marker1,marker;

//--------------------------------------------------------------------------------------------B[ Build Map & Its Variables]
        
        var map = L.map('map').setView([11.0542035978,13.5941134872], 3);
        var numFormat = new Intl.NumberFormat('en-US');

        // Add tile layers - https://leaflet-extras.github.io/leaflet-providers/preview/
        var esriWorldShadedRelief = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Shaded_Relief/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri',
            maxZoom: 13
        });
        esriWorldShadedRelief.addTo(map);

        map.addControl(new L.Control.Fullscreen({
            title: {
                'false': 'View Fullscreen',
                'true': 'Exit Fullscreen'
            }
        }));

        var baseMaps = {
          "Relief": esriWorldShadedRelief
        };

        var overMaps = {
          "Liberated Africans": statusOutbound,
          "Empty Ships": statusInbound
        }

        var legendLayersControl = L.control.layers(null,overMaps,{collapsed:false, position:'bottomright'});
        legendLayersControl.addTo(map);

        statusInbound.addTo(map);
        statusOutbound.addTo(map);

        // Title Info 
        $(".leaflet-control-layers-base").css('display','none');
        $(".leaflet-control-layers-overlays").prepend("<h6 id=\"Ltitle\"><strong>Ship Status</strong></h6>");
        $(".leaflet-control-layers-overlays").append("<hr class=\"lHr\">");
        $(".leaflet-control-layers-overlays").append("<p class=\"lParagraph\"><strong>Countries</strong></p>");
        for (let k in flagColors){
            var c = flagColors[k];
            var v = k.replaceAll(' ',''); // Remove spaces for class naming
            var appendData = "<label><span><input class=\"leaflet-control-layers-selector flagCountries\" type=\"checkbox\" value=\""+v+"\" checked> <span class=\"legendCircle\"><i style=\"background:" + c + "\"></i>" + k + "</span></span></label>";
            $(".leaflet-control-layers-overlays").append(appendData);
        }
        // Add the blank ones. 
        $(".leaflet-control-layers-overlays").append("<label><span><input class=\"leaflet-control-layers-selector flagCountries\" type=\"checkbox\" value=\"Unspecified\" checked> <span class=\"legendCircle\"><i style=\"background:#000000\"></i>Unspecified</span></span></label>");

        $('.flagCountries').change(function(e){
          var country = '.' + this.value;
          if(this.checked){
            //console.log(country + " is checked!");
            const cMrks = document.querySelectorAll(country);
            cMrks.forEach(mkr => {
              mkr.style.visibility = 'visible';
            });
          } else {
            //console.log(country + " is unchecked!");
            const cMrks = document.querySelectorAll(country);
            cMrks.forEach(mkr => {
              mkr.style.visibility = 'hidden';
            });
          }
        });

        // Set checkbox to checked when the layers load ...
        statusInbound.on('add', (e)=>{
          // Show the basemap labels
          const cBxs = document.querySelectorAll('.flagCountries');
          cBxs.forEach(Bx => {
            if(!Bx.checked){
              Bx.click();
            }
          });
        });

        statusOutbound.on('add', (e)=>{
          // Show the basemap labels
          const cBxs = document.querySelectorAll('.flagCountries');
          cBxs.forEach(Bx => {
            if(!Bx.checked){
              Bx.click();
            }
          });
        });

        var firstProjection = 'PROJCS["Google Maps Global Mercator",GEOGCS["WGS 84",DATUM["WGS_1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.01745329251994328,AUTHORITY["EPSG","9122"]],AUTHORITY["EPSG","4326"]],PROJECTION["Mercator_2SP"],PARAMETER["standard_parallel_1",0],PARAMETER["latitude_of_origin",0],PARAMETER["central_meridian",0],PARAMETER["false_easting",0],PARAMETER["false_northing",0],UNIT["Meter",1],EXTENSION["PROJ4","+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs"],AUTHORITY["EPSG","900913"]]';

        var secondProjection = "+proj=longlat +datum=WGS84 +no_defs";

        $("#resetForm").on("click", function(){
          window.location.href = "cases-blockades.php";
        });

        $("#refreshForm").on("click", function(){
          window.location.href = "cases-blockades.php";
        });

//---------------------------------------------------------------------------------------C[Load Data]
        <?php if(!$isAdvancedSResults): ?>
          // Default Load 
          $.ajax({
            url:"mysqlQuery_Blockades.php",
            method:"POST",
            dataType: "json",
            data: {'perPage': perPage},
            async: false,
            success:function(data){
                latitude=data[0];
                longitude=data[1];
                names=data[2];
                years=data[3];
                captureLocations=data[4];
                captureVesselNames=data[5];
                captureVesselTypes=data[6];
                flags=data[7];
                captains=data[8];
                personIDs=data[9];
                personUIs=data[10];
                liberatedTot=data[11];
                shipStatus=data[12];

                stats = data[14];
                curQuery = data[17]; //console.log("current query - default is: " + curQuery);
                returnedTotal = data[21];
                totalPage = data[22];

                $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                $("#accordionStats").html(data[19]);
                $("#accordionStatsCourts").html(data[20]);

                displayMap();
                updateCharts(data[15],data[16]);
                updateTable(curQuery,returnedTotal,totalPage);
                updateTotals();
                isFirstPageLoad = false;
            },
            error: function (request, status, error) {
              alert(request.responseText);
              alert(error);
              alert(status);
            }
          });
        <?php else: ?>
          // Advanced Search
          <?php
            $as_query = json_encode($advanced_query);
            echo "var as_query = " . $as_query . ";\n"; // The Advanced Query String 
          ?>
          //console.log(as_query);
          $.ajax({
            url:"mysqlQuery_Blockades.php",
            method:"POST",
            dataType: "json",
            data: {
              'fltr':"AS",
              'adv-search': as_query,
              'perPage': perPage
            },
            async: false,
            success:function(data){
                latitude=data[0];
                longitude=data[1];
                names=data[2];
                years=data[3];
                captureLocations=data[4];
                captureVesselNames=data[5];
                captureVesselTypes=data[6];
                flags=data[7];
                captains=data[8];
                personIDs=data[9];
                personUIs=data[10];
                liberatedTot=data[11];
                shipStatus=data[12];

                stats = data[14];
                curQuery = data[17]; //console.log("current query - default is: " + curQuery);
                returnedTotal = data[21];
                totalPage = data[22];

                if(!data[18]){
                  $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                  //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                  $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                  //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                  $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                  $("#accordionStats").html(data[19]);
                  $("#accordionStatsCourts").html(data[20]);

                  displayMap();
                  updateCharts(data[15],data[16]);
                  updateTable(curQuery,returnedTotal,totalPage);
                  updateTotals();
                  isFirstPageLoad = false;
                } else {
                  // No results found
                  window.location.href = 'cases-blockades.php?msg=0';
                }
            },
            error: function (request, status, error) {
              alert(request.responseText);
              alert(error);
              alert(status);
            }
          });

        <?php endif; ?>

//---------------------------------------------------------------------------------------C[Load Data - Functions]

        function updateCharts(data, entries){
          window.myBar.data.datasets[0].data = entries;
          window.myBar.data.labels = data; // x-axis 
          window.myBar.update();
        }

        function updateTable(cQry,rTot,tPages){
          $("#content").html('<tr><td colspan="6"><strong>loading...</strong></td></tr>');
          //console.log("Current Query: " + cQry);
          //console.log("Returned Total: " + rTot);
          //console.log("Total Pages: " + tPages);
          //console.log("Current Page : " + cPg);
          
          $.ajax({
            url:"mysqlQuery_Table.php",
            method:"POST",
            dataType: "json",
            data:{'cQuery':cQry,'returnedTotal':rTot,'totalPages':tPages,'page':cPg,'perPage':perPage,'key':"blockades"},
            success:function(data){
              //console.log("Row start " + data[1]);
              $('#content').html(data[0]);
              $('#byRowStart').html(data[1]);
              if (data[2] > rTot) {
                $("#recordsTo").html(rTot);
              } else {
                $('#recordsTo').html(data[2]);
              }
              $('#recordsTotal').html(rTot);
              $('#pagTotal').html(tPages);
              //console.log(tPages + "*");
              // Update the paginator
              if(tPages>0){
                pag.simplePaginator('setTotalPages',tPages);
              }
            },
            error: function (request, status, error) {
              alert(request.responseText);
            }
          });
        }

        function updateTotals(){
          var tot = liberatedTot.length;
          maxLiberated = liberatedTot[0];
          minEnslaved = liberatedTot[tot-1];
          //console.log("Update totals :" + liberatedTot);
        }

        function getBackground(country){
            if(country == '' || country == null){
              //console.log("Found a blank country");
                return '#000000'; 
            } else {
                return flagColors[country]; 
            }
        }

        /* Calculate the radius of the circle based on total liberated - proportional */
        function getPixels(tot){
          var r = Math.sqrt(tot);
          if(r==0){
            return 4;
          } else if (r < 5){
            return 5;
          } else {
            return r;
          }
        }

        function displayMap(){
          map.setView([11.0542035978,13.5941134872], 3);
          $('#place-tab').get(0).click();
          //console.log(flags);
          for (var i = 0; i < latitude.length; i++) {
            var country = flags[i];
            if(country==""){
              var cName = 'Unspecified';
            } else {
              var cName = country.replaceAll(' ','');
            }
            // radius=5; // Radius in pixels - fixed size [5 is base and 10 is max in range]
            radius=getPixels(liberatedTot[i]);
            var coords = proj4(firstProjection,secondProjection,[latitude[i],longitude[i]]);
            var long = coords[0];
            var lat = coords[1];
            var col = getBackground(country);
            marker = L.circleMarker(new L.LatLng(lat, long), {
            className: cName,
            radius: radius,
            color: '#343a40',
            fillColor: col,
            opacity: 0.5,
            fillOpacity: 1,
            weight: 0.5,
            zIndexOffset: -1000
            });
            var lib = '';
            if(liberatedTot[i]==0){
              lib = 'Empty Ship';
            } else {
              lib = liberatedTot[i];
            }

            var content="<h6><b>UID:</b> " + personUIs[i] + "</h6><h6><b>Case:</b> " + names[i] + "</h6><h6><b>Year:</b> " + years[i] + "</h6><h6><b>Country:</b> " + flags[i] + "</h6><h6><b>Capture Location:</b> " + captureLocations[i] + "</h6><h6><b>Liberated Africans:</b> " + lib + "</h6><br>" +'<a class=btnNext style=font-size:18px; target=\"_blank\" href=\"event_details.php?EventID=' + personIDs[i] +'\" id=getEvent>View Details</a>';

            marker.bindPopup(content, {
            'maxWidth': '330',
            'minWidth': '330',
            'minHeight':'250'
            });
            marker.on('click', function (e) {
                    this.openPopup();
            });

            // Place the marker based on ship status... 
            if(shipStatus[i]==2){
              marker.addTo(statusInbound);
            } else if(shipStatus[i]==1) {
              marker.addTo(statusOutbound);
            }
          }
        }

//---------------------------------------------------------------------------------------D[Page Listeners]
        // Update the perPage count 
        $("#perPageBtn").on("click", function(e){
          perPage = $("#pCnt-select").val();
          totalPage = Math.ceil(returnedTotal/perPage);
          pag.simplePaginator('changePage',1); // Go back to start and reupload table rows
          pag.simplePaginator('setTotalPages',totalPage);
        });

        // Combined Date - Court - Place -----------------------------------------------------------------
        $("#filterForm").on("click", function(e){
          // Date Data ------------------------------
          startDate = $("#from").val();
          EndDate = $("#to").val();
          // Place & Court Data ---------------------
          // Get the checkboxes
          var clickedBoxes = document.querySelectorAll('input.tOption.checked');
          ////console.log("Checked parents: " + clickedBoxes.length);
          ////console.log(clickedBoxes);
          var allClickedD = []; var allClickedA = []; var allClickedG = [];
          var allIndeterminateD = []; var allIndeterminateA = []; var allIndeterminateG = [];

          for(var i=0; i < clickedBoxes.length; i++){
            /* Seperate the checkboxes - indeterminate ~ some children not checked vs all children checked
             * D - Departure, A - Arrival G - Govt. Depts & Courts */
            var key = clickedBoxes[i].getAttribute("data-key");
            var v = clickedBoxes[i].getAttribute("value");
            if(clickedBoxes[i].dataset.flag == "true"){
              if(key == "dFltr"){
                allIndeterminateD.push(v);
              } else if (key == "aFltr") {
                allIndeterminateA.push(v);
              } else if (key == "gFltr") {
                allIndeterminateG.push(v);
              }
              ////console.log("I " + v + " Key " + key);
            } else {
              if(key == "dFltr"){
                allClickedD.push(v);
              } else if (key == "aFltr") {
                allClickedA.push(v);
              } else if (key == "gFltr") {
                allClickedG.push(v);
              }
              ////console.log("C " + v + " Key " + key);
            }
          }

          var cvIDs = []; var cvIDsD = []; var cvIDsA = []; var cvIDsG = [];
          /* Get the individual ports based on the indeterminate parent checks*/
          for(var i=0; i <allIndeterminateD.length; i++){
            var rid = allIndeterminateD[i];
            // Get all the children 
            var clickedChildren = document.querySelectorAll('input.sub-d-'+rid+':checked');
            for(var j=0; j < clickedChildren.length; j++){
              var v = clickedChildren[j].getAttribute("value");
              cvIDsD.push(v);
              ////console.log("PD- " + v);
            }
          }

          for(var i=0; i <allIndeterminateA.length; i++){
            var rid = allIndeterminateA[i];
            // Get all the children 
            var clickedChildren = document.querySelectorAll('input.sub-a-'+rid+':checked');
            for(var j=0; j < clickedChildren.length; j++){
              var v = clickedChildren[j].getAttribute("value");
              cvIDsA.push(v);
              ////console.log("PA- " + v);
            }
          }

          for(var i=0; i <allIndeterminateG.length; i++){
            var gid = allIndeterminateG[i];
            // Get all the children 
            var clickedChildren = document.querySelectorAll('input.sub-g-'+gid+':checked');
            for(var j=0; j < clickedChildren.length; j++){
              var v = clickedChildren[j].getAttribute("value");
              cvIDsG.push(v);
              ////console.log("PG- " + v);
            }
          }
          // Maintain index/order for sql query Departure - [0], Arrival - [1], Govt. Dept - [2] 
          allClicked = [allClickedD, allClickedA, allClickedG];
          cvIDs = [cvIDsD,cvIDsA,cvIDsG];

          // Clear Previous Data
          statusInbound.clearLayers();
          statusOutbound.clearLayers();
          $.ajax({
            url:"mysqlQuery_Blockades.php",
            method:"POST",
            dataType: "json",
            data: {
              'fltr':"All",
              'perPage': perPage,
              'startDate':startDate, 
              'EndDate':EndDate, 
              'allClicked':allClicked,
              'cvIDs':cvIDs
            },
            async: false,
            success:function(data){
                latitude=data[0];
                longitude=data[1];
                names=data[2];
                years=data[3];
                captureLocations=data[4];
                captureVesselNames=data[5];
                captureVesselTypes=data[6];
                flags=data[7];
                captains=data[8];
                personIDs=data[9];
                personUIs=data[10];
                liberatedTot=data[11];
                shipStatus=data[12];

                //console.log(data[13]);
                stats = data[14];
                curQuery = data[17]; //console.log("current query - filtered is: " + curQuery);
                returnedTotal = data[21];
                totalPage = data[22];

                if(!data[18]){
                  $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                  //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                  $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                  //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                  $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                  $("#accordionStats").html(data[19]);
                  $("#accordionStatsCourts").html(data[20]);

                  displayMap();
                  updateCharts(data[15],data[16]);
                  updateTable(curQuery,returnedTotal,totalPage);
                  updateTotals();
                  isFirstPageLoad = false;
                } else {
                  // No results found
                  window.location.href = 'cases-blockades.php?msg=0';
                }
            },
            error: function (request, status, error) {
              //console.log(request.responseText);
            }
          });
        });

        $("#recordsTotal").html(<?php echo $returnedTotal;?>);

//---------------------------------------------------------------------------------------D[Page Listeners - View Details from Map]
//--------#getEvent opens event page in new tab 
      });
    </script>

    <!--Advanced Search Listener--> 
    <?php require 'sections/scripts_advanced_search_listener.php'; ?>

    <!--Input Template--> 
    <?php require 'sections/search_template.php'; ?>

  </body>
</html>
