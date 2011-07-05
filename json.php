<?php
error_reporting(E_ALL);
ini_set('display_errors','On');
 
require('JSONAPI.php'); // get this file at: https://github.com/alecgorge/jsonapi/raw/master/sdk/php/JSONAPI.php
$DefaultWorld = "world";
require('json.config.php');
$api = new JSONAPI($host,$port, $username, $password,$salt);

// seconds, minutes, hours, days
//$expires = 60*60*24*14;
$expires = 1; // cache for at most 1 second
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
 

$output = array();
if (@$_GET["q"] == "playercount")
{
    $result = $api->call("getPlayerCount");
    $output = array( 'PlayerCount' => @$result["success"]);
    if ($output['PlayerCount'] == "") $output['PlayerCount'] = 0;
}
else if (@$_GET["q"] == "playerlimit")
{
    $result = $api->call("getPlayerLimit");
    $output = array( 'PlayerLimit' => @$result["success"]);
    if ($output['PlayerLimit'] == "") $output['PlayerLimit'] = 0;
}
else if (@$_GET["q"] == "players")
{
    $showspawns = @$_GET["spawn"] == "show";
    $formatstring = "Ymd H:i:s";
    $MARKER_SPAWN_ID  = 0;
	$MARKER_PLAYER_ID = 4;
    
    $result = $api->call("getPlayers");
    //print "<pre>";
    //var_dump($result);
    
    if (is_array(@$result["success"]))    
    foreach(@$result["success"] as $player)
    {
        $player_formated = array();
        $player_formated['x'] = $player['location']['x'];
        $player_formated['y'] = $player['location']['y'];
        $player_formated['z'] = $player['location']['z'];
        $player_formated['world'] = '';
        $player_formated['msg'] = $player['name'];
        $player_formated['id'] = $MARKER_PLAYER_ID;
        $player_formated['timestamp'] = date($formatstring);
        
        $output[] = $player_formated;
    }
}
else if (@$_GET["q"] == "weather")
{
    $world = @$_GET["world"]; 
    if ($world == "") $world = $DefaultWorld;    
    $result = $api->callMultiple(array("getWorld","getWeatherDuration","getThunderDuration"),array(array($world),array($world),array($world)));    
    $output['time'] = @($result["success"][0]['success']['time']);
    $output['raining'] = @($result["success"][0]['success']['hasStorm']);
    $output['thundering'] = @($result["success"][0]['success']['isThundering']);    
    $output['rainTime'] = @($result["success"][1]['success']);
    $output['thunderTime'] = @($result["success"][2]['success']);   
}
else if (@$_GET["q"] == "time" || @$_GET["q"] == "fulltime")
{
    $world = @$_GET["world"]; 
    if ($world == "") $world = $DefaultWorld;
    $result = $api->call("getWorld",array($world));
    $result = @$result["success"];
    if (isset($result))
    {
        if ($_GET["q"] == "fulltime")
            $output['time'] = $result['fullTime'];
        else
            $output['time'] = $result['time'];
    }    
}
print json_encode($output);

?>
