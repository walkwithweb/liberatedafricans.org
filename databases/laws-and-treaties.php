<?php
  require_once("../public/head.php");
  require_once("../database.php");
?>
  <body>
    <section>
      <?php require_once("../public/header.php"); ?>
      <?php 
            /* Get the CV_Location data */
            $CV_Location = array();
            $q_CV_source_fields1= "SELECT * FROM CV_Location_AL";
            $query_CV_source_fields1 = $conn->query($q_CV_source_fields1);
            while($row = $query_CV_source_fields1->fetch(PDO::FETCH_ASSOC)){
                $id = $row['ID'];
                $CV_Location[$id] = $row;
            }

            $CV_LocationAS = array();
            $q_CV_source_fields1= "SELECT * FROM CV_Location_AS";
            $query_CV_source_fields1 = $conn->query($q_CV_source_fields1);
            while($row = $query_CV_source_fields1->fetch(PDO::FETCH_ASSOC)){
                $id = $row['ID'];
                $CV_LocationAS[$id] = $row;
            }

            /* Get the CV_Nationality */
            $CV_Nationality = array();
            $q_Nationality = "SELECT `ID`,`Name` FROM CV_Nationality_AL";
            $query_Nationality = $conn->query($q_Nationality);
            while($row = $query_Nationality->fetch(PDO::FETCH_ASSOC)){
                $id = $row['ID'];
                $CV_Nationality[$id] = $row;
            }

            $CV_NationalityAS = array();
            $q_Nationality = "SELECT `ID`,`Name` FROM CV_Signee_AS";
            $query_Nationality = $conn->query($q_Nationality);
            while($row = $query_Nationality->fetch(PDO::FETCH_ASSOC)){
                $id = $row['ID'];
                $CV_NationalityAS[$id] = $row;
            }

            /* Get the CV_Language */
            $CV_Language = array();
            $q_Language = "SELECT `ID`,`Name` FROM CV_Language_AL";
            $query_Language = $conn->query($q_Language);
            while($row = $query_Language->fetch(PDO::FETCH_ASSOC)){
                $id = $row['ID'];
                $CV_Language[$id] = $row;
            }

            $CV_LanguageAS = array();
            $q_Language = "SELECT `ID`,`Name` FROM CV_Language_AS";
            $query_Language = $conn->query($q_Language);
            while($row = $query_Language->fetch(PDO::FETCH_ASSOC)){
                $id = $row['ID'];
                $CV_LanguageAS[$id] = $row;
            }

            /* Get the people */
            $AL_People = array();
            $q_AL_People = "SELECT person_AL.personID, person_AL.Name as Name, person_AL.Field1 as Title, person_AL.Field2 as Nationality, person_AL.Field3 as Language FROM person_AL, objects_person_AL WHERE person_AL.personID = objects_person_AL.personID GROUP BY personID";
            $query_AL_People = $conn->query($q_AL_People);
            while($row = $query_AL_People->fetch(PDO::FETCH_ASSOC)){
                $id = $row['personID'];
                $AL_People[$id] = $row;
            }

            /* GEOJSON object for points.json */
            $geojson = array(
                'type'      => 'FeatureCollection',
                'features'  => array()
            );

            /* Get all the objects */
            $q_source_data="SELECT * FROM `object_AL` ORDER BY Field4 ASC";
            $query_source_data = $conn->query($q_source_data);
            while ($source_data = $query_source_data->fetch(PDO::FETCH_ASSOC)) {
                //Extract all IDS saved in source metafield column
                $selectedlocations=$source_data['Field27'];
                $selectedlocations_arr = explode(';', trim($selectedlocations,';'));
                $personName = array();
                $personTitle = array();
                $pNationality = array();
                $pLanguage = array();

                /* Collect the person data associated with this object */
                // - Get the related persons from objects_person
                $q_op = "SELECT personID FROM objects_person_AL WHERE objectID ='".$source_data['objectID']."'";
                $query_op = $conn->query($q_op);
                while($row = $query_op->fetch(PDO::FETCH_ASSOC)){
                    /* Get the person */
                    $pid= $row['personID']; 
                    if(array_key_exists($pid,$AL_People)){
                        $persondata = $AL_People[$pid];
                        $personName[] = $persondata['Name'];
                        $personTitle[] = $persondata['Title'];
                        $nID = $persondata['Nationality'];
                        if(array_key_exists($persondata['Nationality'],$CV_Nationality)){
                            $pNationality[] = $CV_Nationality[$persondata['Nationality']]['Name'];
                        } else {
                            $pNationality[] = '';
                        }
                        if(array_key_exists($persondata['Language'],$CV_Language)){
                            $pLanguage[] = $CV_Language[$persondata['Language']]['Name'];
                        } else {
                            $pLanguage[] = '';
                        }
                    }
                }

                /* Loop through the locations and save the person arrays per location for the object */
                foreach($selectedlocations_arr as $locationID){
                    if($locationID != 0 && $locationID != '' && $locationID != null){
                        $locData = $CV_Location[$locationID];
                        $feature = array(
                            'type' => 'Feature',
                            'geometry' => array(
                            'type' => 'Point',
                            'coordinates' => array($locData['x_coordinate'], $locData['y_coordinate'])),
                            'properties' => array(
                            'objectID' => $source_data['objectID'],
                            'name' => $source_data['Field1'],
                            'location' => $locData['Name'],
                            'RegID' => $source_data['UI'],
                            'time' => date('Y', strtotime($source_data['Field4'])),
                            'source'=>$source_data['Field5'],
                            'file'=>$source_data['File'],
                            'pname' =>$personName,
                            'ptitle' =>$personTitle,
                            'pnationality'=>$pNationality,
                            'planguage'=>$pLanguage,
                            'x'=>$locData['x_coordinate'],
                            'y'=>$locData['y_coordinate']
                            )
                        );
                        array_push($geojson['features'], $feature);
                        // echo date('Y', strtotime($source_data['Field4'])) .' ' . $source_data['Field4'] . '<br>' ;
                    } else {
                        //Field 27 is either blank or 0 so cannot be mapped. 
                        //echo 'This id is missing - ' . $locationID . ' objectID - '.$source_data['objectID'].'<br>';
                    }
                    
                }
                
                unset($personName);
                unset($personTitle);
                unset($pNationality);
                unset($pLanguage);
            }

            /* Get all the objects - AS */
            $q_source_data="SELECT * FROM `object_AS` ORDER BY Field4 ASC";
            $query_source_data = $conn->query($q_source_data);
            while ($source_data = $query_source_data->fetch(PDO::FETCH_ASSOC)) {
                //Extract all IDS saved in source metafield column
                $selectedlocations=$source_data['Field27'];
                $selectedlocations_arr = explode(';', trim($selectedlocations,';'));
                $personName = array();
                $personTitle = array();
                $pNationality = array();
                $pLanguage = array();

                /* Loop through the locations and save the person arrays per location for the object */
                foreach($selectedlocations_arr as $locationID){
                    if($locationID != 0 && $locationID != '' && $locationID != null){
                        $locData = $CV_LocationAS[$locationID];
                        $feature = array(
                            'type' => 'Feature',
                            'geometry' => array(
                            'type' => 'Point',
                            'coordinates' => array($locData['Longitude'], $locData['Latitude'])),
                            'properties' => array(
                            'objectID' => $source_data['objectID'],
                            'name' => $source_data['Field1'],
                            'location' => $locData['Name'],
                            'RegID' => $source_data['UI'],
                            'time' => explode("_",$source_data['Field4'])[0],
                            'source'=>$source_data['Field5'],
                            'file'=>$source_data['File'],
                            'pname' =>$personName,
                            'ptitle' =>$personTitle,
                            'pnationality'=>$pNationality,
                            'planguage'=>$pLanguage,
                            'x'=>$locData['Longitude'],
                            'y'=>$locData['Latitude']
                            )
                        );
                        array_push($geojson['features'], $feature);
                        // echo date('Y', strtotime($source_data['Field4'])) .' ' . $source_data['Field4'] . '<br>' ;
                    } else {
                        //Field 27 is either blank or 0 so cannot be mapped. 
                        //echo 'This id is missing - ' . $locationID . ' objectID - '.$source_data['objectID'].'<br>';
                    }
                    
                }
                
                unset($personName);
                unset($personTitle);
                unset($pNationality);
                unset($pLanguage);
            }

            //header("Content-Type:application/json",true);
            //write to json file
            $fp = fopen('points.json', 'w');
            fwrite($fp, json_encode($geojson,JSON_PRETTY_PRINT));
            fclose($fp);
      ?>
      <div class="container-fluid content">
        <!--Page Title-->
        <div class="row justify-content-center">
            <div id="pageTitleContainer" class="col-11 justify-content-start">
                <h1 class="pageTitle">Anti-Slavery Legislation</h1>
                <hr class="pageTitleBorder">
            </div>
        </div>
        <!--Start Page Content-->
        <div class="row justify-content-center align-items-center">
            <div id="main-content" class="col-11 mb-5" style="min-height:auto;">
                <!--Your code starts here-->
                <div class="row">
                    <div class="col- px-0">
                        <div id="map" class="p-5" style="height: 700px; width: auto;" ></div>
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
    <script src="../assets/js/mapextras.js"></script><!--Additional Data-->
    <script type="text/javascript">
        $(document).ready(function(){
            // Add tile layers - https://leaflet-extras.github.io/leaflet-providers/preview/
            var esriWorldShadedRelief = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Shaded_Relief/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri',
                maxZoom: 13
            });

            var map = L.map('map').setView([5.208570, -4.420624], 2);
            map.addControl(new L.Control.Fullscreen({
                title: {
                    'false': 'View Fullscreen',
                    'true': 'Exit Fullscreen'
                }
            }));

            map.createPane("pane250").style.zIndex = 250; // For the labels
            map.createPane("pane700").style.zIndex = 1000; // For the marker clusters, to keep them above the labels (650)

            esriWorldShadedRelief.addTo(map);
            
            var layerControl = L.control.layers(null,null,{collapsed:false,position:'bottomleft'});
            layerControl.addTo(map);
            // Add a title for the legend 
            // $(".leaflet-control-layers-base").css('display','none');
            $(".leaflet-control-layers-overlays").append("<p class=\"lParagraph mb-3\"><strong>Location of Legislation Sources By Year</strong></p>");
            var appendData = "<div class=\"sliderContainer\"><div id=\"slider\" class=\"lSlider mb-3\"></div></div>";
            $(".leaflet-control-layers-overlays").append(appendData);

            let locationsOverlay = L.layerGroup(); // Location labels - locations
            // map.addLayer(locationsOverlay);
            locationsOverlay.addTo(map);

            for(var i=0; i<locations.length; i++){
                var obj = locations[i];
                for(var key in obj){
                    var tName = key;

                    var tVal = obj[key];
                    var tLat = tVal['Lat'];
                    var tLng = tVal['Lng'];
                    var tt = L.tooltip({
                        pane: "pane250",
                        permanent:true,
                        direction:"center",
                        className: 'locationsLabel'
                    }).setLatLng([tLat,tLng]).setContent(tName).openTooltip();
                    locationsOverlay.addLayer(tt);
                }
            }

            let mlayerGroup = L.markerClusterGroup(); // Marker Clustering Extension
            let mcontrolGroup = L.layerGroup(); // Hold all the layers created from points.json 
            mlayerGroup.addTo(map);

            $.getJSON("points.json", function(json) {
                var minV = 0;
                var maxV = 0;
                var mapMarkers = L.geoJSON( json, {
                    onEachFeature: function (feature, layer) {
                        // Check for min and max values for the slider
                        var year = parseInt(feature.properties.time);
                        if(year<minV || minV ===0){
                            minV = year;
                        }
                        if(year>maxV){
                            maxV = year;
                        }
                        var source_code = feature.properties.RegID.substring(0,2);
                        var source_link = "";
                        if(source_code == "AL"){
                            source_link = "source_details_AL.php?ObjectID=";
                        } else if(source_code == "AS"){
                            source_link = "https://usantislaverylaws.org/resources/view-details.php?objectID=";
                        } 

                        layer.bindPopup("<h6><b>UID:</b> "+ feature.properties.RegID+ "</h6><h6><b>Year:</b> " + feature.properties.time+"</h6><h6><b>Title:</b> " + feature.properties.name + "</h6><h6><b>Location:</b> "+ feature.properties.location+ "</h6><br>" + '<a style="font-size:18px;" target="_blank" href=' + source_link + feature.properties.objectID + ' />Source Details</a>' + "<h6 class=pb-2></h6>", 
                        {
                            'maxWidth': '300',
                            'maxHeight':'350',
                            'minWidth': '300',
                            'minHeight':'250'
                        });
                    
                        layer.feature = feature;
                        mcontrolGroup.addLayer(layer); // Add to be used for filtering / slider
                        mlayerGroup.addLayer(layer); // Add for initial map page load
                    }
                });
                // Make our slider - using values from minV,maxV calculation above. 
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

                    // Clear the markers on the map by clearing the cluster group
                    mlayerGroup.clearLayers();

                    // Iterate over the control group
                    mcontrolGroup.eachLayer(function(layer){
                        var year = parseInt(layer.feature.properties.time);
                        if(year>=parseInt(e[0])&&year<=parseInt(e[1])){
                            // Show layer
                            mlayerGroup.addLayer(layer);
                        } else {
                            // Hide layer 
                            mlayerGroup.removeLayer(layer);
                        }
                    });
                });
            });
        });
        

    </script>
  </body>
</html>
