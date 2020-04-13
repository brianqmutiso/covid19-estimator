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
//$all=json_encode($all_array,JSON_FORCE_OBJECT);
/*if (isset($request->datas)) {
  if ($request->datas=="json") {
    logs();
    return json_decode($all,true);
  }
  if ($request->datas=="logs") {
   if (file_exists("logs.json")) {
   $logfile="logs.json";
$lof=file_get_contents("logs.json");
$all_val=json_decode("[".$lof."]",true);
foreach ($all_val as $key => $value) {
print_r($value['current_timestamp']."\t\t".$value['URL']."\t\t".$value['response']."\t\t".$value['time']."\n");} } 
    else{
      return "no logs";
    } }
if ($request->datas=="xml") {
$dat=json_encode($data);
$data=json_decode($dat,true);
$array = array ('xmldata' => 'xmldata',$data,impact($sr),severeImpact($sr),
);
$xml_data =xml_data($array, false );
logs();
return $xml_data->asXML();
}}
else{
 logs();
return json_decode($all,true);
}*/
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
     $dollarsInFlight=$infectionsByRequestedTime*$s1->region->avgDailyIncomePopulation*$s1->region->avgDailyIncomeInUSD*$timeToElapse;
     $impact=array("currentInfected"=>$currentInfected,"infectionsByRequestedTime"=>$infectionsByRequestedTime,"severeCasesByRequestedTime"=>$severeCasesByRequestedTime,"hospitalBedsByRequestedTime"=>$hospitalBedsByRequestedTime,"casesForICUByRequestedTime"=>$casesForICUByRequestedTime,"casesForVentilatorsByRequestedTime"=>$casesForVentilatorsByRequestedTime,"dollarsInFlight"=>round($dollarsInFlight,2));
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
    $dollarsInFlight=$infectionsByRequestedTime*$s1->region->avgDailyIncomePopulation*$s1->region->avgDailyIncomeInUSD*$timeToElapse;
    $severeImpact=array("currentInfected"=>$currentInfected,"infectionsByRequestedTime"=>$infectionsByRequestedTime,"severeCasesByRequestedTime"=>$severeCasesByRequestedTime,"hospitalBedsByRequestedTime"=>$hospitalBedsByRequestedTime,"casesForICUByRequestedTime"=>$casesForICUByRequestedTime,"casesForVentilatorsByRequestedTime"=>$casesForVentilatorsByRequestedTime,"dollarsInFlight"=>round($dollarsInFlight,2));
return array("severeImpact"=>$severeImpact);
  }
function xml_data( $data, $xml=false) {
   if (!$xml) {$xml = new \SimpleXMLElement("<root></root>"); }
    foreach($data as $key => $value) {
    if(is_array($value)) {
      if(!is_numeric($key)){
        $subnode = $xml->addChild($key);
        foreach ($value as $key => $value1) {
if (!is_array($value1)) {
$subnode->addChild("$key","$value1");}
else{$subnode1= $subnode->addChild($key);
   foreach ($value1 as $key => $value2) {
   $subnode1->addChild("$key","$value2");
   }} }  }
      else{
        xml_data($value, $xml); }}
    else {$xml->addChild("$key","$value");
    }}
  return $xml;
}
function logs(){
  $posts=array("current_timestamp"=>time(),"URL"=>"","response"=>http_response_code(),"time"=>"");
if (!file_exists("logs.json")) {
 $logfile="logs.json";
$lof=fopen($logfile, 'w');
fwrite($lof, json_encode($posts));
fclose($lof);
}else{
  $logfile="logs.json";
$lof=fopen($logfile, 'a');
fwrite($lof,",".json_encode($posts));
fclose($lof);}}

