<?php
//header('Content-Type: application/json'); 
function Covid19ImpactEstimator($data){
$sr=json_encode($data);
$sr=json_decode($sr);
if($sr->periodType=="days"){
  $timeToElapse=$sr->timeToElapse;
}else if($sr->periodType=="weeks"){
  $timeToElapse=($sr->timeToElapse)*7;
}else{
  $timeToElapse=($sr->timeToElapse)*30;
}
$data=array("data"=>$sr);
$estimate=array("estimate"=>array_merge(impact($sr,$timeToElapse),severeImpact($sr,$timeToElapse)));
$all_array=array_merge($data,impact($sr,$timeToElapse),severeImpact($sr,$timeToElapse));

print_r($all_array);
return $all_array;
}
  function  impact($s1,$timeToElapse){
    $currentInfected=$s1->reportedCases*(10);
    $factor=(int)((int)($timeToElapse)/3);
    $infectionsByRequestedTime=$currentInfected*(pow(2,$factor));
    $severeCasesByRequestedTime=0.15*$infectionsByRequestedTime;
    $val=(0.35*($s1->totalHospitalBeds))-$severeCasesByRequestedTime;
    if($val<0){$hospitalBedsByRequestedTime=(floor($val*-1)*-1);}
      else{$hospitalBedsByRequestedTime=floor($val);}
    $casesForICUByRequestedTime=0.05*$infectionsByRequestedTime;
    $casesForVentilatorsByRequestedTime=(int)(0.02*$infectionsByRequestedTime);
     $dollarsInFlight=(int)(($s1->region->avgDailyIncomeInUSD*$infectionsByRequestedTime*$s1->region->avgDailyIncomePopulation)/$timeToElapse);
     $impact=array("currentlyInfected"=>$currentInfected,"infectionsByRequestedTime"=>$infectionsByRequestedTime,"severeCasesByRequestedTime"=>$severeCasesByRequestedTime,"hospitalBedsByRequestedTime"=>$hospitalBedsByRequestedTime,"casesForICUByRequestedTime"=>$casesForICUByRequestedTime,"casesForVentilatorsByRequestedTime"=>$casesForVentilatorsByRequestedTime,"dollarsInFlight"=>round($dollarsInFlight,2));
return array("impact"=>$impact);
  }
  function severeImpact($s1,$timeToElapse){
     $currentInfected=$s1->reportedCases*(50);
      $factor=(int)((int)($timeToElapse)/3);
    $infectionsByRequestedTime=$currentInfected*(pow(2,$factor));
    $severeCasesByRequestedTime=0.15*$infectionsByRequestedTime;
    $val=(0.35*($s1->totalHospitalBeds))-$severeCasesByRequestedTime;
    if($val<0){$hospitalBedsByRequestedTime=(floor($val*-1)*-1);}
      else{$hospitalBedsByRequestedTime=floor($val);}
    $casesForICUByRequestedTime=0.05*$infectionsByRequestedTime;
    $casesForVentilatorsByRequestedTime=(int)(0.02*$infectionsByRequestedTime);
    $dollarsInFlight=(int)(($s1->region->avgDailyIncomeInUSD*$infectionsByRequestedTime*$s1->region->avgDailyIncomePopulation)/$timeToElapse);
    $severeImpact=array("currentlyInfected"=>$currentInfected,"infectionsByRequestedTime"=>$infectionsByRequestedTime,"severeCasesByRequestedTime"=>$severeCasesByRequestedTime,"hospitalBedsByRequestedTime"=>$hospitalBedsByRequestedTime,"casesForICUByRequestedTime"=>$casesForICUByRequestedTime,"casesForVentilatorsByRequestedTime"=>$casesForVentilatorsByRequestedTime,"dollarsInFlight"=>round($dollarsInFlight,2));
return array("severeImpact"=>$severeImpact);
  }


