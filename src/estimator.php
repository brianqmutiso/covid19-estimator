<?php
header('Content-Type: application/json'); 
function Covid19ImpactEstimator(){
$s1='{"region": {"name": "Africa","avgAge": 19.7,"avgDailyIncomeInUSD": 4,"avgDailyIncomePopulation": 0.73},"periodType": "days","timeToElapse": 38,
 "reportedCases": 2747,"population": 92931687,"totalHospitalBeds": 678874}';
$sr=json_decode($s1);
$data=array("data"=>$sr);
$estimate=array("estimate"=>array_merge(impact($sr),severeImpact($sr)));
$all_array=array_merge($data,$estimate);
$all=json_encode($all_array,JSON_FORCE_OBJECT);
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
print_r($all);
}
  function  impact($s1){
    $currentInfected=$s1->reportedCases*(10);
    $factor=(int)((int)($s1->timeToElapse)/3);
    $infectionsByRequestedTime=$currentInfected*(pow(2,$factor));
    $severeCasesByRequestedTime=0.15*$infectionsByRequestedTime;
    $hospitalBedsByRequestedTime=(int)(0.35*($s1->totalHospitalBeds))-$severeCasesByRequestedTime;
    $casesForICUByRequestedTime=0.05*$infectionsByRequestedTime;
    $casesForVentilatorsByRequestedTime=(int)(0.02*$infectionsByRequestedTime);
     $dollarsInFlight=$infectionsByRequestedTime*$s1->region->avgDailyIncomePopulation*$s1->region->avgDailyIncomeInUSD*$s1->timeToElapse;
     $impact=array("currentInfected"=>$currentInfected,"infectionsByRequestedTime"=>$infectionsByRequestedTime,"severeCasesByRequestedTime"=>$severeCasesByRequestedTime,"hospitalBedsByRequestedTime"=>$hospitalBedsByRequestedTime,"casesForICUByRequestedTime"=>$casesForICUByRequestedTime,"casesForVentilatorsByRequestedTime"=>$casesForVentilatorsByRequestedTime,"dollarsInFlight"=>round($dollarsInFlight,2));
return array("impact"=>$impact);
  }
  function severeImpact($s1){
     $currentInfected=$s1->reportedCases*(50);
      $factor=(int)((int)($s1->timeToElapse)/3);
    $infectionsByRequestedTime=$currentInfected*(pow(2,$factor));
    $severeCasesByRequestedTime=0.15*$infectionsByRequestedTime;
    $hospitalBedsByRequestedTime=(int)(0.35*($s1->totalHospitalBeds))-$severeCasesByRequestedTime;
    $casesForICUByRequestedTime=0.05*$infectionsByRequestedTime;
    $casesForVentilatorsByRequestedTime=(int)(0.02*$infectionsByRequestedTime);
    $dollarsInFlight=$infectionsByRequestedTime*$s1->region->avgDailyIncomePopulation*$s1->region->avgDailyIncomeInUSD*$s1->timeToElapse;
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
//Covid19ImpactEstimator();
