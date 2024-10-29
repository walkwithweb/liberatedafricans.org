<?php
require '../database.php';
$perPage = 10;
$startDate=0; $endDate=0;$selectedDropdown=0;$flag=0;$reg=0;
if(isset($_POST['advsearch']) && $_POST['advsearch']!=''){
  $advsearch = $_POST['advsearch'];
  $isAdvancedSResults = true;
} else {
  $advsearch = '';
  $isAdvancedSResults = false;
}

if(isset($_POST['id'])){
  $id=$_POST['id'];
  $columnName='Field12';
  if(isset($_POST['startDate'])){
    $startDate=$_POST['startDate'];
    $endDate=$_POST['EndDate'];
    $flag=0;
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field12='".$id."' and Field2 BETWEEN '".$_POST['startDate']."'  AND '".$_POST['EndDate']."' ".$advsearch);
  }else if(isset($_POST['selectedDropdown'])){
    $selectedDropdown=json_encode($_POST['selectedDropdown']);
    $flag=1;
    $startDate=0;
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field12='".$id."' and Field7 in (" . implode(",", $_POST['selectedDropdown']) . ") ".$advsearch);
  }else{
     $stmt = $conn->prepare("SELECT count(*) FROM person where Field12='".$id."'".$advsearch);
  }
}else if(isset($_POST['pid'])){
  $id=$_POST['pid'];
  $columnName='Field13';

  if(isset($_POST['startDate'])){
    $startDate=$_POST['startDate'];
    $endDate=$_POST['EndDate'];
    $flag=0;
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field13='".$id."' and Field2 BETWEEN '".$_POST['startDate']."'  AND '".$_POST['EndDate']."' ".$advsearch);
  }else if(isset($_POST['selectedDropdown'])){
    $flag=1;
    $startDate=0;
    $selectedDropdown=json_encode($_POST['selectedDropdown']);
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field13='".$id."' and Field7 in (" . implode(",", $_POST['selectedDropdown']) . ") ".$advsearch);
  }else{
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field13='".$id."' ".$advsearch);
  }
} else if(isset($_POST['bid'])){
  $arrIDs = array(); // Get all the places that are in the broad region 
  $bid = $_POST['bid'];
  $reg = 1;
  $columnName='Field12';
  $sql = "SELECT * FROM `CV_Places` WHERE Broadregion = " .$bid. "";
  $query = $conn->query($sql);
  while ($row = $query->fetch(PDO::FETCH_ASSOC)){
    $arrIDs[] = $row['ID'];
  }
  if(isset($_POST['startDate'])){
    $startDate = $_POST['startDate'];
    $endDate = $_POST['EndDate'];
    $flag = 0;
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field12 " . " in (" . implode(",", $arrIDs) .")" . " AND `Field2` BETWEEN $startDate AND $endDate ".$advsearch);
  } else if(isset($_POST['selectedDropdown'])){
    $flag = 1;
    $startDate = 0;
    $selectedDropdown=json_encode($_POST['selectedDropdown']);
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field12 " . " in (" . implode(",", $arrIDs) .")" . " AND `Field7` in (" . implode(",", $_POST['selectedDropdown']) . ") ".$advsearch);
  } else {
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field12 " . " in (" . implode(",", $arrIDs) .") ".$advsearch);
  }
  
} else if(isset($_POST['sid'])){
  $arrIDs = array(); // Get all the places that are in the broad region 
  $sid = $_POST['sid'];
  $reg = 2;
  $columnName='Field12';
  $sql = "SELECT * FROM `CV_Places` WHERE Subregion = " .$sid. "";
  $query = $conn->query($sql);
  while ($row = $query->fetch(PDO::FETCH_ASSOC)){
    $arrIDs[] = $row['ID'];
  }
  if(isset($_POST['startDate'])){
    $startDate = $_POST['startDate'];
    $endDate = $_POST['EndDate'];
    $flag = 0;
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field12 " . " in (" . implode(",", $arrIDs) .")" . " AND `Field2` BETWEEN $startDate AND $endDate " .$advsearch);
  } else if(isset($_POST['selectedDropdown'])){
    $flag = 1;
    $startDate = 0;
    $selectedDropdown=json_encode($_POST['selectedDropdown']);
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field12 " . " in (" . implode(",", $arrIDs) .")" . " AND `Field7` in (" . implode(",", $_POST['selectedDropdown']) . ") ".$advsearch);
  } else {
    $stmt = $conn->prepare("SELECT count(*) FROM person where Field12 " . " in (" . implode(",", $arrIDs) .") ".$advsearch);
  }
}

  $stmt->execute();
  $returnedTotal = $stmt->fetchColumn();
  $totalPages = ceil($returnedTotal/$perPage);?>
   <div class="row">
     <div class="col-lg-6">
      <p class="pl-4" id="pShowing">Showing <span id="byRowStart"></span> - <span id="recordsTo"></span>
      of <span id="recordsTotal"></span> Records</p>
     </div>
     <div class="col-lg-6">
      <p class="pagnumber">Page <span id="currentPage"></span> of <span> <?php echo $totalPages;?></span></p>
     </div>
   </div>
   <div class="table-responsive p-3 pt-5">
    <table class="table">
     <thead>
      <tr>
        <th scope="col">UID</th>
        <th scope="col">Case Name</th>
        <th scope="col">Date</th>
        <th scope="col">Court Name</th>
        <th scope="col">Enslaved Africans</th>
        <th scope="col">Identified by a Name</th>
        <th scope="col"></th>
      </tr>
     </thead>
    <tbody class="pb-5" id="content"></tbody>
   </table>
   <div class="pt-5 pb-5" id="pagination"></div>
   <input type="hidden" id="totalPages" value="<?php echo $totalPages; ?>">
    <?php if(isset($_POST['bid'])):?>
      <input type="hidden" id="id" value="<?php echo $bid; ?>">
    <?php elseif(isset($_POST['sid'])): ?>
      <input type="hidden" id="id" value="<?php echo $sid; ?>">
    <?php else: ?>
      <input type="hidden" id="id" value="<?php echo $id; ?>">
    <?php endif; ?>
    <input type="hidden" id="columnName" value="<?php echo $columnName; ?>">
    <input type="hidden" id="startDate" value="<?php echo $startDate; ?>">
    <input type="hidden" id="endDate" value="<?php echo $endDate; ?>">
  </div>

  <script type="text/javascript">
  $(document).ready(function(){
  $("#recordsTotal").html(<?php echo $returnedTotal;?>);
  var returnedTotal = <?php echo $returnedTotal;?>;
  var startDate = <?php echo $startDate;?>;
  var EndDate = <?php echo $endDate;?>;
  var flag = <?php echo $flag;?>;

  var totalPage = parseInt($('#totalPages').val());
  var id = parseInt($('#id').val());
  var columnName = $('#columnName').val();

  var summaryStats;
  var numFormat = new Intl.NumberFormat('en-US');
  <?php
    if($isAdvancedSResults){
      $adv_query = json_encode($advsearch);
      echo "var advquery = " . $adv_query . ";\n"; // The Advanced Query String 
    } else { ?>
      var advquery = '';
  <?php } ?> 

  var reg = <?php echo $reg; ?>; // Used to flag whether broad or subregion
  var pag = $('#pagination').simplePaginator({
    totalPages: totalPage,
    maxButtonsVisible: 5,
    currentPage: 1,
    nextLabel: 'Next',
    prevLabel: 'Prev',
    clickCurrentPage: true,
    pageChange: function(page) {
      $('#currentPage').html(page);
      $("#content").html('<tr><td colspan="6"><strong>loading...</strong></td></tr>');
     if (startDate == 0 && flag==0){
      //alert("commomxxx")
      $.ajax({
        url:"pagination_AfricanOrigins.php",
        method:"POST",
        dataType: "json",
        data:{page: page,id:id,columnName:columnName,reg:reg,advsearch:advquery},
        success:function(responseData){
          $('#content').html(responseData[0].html);
          $('#byRowStart').html(responseData[1]);
          if (responseData[2] > returnedTotal) {
            $("#recordsTo").html(returnedTotal);
          }else{
            $('#recordsTo').html(responseData[2]);
          }
          summaryStats = responseData[5];
          window.myBar.data.datasets[0].data = responseData[4];
          window.myBar.data.labels = responseData[3];
          window.myBar.update();
          $("#cases-tot").html(numFormat.format(summaryStats['cases_tot']));
          $("#enslaved-tot").html(numFormat.format(summaryStats['enslaved_tot']));
          $("#liberated-tot").html(numFormat.format(summaryStats['liberated_tot']));
          $("#courts-tot").html(numFormat.format(summaryStats['courts_tot']));
          $("#registered-tot").html(numFormat.format(summaryStats['registered_tot']));
          $("#accordionStats").html(responseData[6]);
          $("#accordionStatsCourts").html(responseData[7]);
        },
        error: function (request, status, error) {
          alert(request.responseText);
        }
      });
     } else if (startDate != 0){
      $.ajax({
        url:"pagination_AfricanOrigins.php",
        method:"POST",
        dataType: "json",
        data:{page: page,id:id,columnName:columnName,startDate:startDate,EndDate:EndDate, reg:reg,advsearch:advquery},
        success:function(responseData){

          $('#content').html(responseData[0].html);
          $('#byRowStart').html(responseData[1]);
          if (responseData[2] > returnedTotal) {
            $("#recordsTo").html(returnedTotal);
          }else{
            $('#recordsTo').html(responseData[2]);
          }
          summaryStats = responseData[5];
          window.myBar.data.datasets[0].data = responseData[4];
          window.myBar.data.labels = responseData[3];
          window.myBar.update();
          $("#cases-tot").html(numFormat.format(summaryStats['cases_tot']));
          $("#enslaved-tot").html(numFormat.format(summaryStats['enslaved_tot']));
          $("#liberated-tot").html(numFormat.format(summaryStats['liberated_tot']));
          $("#courts-tot").html(numFormat.format(summaryStats['courts_tot']));
          $("#registered-tot").html(numFormat.format(summaryStats['registered_tot']));
          $("#accordionStats").html(responseData[6]);
          $("#accordionStatsCourts").html(responseData[7]);
        },
        error: function (request, status, error) {
        alert(request.responseText);
        }
      });
    } else {
      var selectedDropdown = <?php echo $selectedDropdown;?>;
      $.ajax({
        url:"pagination_AfricanOrigins.php",
        method:"POST",
        dataType: "json",
        data:{page:page,id:id,columnName:columnName,selectedDropdown:selectedDropdown,reg:reg,advsearch:advquery},
        success:function(responseData){
          $('#content').html(responseData[0].html);
          $('#byRowStart').html(responseData[1]);
          if (responseData[2] > returnedTotal) {
            $("#recordsTo").html(returnedTotal);
          }else{
            $('#recordsTo').html(responseData[2]);
          }
          window.myBar.data.datasets[0].data = responseData[4];
          window.myBar.data.labels = responseData[3];
          window.myBar.update();
          summaryStats = responseData[5];
          $("#cases-tot").html(numFormat.format(summaryStats['cases_tot']));
          $("#enslaved-tot").html(numFormat.format(summaryStats['enslaved_tot']));
          $("#liberated-tot").html(numFormat.format(summaryStats['liberated_tot']));
          $("#courts-tot").html(numFormat.format(summaryStats['courts_tot']));
          $("#registered-tot").html(numFormat.format(summaryStats['registered_tot']));
          $("#accordionStats").html(responseData[6]);
          $("#accordionStatsCourts").html(responseData[7]);
        },
        error: function (request, status, error) {
          alert(request.responseText);
        }
      });
     }
  }
  });
});
</script>
