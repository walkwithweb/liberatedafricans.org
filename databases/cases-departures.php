<?php
  require_once("../public/head.php");
  require_once("../database.php");
  require_once("advancedsearch.php");
?>
  <body class="home">
    <section id="courts-cases" class="main">
      <?php $addRowsCount = 1; ?>
      <?php require_once("../public/header.php"); ?>
      <?php include("../public/blur.php"); ?>
      <div class="container-fluid content courtcases">
        <!--Page Title-->
        <?php 
        $current_page = '1';
        require 'sections/page_title.php'; 
        ?>
        <!--Start Page Content-->
        <?php 
        $field_for_totals = 'Field13'; // Departure place, should not be blank 
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
    <!--African Regions--> 
    <script src="../assets/js/africansubregions.js"></script>
    <script src="../assets/js/africanbroadregions.js"></script>
    <!--Colours--> 
    <script type="text/javascript" src="../assets/js/rainbowvis.js"></script>

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
        var sid = ''; // subregion id 
        var did = '';// departure id with the sid
        var lang = ''; // subregion label 

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
                var vdQuery = curQuery + " AND Field12 = '" + did + "'";
                //console.log("vdQuery: " + vdQuery);
                updateTable(vdQuery,returnedTotal,totalPage);
              } else {
                updateTable(curQuery,returnedTotal,totalPage);
              }
            } else {
              if(!isFirstPageLoad){
                if(isViewDetails){
                  var vdQuery = curQuery + " AND Field12 = '" + did + "'";
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

        // Regions ------------
        var locations;
        var latitude;
        var longitude;
        var Field12;
        var regional;

        // Places -------------
        var locations1;
        var latitude1;
        var longitude1;
        var stats;
        var Field13 ;
        var port;

        // Filter ----------
        var startDate;
        var EndDate;
        var selectedDropdown;

        // MAP Begins ------
        var broadregions;
        var broadregionsN;
        var subregions; 
        var subregionsN; 
        var subregionNames;
        var distinctregions; 
        var regionTotals;
        var totEnslaved;
        var subregionTotals;
        var broadregionTotals;
        var totBroadR;
        var totSubR;

//-----------------------------------------------------------------------------------------------B[ Build Map & Its Variables]

        var map = L.map('map').setView([11.0542035978,13.5941134872], 3);
        var numFormat = new Intl.NumberFormat('en-US');
        
        // Add tile layers - https://leaflet-extras.github.io/leaflet-providers/preview/
        var esriWorldShadedRelief = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Shaded_Relief/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri',
            maxZoom: 13
        });

        map.addControl(new L.Control.Fullscreen({
            title: {
                'false': 'View Fullscreen',
                'true': 'Exit Fullscreen'
            }
        }));

        // Handle style for geojson polygons
        var styleRegions = {
            'stroke': true,
            'color': "#0A15A3",
            'weight': 1,
            'opacity': 1,
            'fill': true,
            'fillColor': "#0A15A3",
            'fillOpacity': 1,
            'className': "blurme"
        };

        // Generate Gradient 
        var numberOfItems = 101; // 0-100
        var largestTotal = 0;
        var myRainbow = new Rainbow();
        var rainbowGradient = [];
        myRainbow.setNumberRange(1, numberOfItems);
        myRainbow.setSpectrum('#E9CC9B','#AB4503','#4F040C'); // yellow->orange->maroon
        var s = '';
        for(var i = 1; i <= numberOfItems; i++){
          var hexColour = '#' + myRainbow.colourAt(i);
          rainbowGradient.push(hexColour);
          s += '#' + hexColour + ',';
        }

        // Fetch the geoJSON for the map - Subregions & Broadregions
        let subregionsGroup = L.layerGroup();

        let baseMaps = {
            "Subregions" : esriWorldShadedRelief
        }

        let overMaps = {
            "Subregions" : subregionsGroup
        }

        var toggleLayersControl = L.control.layers(null,null,{collapsed:false, position:'bottomright'});
        toggleLayersControl.addTo(map);

        // Title Info 
        $(".leaflet-control-layers-list").prepend("<p class=\"lParagraph\" style=\"margin-left:4px;\"><strong>Regional Departures</strong></p>");
        $(".leaflet-control-layers-base").css('display','none');
        //$(".leaflet-control-layers-overlays").css('display','none');
        var div = L.DomUtil.create('div','info legend');
        var grades = [0,50,100];
        var labels = [];
        // Loop and generate labels
        $(".leaflet-control-layers-overlays").append('<p class="lParagraph"></p>');
        for (var i = 0; i < 100; i++){
          $(".leaflet-control-layers-overlays p.lParagraph").append('<i class="iLegend" style="background:' + rainbowGradient[i] + '"></i>');
        }

        $(".leaflet-control-layers-overlays").append('<div id="lLbls" class="row"></div>');
        for (var i = 0; i < grades.length; i++){
          if(grades[i]==100){
            $(".leaflet-control-layers-overlays div#lLbls").append('<div class="col text-end"><span class="lbl">|</span></div>');
          } else if(grades[i]==0){
            $(".leaflet-control-layers-overlays div#lLbls").append('<div class="col text-start"><span class="lbl">|</span></div>');
          } else {
            $(".leaflet-control-layers-overlays div#lLbls").append('<div class="col text-center"><span class="lbl">|</span></div>');
          }
        }
        $(".leaflet-control-layers-overlays").append('<div id="lPips" class="row"></div>');
        for (var i = 0; i < grades.length; i++){
          //var numFormatG = new Intl.NumberFormat('en-US');
          var formattedNumG = numFormat.format(grades[i]*1000);
          if(grades[i]==100){
            $(".leaflet-control-layers-overlays div#lPips").append('<div class="col text-end">' + formattedNumG + '+</div>');
          } else if(grades[i]==0){
            $(".leaflet-control-layers-overlays div#lPips").append('<div class="col text-start">' + formattedNumG+ '</div>');
          } else {
            $(".leaflet-control-layers-overlays div#lPips").append('<div class="col text-center">' + formattedNumG + '</div>');
          }
        }

        // Launch the page with subregions showing
        esriWorldShadedRelief.addTo(map);
        subregionsGroup.addTo(map);

        var firstProjection = 'PROJCS["Google Maps Global Mercator",GEOGCS["WGS 84",DATUM["WGS_1984",SPHEROID["WGS 84",6378137,298.257223563,AUTHORITY["EPSG","7030"]],AUTHORITY["EPSG","6326"]],PRIMEM["Greenwich",0,AUTHORITY["EPSG","8901"]],UNIT["degree",0.01745329251994328,AUTHORITY["EPSG","9122"]],AUTHORITY["EPSG","4326"]],PROJECTION["Mercator_2SP"],PARAMETER["standard_parallel_1",0],PARAMETER["latitude_of_origin",0],PARAMETER["central_meridian",0],PARAMETER["false_easting",0],PARAMETER["false_northing",0],UNIT["Meter",1],EXTENSION["PROJ4","+proj=merc +a=6378137 +b=6378137 +lat_ts=0.0 +lon_0=0.0 +x_0=0.0 +y_0=0 +k=1.0 +units=m +nadgrids=@null +wktext  +no_defs"],AUTHORITY["EPSG","900913"]]';

        var secondProjection = "+proj=longlat +datum=WGS84 +no_defs";

        $("#resetForm").on("click", function(){
          window.location.reload();
        });

        $("#refreshForm").on("click", function(){
          window.location.href = "cases-departures.php";
        });

//---------------------------------------------------------------------------------------C[Load Data]        

        <?php if(!$isAdvancedSResults): ?>
          // Default page load 
          $.ajax({
            url:"mysqlQuery_Departures.php",
            method:"POST",
            dataType: "json",
            data: {'perPage': perPage},
            async: false,
            success:function(data){
              broadregions=data[0];
              broadregionsN=data[1];
              subregions=data[2]; 
              subregionsN=data[3]; 
              subregionNames = data[3];
              distinctregions=data[4]; 
              regionTotals=data[5];
              totEnslaved = data[6];

              stats = data[8];
              curQuery = data[11]; //console.log("current query - default is: " + curQuery);
              returnedTotal = data[16]; //console.log(returnedTotal);
              totalPage = data[17]; //console.log(totalPage);

              $("#cases-tot").html(numFormat.format(stats['cases_tot']));
              //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
              $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
              //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
              $("#registered-tot").html(numFormat.format(stats['registered_tot']));
              $("#accordionStats").html(data[13]);
              $("#accordionStatsCourts").html(data[14]);

              displayMap();
              updateCharts(data[9],data[10]);
              updateTable(curQuery,returnedTotal,totalPage);
              isFirstPageLoad = false;
              isViewDetails = false;
            },
            error: function (request, status, error){
              alert(request.responseText);
            }
          });
        <?php else: ?> 
          // Advanced Search 
          <?php 
          $as_query = json_encode($advanced_query);
          echo "var as_query = " . $as_query . ";\n"; // The advanced query string 
          ?>
          $.ajax({
            url:"mysqlQuery_Departures.php",
            method:"POST",
            dataType:"json",
            data: {
              'fltr':"AS",
              'adv-search': as_query,
              'perPage': perPage
            },
            async:false,
            success:function(data){
              broadregions=data[0];
              broadregionsN=data[1];
              subregions=data[2]; 
              subregionsN=data[3]; 
              subregionNames = data[3];
              distinctregions=data[4]; 
              regionTotals=data[5];
              totEnslaved = data[6];

              stats = data[8];
              curQuery = data[11]; //console.log("current query - advanced is: " + curQuery);
              returnedTotal = data[16]; //console.log(returnedTotal);
              totalPage = data[17]; //console.log(totalPage);

              if(!data[12]){
                // results found
                $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                $("#accordionStats").html(data[13]);
                $("#accordionStatsCourts").html(data[14]);

                displayMap();
                updateCharts(data[9],data[10]);
                updateTable(curQuery,returnedTotal,totalPage);
                isFirstPageLoad = false;
                isViewDetails = false;
              } else {
                // No results found
                window.location.href = 'cases-departures.php?msg=0';
              }
            },
            error:function(request,status,error){
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
          //console.log("Current Query: " + cQry);
          //console.log("Returned Total: " + rTot);
          //console.log("Total Pages: " + tPages);
          //console.log("Current Page : " + cPg);
          //console.log("Per Page : " + perPage);
          $.ajax({
            url:"mysqlQuery_Table.php",
            method:"POST",
            dataType:"json",
            data:{'cQuery':cQry,'returnedTotal':rTot,'totalPages':tPages,'page':cPg,'perPage':perPage,'key':"departures"},
            success:function(data){
              //console.log("Row start " + data[1]);
              //console.log(data[5]);
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
            error: function (request, status, error) {
              alert(request.responseText);
            }
          });
        }

        function displayMap(){
          map.setView([11.0542035978,13.5941134872], 3);
          $('#place-tab').get(0).click();

          // Set-up data - returned from ajax call... 
          subregionTotals = [];
          let totalEnslavedAfricans = totEnslaved;
          totBroadR = 0;
          totSubR = 0;
          let placeIDS = Object.keys(regionTotals);
          placeIDS.forEach(placeID => {
              let sid = regionTotals[placeID]['Subregion'];
              let total = Number(regionTotals[placeID]['totEnslaved']);
              if(sid){
                if(sid in subregionTotals){
                    subregionTotals[sid] += total;
                } else {
                    subregionTotals[sid] = total;
                }
                totSubR += total;
              } else {}
          });

          var arsubregions = L.geoJSON(africansubregions, {
            style: styleRegions,
            onEachFeature: onEachFeatureSub
          });
        }

        // Calculate gradient
        function getBackground(cintensity){
            var n = Math.round(cintensity/1000);
            if (n > 100){
              n = 100;
            }
            var c = rainbowGradient[n];
            return c;
        }

        function closeTooltip(e){
            var tooltip = this.getTooltip();
            if(this.isPopupOpen()){
            tooltip.setOpacity(0.0);
            } else {
            tooltip.setOpacity(1.0);
            }
        }

        // Fetch data for the geojson polygons
        function onEachFeatureSub(feature,layer){
            if(feature.properties && feature.properties.title){
            subregionsGroup.addLayer(layer);
            /* Use the title to get the ID number */
            let title = feature.properties.title;
            var temp = subregionNames[title];
            let id = subregionNames[title]['personID'];
            /* Count all the instances of this id in the Broadregions column */
            // Check that id is there
            if(id in subregionTotals){
                var totForReg = subregionTotals[id];
                if(totForReg == 0){
                  layer.setStyle({
                  'opacity': 1,
                  'color': "#E9CC9B",
                  'fillColor': "#E9CC9B"
                  });

                  var content="<h5><strong>" + title + "</strong></h5>" + "<p style=margin:0;margin-bottom:15px;>Enslaved: " + totForReg + '<br><a href="https://africanregions.org/subregions.php?pid=%27'+title+'%27" target="_blank" style="color:var(--orange);">Learn more about this African region...</a></p>';
                } else {
                  var setColor = getBackground(totForReg);
                  layer.setStyle({
                  'opacity': 1,
                  'color': setColor,
                  'fillColor': setColor
                  });

                  //var numFormatR = new Intl.NumberFormat('en-US');
                  var formattedNumR = numFormat.format(totForReg);
                  var content="<h5><strong>" + title + "</strong></h5>" + "<p style=margin:0;margin-bottom:15px;>Enslaved: " + formattedNumR + '<br><a href="https://africanregions.org/subregions.php?pid=%27'+title+'%27" target="_blank" style="color:var(--orange);">Learn more about this African region...</a></p>' +'<a class=btnNext style=font-size:18px; href=# data-toggle=modal data-target=#view-modal data-lang="'+title+'" data-sid="'+id+'" id=getSRegion>View Details</a>';
                }
            } else {
                layer.setStyle({
                'opacity': 0.1,
                'color': "#DED3D1",
                'fillOpacity': 0.1,
                'fillColor': "#DED3D1"
                });

                var content="<h5><strong>" + title + "</strong></h5>" + '<p style=margin:0;margin-bottom:15px;>Data for region unavailable<br><a href="https://africanregions.org/subregions.php?pid=%27'+title+'%27" target="_blank" style="color:var(--orange);">Learn more about this African region...</a></p>';
            }

            layer.bindTooltip(title,{
                closeButton: false, 
                offset:L.point(0,-20),
                sticky: true,
                className: 'arTooltip'
            });

            layer.bindPopup(content, {
                'maxWidth': '400',
                'maxHeight':'250',
                'minWidth': '250',
                'minHeight':'250'
            });

            layer.on({
                click: closeTooltip
            });
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

        // Combined Date - Court - Place -----------------------------------------------------------------[FILTER]
        $("#filterForm").on("click", function(e){
          // Date Data ------------------------------
          startDate = $("#from").val();
          EndDate = $("#to").val();
          //console.log(startDate);
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
          subregionsGroup.clearLayers();
          $.ajax({
            url:"mysqlQuery_Departures.php",
            method:"POST",
            data: {
              'fltr':"All",
              'perPage': perPage,
              'startDate':startDate, 
              'EndDate':EndDate, 
              'allClicked':allClicked,
              'cvIDs':cvIDs
            },
            dataType: "json",
            async: false,
            success:function(data){
              broadregions=data[0];
              broadregionsN=data[1];
              subregions=data[2]; 
              subregionsN=data[3]; 
              subregionNames = data[3];
              distinctregions=data[4]; 
              regionTotals=data[5];
              totEnslaved = data[6];

              stats = data[8];
              curQuery = data[11]; //console.log("current query - filter is: " + curQuery);
              returnedTotal = data[16]; //console.log(returnedTotal);
              totalPage = data[17]; //console.log(totalPage);

              if(!data[12]){
                // results found
                $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                $("#accordionStats").html(data[13]);
                $("#accordionStatsCourts").html(data[14]);

                displayMap();
                updateCharts(data[9],data[10]);
                updateTable(curQuery,returnedTotal,totalPage);
                isFirstPageLoad = false;
                isViewDetails = false;
                
              } else {
                // No results found
                //console.log(data);
                window.location.href = 'cases-departures.php?msg=0';
              }
            },
            error: function (request, status, error) {
              //console.log(request.responseText);
            }
          });
        });

        $("#recordsTotal").html(<?php echo $returnedTotal;?>);

//---------------------------------------------------------------------------------------D[Page Listeners - View Details from Map]

          // Handle Sub Regions - View Details - [Currently in use]
        $(document).on('click', '#getSRegion', function(e){
            e.preventDefault();
            $('#event-tab').get(0).click(); // Show database tab

            sid = $(this).data('sid');   // gives the broad region id
            lang = $(this).data('lang'); // give the broad region name 

            isViewDetails = true;
            $('#LocationName').html(lang);

            $.ajax({
              url:"mysqlQuery_Departures.php",
              method:"POST",
              data: {
                'fltr':"VD",
                'perPage': perPage,
                'sid': sid,
                'cQuery': curQuery
              },
              dataType: "json",
              async: false,
              success:function(data){
                broadregions=data[0];
                broadregionsN=data[1];
                subregions=data[2]; 
                subregionsN=data[3]; 
                subregionNames = data[3];
                distinctregions=data[4]; 
                regionTotals=data[5];
                totEnslaved = data[6];
                did = data[21];

                stats = data[8];
                var vQuery = data[11]; //curQuery = data[14]; 
                //console.log("current query - getsregion is: " + data[11]);
                returnedTotal = data[16]; //console.log(returnedTotal);
                totalPage = data[17]; //console.log(totalPage);

                if(!data[12]){
                  // results found
                  $("#cases-tot").html(numFormat.format(stats['cases_tot']));
                  //$("#enslaved-tot").html(numFormat.format(stats['enslaved_tot']));
                  $("#liberated-tot").html(numFormat.format(stats['liberated_tot']));
                  //$("#courts-tot").html(numFormat.format(stats['courts_tot']));
                  $("#registered-tot").html(numFormat.format(stats['registered_tot']));
                  $("#accordionStats").html(data[13]);
                  $("#accordionStatsCourts").html(data[14]);

                  // Skip map update
                  updateCharts(data[9],data[10]);
                  //isFirstPageLoad = true;
                  //console.log("Change the page!!!");
                  pag.simplePaginator('changePage',1); // Go back to start and reupload table rows
                  //updateTable(vQuery,returnedTotal,totalPage);
                  isFirstPageLoad = false;
                } else {
                  // No results found
                  //console.log(data);
                  window.location.href = 'cases-departures.php?msg=0';
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
