<?php
//$q="Select (FLOOR(Field2 /10) * 10) AS Decade, SUM(Field9) AS amt from person GROUP by FLOOR(Field2/ 10)";
$q="Select Field2 AS Decade, SUM(Field9) AS amt from person GROUP by Field2";
$query = $conn->query($q);
while($total = $query->fetch(PDO::FETCH_ASSOC)){
$data[]=$total['Decade'];
$entries[]=$total['amt'];
}
?>

<h3 class="text-center pb-4"><b>Number of Liberated Africans vs. Year</b></h3>
<canvas id="histogram" width="100%" height="50%"></canvas>

<script>
    var barChartData = {
      labels: <?php echo json_encode($data); ?>,
      datasets: [{
        label: 'Liberated Africans',
        borderColor: '#DFB56B',
        borderWidth: 3,
        data: <?php echo json_encode($entries); ?>
      }]

    };
    window.onload = function() {
      var ctx = document.getElementById('histogram').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'line',
        
        data: barChartData,
        options: {
                   scales: {
                      yAxes: [{
                           ticks: {
                           beginAtZero: true
                           },
                           scaleLabel: {
                            display: true,
                             labelString: 'Number Of Liberated Africans',
                             fontSize: 16,
                             fontColor: '#000',
                           }
                       }],

                   
                      xAxes: [{
                          ticks: {
                            autoSkip: true,
                            maxTicksLimit: 20
                          },
                        scaleLabel: {
                          display: true,
                          labelString: 'Year',
                          fontSize: 16,
                          fontColor: '#000',
                        },
                      }],
                    },
                    elements: {
                        line: {
                          fill: false
                        }
                    },
                     legend: {
                      display: false //This will do the task
                    }
                }
        
      });
    };

</script>

