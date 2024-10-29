<?php 
/* Get Data for stats by region - Summary */
if(!$noResults){
    $regionHtml = '';
    foreach($Regs as $rid => $rdata){
        $region_name = $rdata['name'];
        $region_id = $rid;
        $region_liberated = number_format($rdata['liberated_tot']);

        $regionHtml .= '<div class="accordion-item">';
        $regionHtml .= '<h2 class="accordion-header" id="heading'.$region_id.'">';
        $regionHtml .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$region_id.'" aria-expanded="true" aria-controls="collapse'.$region_id.'">'.$region_name.' - '.$region_liberated.'</button>';
        $regionHtml .= '</h2>';
        $regionHtml .= '<div id="collapse'.$region_id.'" class="accordion-collapse collapse" aria-labelledby="heading'.$region_id.'" data-bs-parent="#accordionStats">';
        $regionHtml .= '<div class="accordion-body">';

        /*SQL - Places*/ 
        if(array_key_exists($rid,$Regs_Places)){
            $places_arr = $Regs_Places[$rid];
            $placeHtml = '<div class="table-responsive-md"><table class="table table-striped"><thead><tr><th>Place</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
            foreach($places_arr as $pid):
            $pdata = $Places[$pid];
            $placeHtml.='<tr>';
            $placeHtml.='<td>'.$pdata['name'].'</td>';
            $placeHtml.='<td>'.number_format($pdata['liberated_tot']).'</td>';
            $placeHtml.='<td>'.number_format($pdata['registered_tot']).'</td>';
            $placeHtml.='</tr>';
            endforeach;
            $placeHtml.='</tbody></table></div>';
        } else {
            $placeHtml = '<div class="table-responsive-md"><table class="table table-striped"><thead><tr><th>Place</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
            $placeHtml.='<tr>';
            $placeHtml.='<td></td>';
            $placeHtml.='<td></td>';
            $placeHtml.='<td></td>';
            $placeHtml.='</tr>';
            $placeHtml.='</tbody></table></div>';
        }
        

        $regionHtml .= $placeHtml;
        $regionHtml .= '</div></div></div>';
    }
} else {
// No results found
}

/* Get Data for stats by govt departments - Summary */
if(!$noResults){
    $deptsHtml = '';
    foreach($Govts as $gid =>$gdata){
        $dept_name = $gdata['name'];
        $dept_id = $gid;
        $dept_liberated = number_format($gdata['liberated_tot']);
        $dept_registered = number_format($gdata['registered_tot']);

        $deptsHtml .= '<div class="accordion-item">';
        $deptsHtml .= '<h2 class="accordion-header" id="heading'.$dept_id.'">';
        $deptsHtml .= '<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$dept_id.'" aria-expanded="true" aria-controls="collapse'.$dept_id.'">'.$dept_name.' - '.$dept_liberated.'</button>';
        $deptsHtml .= '</h2>';
        $deptsHtml .= '<div id="collapse'.$dept_id.'" class="accordion-collapse collapse" aria-labelledby="heading'.$dept_id.'" data-bs-parent="#accordionStatsCourts">';
        $deptsHtml .= '<div class="accordion-body">';

        /*SQL - Courts*/ 
        $courts_arr = $Govts_Courts[$gid];
        $courtHtml = '<div class="table-responsive-md"><table class="table table-striped"><thead><tr><th>Court</th><th>Liberated Africans</th><th>Registered Africans</th></tr></thead><tbody>';
        foreach($courts_arr as $cid){
        $cdata = $Courts[$cid];
        $courtHtml.='<tr>';
        $courtHtml.='<td>'.$cdata['name'].'</td>';
        $courtHtml.='<td>'.number_format($cdata['liberated_tot']).'</td>';
        $courtHtml.='<td>'.number_format($cdata['registered_tot']).'</td>';
        $courtHtml.='</tr>';
        }
        $courtHtml.='</tbody></table></div>';

        $deptsHtml .= $courtHtml;
        $deptsHtml .= '</div></div></div>';
    }
} else {
// No results found
}

if(!$noResults){
    $returnedTotal = $Stats['cases_tot'];
    $totalPages = ceil($returnedTotal/$perPage);
} else {
    $returnedTotal = 0;
    $totalPages = 1;
}

?>