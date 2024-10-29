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
        $current_page = '3';
        require 'sections/page_title.php'; 
        ?>
        <!--Start Page Content-->
        <?php 
        $field_for_totals = 'Field27'; // Arrival place, should not be blank
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
        var isViewDetails = false; 
        var pid = ''; // port id 
        var lang = ''; // port label 

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
              if(isViewDetails){
                var vdQuery = curQuery + " AND Field27 = '" + pid + "'";
                updateTable(vdQuery,returnedTotal,totalPage);
              } else {
                updateTable(curQuery,returnedTotal,totalPage);
              }
            } else {
              if(!isFirstPageLoad){
                if(isViewDetails){
                  var vdQuery = curQuery + " AND Field27 = '" + pid + "'";
                  updateTable(vdQuery,returnedTotal,totalPage);
                } else {
                  updateTable(curQuery,returnedTotal,totalPage);
                }
              } else {
                // For default page load
                updateTable(curQuery,returnedTotal,totalPage);
              }
            }
          }
        });

        // Regions ---------
        var locations;
        var latitude;
        var longitude;
        var Field26; 
        var regional;
        // Places ----------
        var locations1;
        var latitude1;
        var longitude1;
        var stats;
        var Field27 ;
        var port;
        
        // Filter ----------
        var startDate;
        var EndDate;
        var selectedDropdown;

        // Map begins -------
        var regionLayerGrp;
        var placeLayerGrp;
        var marker1,marker;

//-----------------------------------------------------------------------------------------------B[ Build Map & Its Variables]

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

        var legendLayersControl = L.control.layers(null,null,{collapsed:false, position:'bottomright'});
        legendLayersControl.addTo(map);

        // Title Info 
        $(".leaflet-control-layers-base").css('display','none');
        var appendData = "<p class=\"lParagraph\" style=\"font-weight:bold;\"><span class=\"legendCircle\"><i style=\"background:#7F0000;\"></i>Liberated Africans by Place</span></p>";
        $(".leaflet-control-layers-overlays").append(appendData);

        var firstProjection = 'PROJCS["Google Maps Global Mercator",GEOGCS["WGS 84",DATUM["WGS_1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.01745329251994328,AUTHORITY["EPSG","9122"]],AUTHORITY["EPSG","4326"]],PROJECTION["Mercator_2SP"],PARAMETER["standard_parallel_1",0],PARAMETER["latitude_of_origin",0],PARAMETER["central_meridian",0],PARAMETER["false_easting",0],PARAMETER["false_northing",0],UNIT["Meter",1],EXTENSION["PROJ4","+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs"],AUTHORITY["EPSG","900913"]]';

        var secondProjection = "+proj=longlat +datum=WGS84 +no_defs";

        $("#resetForm").on("click", function(){
          window.location.href = "cases.php";
        });

        $("#refreshForm").on("click", function(){
          window.location.href = "cases.php";
        });

//---------------------------------------------------------------------------------------C[Load Data] 
        <?php if(!$isAdvancedSResults): ?>
          // Default Load
          $.ajax({
            url:"mysqlQuery_Cases.php",
            method:"POST",
            dataType: "json",
            data: {'perPage': perPage},
            async: false,
            success:function(data){
              // Region Data
              Field26=data[0];
              locations=data[1];
              regional=data[2]; ;
              latitude=data[3];
              longitude=data[4];
              // Port Data
              Field27=data[5];
              locations1=data[6];
              port=data[7];
              latitude1=data[8];
              longitude1=data[9];

              stats = data[11];
              curQuery = data[14]; //console.log("current query - default is: " + curQuery);
              returnedTotal = data[18];
              totalPage = data[19];

              $("#cases-tot").html(numFormat.format(stats['cases_tot']));
              //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
              $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
              //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
              $("#registered-tot").html(numFormat.format(stats['registered_tot']));
              $("#accordionStats").html(data[16]);
              $("#accordionStatsCourts").html(data[17]);

              displayMap();
              updateCharts(data[12],data[13]);
              updateTable(curQuery,data[18],data[19]);
              isFirstPageLoad = false;
              isViewDetails = false;
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
            url:"mysqlQuery_Cases.php",
            method:"POST",
            dataType: "json",
            data: {
              'fltr': "AS",
              'adv-search': as_query,
              'perPage': perPage
            },
            async: false,
            success:function(data){
              // Region Data
              Field26=data[0];
              locations=data[1];
              regional=data[2]; ;
              latitude=data[3];
              longitude=data[4];
              // Port Data
              Field27=data[5];
              locations1=data[6];
              port=data[7];
              latitude1=data[8];
              longitude1=data[9];

              stats = data[11];
              curQuery = data[14]; //console.log("current query - AS is: " + curQuery);
              returnedTotal = data[18];
              totalPage = data[19];
              
              if(!data[15]){
                // results found
                $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                $("#accordionStats").html(data[16]);
                $("#accordionStatsCourts").html(data[17]);

                displayMap();
                updateCharts(data[12],data[13]);
                updateTable(curQuery,data[18],data[19]);
                isFirstPageLoad = false;
                isViewDetails = false;
              } else {
                // No results found
                window.location.href = 'cases.php?msg=0';
              }
            },
            error: function (request, status, error) {
              //console.log(request);
              alert(request.responseText);
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
          /*console.log("Current Query: " + cQry);
          console.log("Returned Total: " + rTot);
          console.log("Total Pages: " + tPages);
          console.log("Current Page : " + cPg);*/
          $.ajax({
            url:"mysqlQuery_Table.php",
            method:"POST",
            dataType:"json",
            data:{'cQuery':cQry,'returnedTotal':rTot,'totalPages':tPages,'page':cPg,'perPage':perPage,'key':"cases"},
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
              pag.simplePaginator('setTotalPages',tPages);
            },
            error:function(request,status,error){
              alert(request.responseText);
            }
          });
        }

        function displayMap(){
          map.setView([11.0542035978,13.5941134872], 3);
          $('#place-tab').get(0).click();
          var radMultiplier = 12;
          // Create Regional Layer Group
          regionLayerGrp = new L.layerGroup();
          for (var i = 0; i < latitude.length; i++) {
            var title = locations[i];
            radius=regional[i];
            var coords = proj4(firstProjection,secondProjection,[latitude[i],longitude[i]]);
            var long = coords[0];
            var lat = coords[1];

            marker = L.circle(new L.LatLng(lat, long), {
              radius: radius * radMultiplier,
              color: '#7f0000',
              fillColor: '#7f0000',
              fillOpacity: 0.4,
              weight: 0.9,
              minRadiusCircle: 500000,
              zIndexOffset: -1000,
            });

            var givenNumberR = regional[i];
            var formattedNumR = numFormat.format(givenNumberR);
            var content="<h5><strong>" + locations[i] + "</strong></h5>" + "<p style=margin:0;margin-bottom:15px;>Liberated Africans: " + formattedNumR + "</p>" +'<a class=btnNext style=font-size:18px; href=# data-toggle=modal data-target=#view-modal data-lang="'+locations[i]+' " data-id="'+Field26[i]+' "id=getUser>View Details</a>';

            marker.bindPopup(content, {
              'maxWidth': '330',
              'maxHeight':'250',
              'minWidth': '330',
              'minHeight':'250'
            });
            marker.on('click', function (e) {
              this.openPopup();
            });
            marker.addTo(regionLayerGrp);
          }
          // regionLayerGrp.addTo(map); // Show regions as default

          // Create Place Layer Group
          placeLayerGrp = new L.layerGroup();
          for (var i = 0; i < latitude1.length; i++) {
            radius=port[i];
            var coords1 = proj4(firstProjection,secondProjection,[latitude1[i], longitude1[i]]);
            var long1 = coords1[0];
            var lat1 = coords1[1];
            marker1 = L.circle(new L.LatLng(lat1, long1), {
              radius: radius * radMultiplier,
              color: '#7f0000',
              fillColor: '#7f0000',
              fillOpacity: 0.4,
              weight: 0.9,
              minRadiusCircle: 500000,
            });

            var givenNumber = port[i];
            var formattedNum = numFormat.format(givenNumber);
            var content="<h5><strong>" + locations1[i] + "</strong></h5>" + "<p style=margin:0;margin-bottom:15px;>Liberated Africans: "+ formattedNum + "</p>" +'<a style=font-size:18px; class=btnNext href=# data-toggle=modal data-target=#view-modal data-lang="'+locations1[i]+' "  data-pid="'+Field27[i]+' "id=getport>View Details</a>';

            marker1.bindPopup(content, {
              'maxWidth': '330',
              'maxHeight':'250',
              'minWidth': '250',
              'minHeight':'250'
            });
            marker1.on('click', function (e) {
                    this.openPopup();
            });
            marker1.addTo(placeLayerGrp);
          }
          placeLayerGrp.addTo(map); // Show Places
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
          regionLayerGrp.clearLayers();
          placeLayerGrp.clearLayers();
          $.ajax({
            url:"mysqlQuery_Cases.php",
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
              // Region Data
              Field26=data[0];
              locations=data[1];
              regional=data[2]; ;
              latitude=data[3];
              longitude=data[4];
              // Port Data
              Field27=data[5];
              locations1=data[6];
              port=data[7];
              latitude1=data[8];
              longitude1=data[9];

              stats = data[11];
              curQuery = data[14]; //console.log("current query - filtered is: " + curQuery);
              returnedTotal = data[18];
              totalPage = data[19];

              if(!data[15]){
                // results found
                $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                $("#accordionStats").html(data[16]);
                $("#accordionStatsCourts").html(data[17]);

                displayMap();
                updateCharts(data[12],data[13]);
                updateTable(curQuery,data[18],data[19]);
                isFirstPageLoad = false;
                isViewDetails = false;
                
              } else {
                // No results found
                window.location.href = 'cases.php?msg=0';
              }
            },
            error: function (request, status, error) {
              //console.log(request.responseText);
            }
          });
        });

        $("#recordsTotal").html(<?php echo $returnedTotal;?>);

//---------------------------------------------------------------------------------------D[Page Listeners - View Details from Map]

        $(document).on('click', '#getport', function(e){
          
          e.preventDefault();
          $('#event-tab').get(0).click();

          pid = $(this).data('pid');   // it will get id of clicked row
          lang = $(this).data('lang'); 
          
          isViewDetails = true;
          $('#LocationName').html(lang);

          $.ajax({
            url: "mysqlQuery_Cases.php",
            method: "POST",
            dataType: "json",
            data: {
              'fltr': "VD",
              'perPage': perPage,
              'pid': pid,
              'cQuery': curQuery
            },
            async: false,
            success:function(data){
              // Region Data
              Field26=data[0];
              locations=data[1];
              regional=data[2]; ;
              latitude=data[3];
              longitude=data[4];
              // Port Data
              Field27=data[5];
              locations1=data[6];
              port=data[7];
              latitude1=data[8];
              longitude1=data[9];

              stats = data[11];
              var vQuery = data[14]; //curQuery = data[14]; 
              //console.log("current query - getport is: " + data[14]);
              returnedTotal = data[18];
              totalPage = data[19];

              if(!data[15]){
                // results found
                $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                $("#accordionStats").html(data[16]);
                $("#accordionStatsCourts").html(data[17]);
                // Skip map update
                updateCharts(data[12],data[13]);
                updateTable(vQuery,data[18],data[19]);
                isFirstPageLoad = false;
              } else {
                // No results found
                window.location.href = 'cases.php?msg=0';
              }
            },
            error: function (request, status, error) {
              //console.log(request.responseText);
            }
          });

        });

      });
    </script>

    <!--Advanced Search Listener--> 
    <?php require 'sections/scripts_advanced_search_listener.php'; ?>
    
    <!--Input Template--> 
    <?php require 'sections/search_template.php'; ?>
    
  </body>
</html>
