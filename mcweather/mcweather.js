// coded by Michael Writhe michael at writhem dot com
// https://github.com/pironic/Minecraft-Overviewer-Addons 
// Thank you to Paul Davey for allowing the use of his icons which can be found at:
// http://mattahan.deviantart.com/art/Buuf-37966044

var forecastAccuracy = 25000; // number of ticks to predict within. 20 ticks a second.
var workingDIR = '/mcweather';  // default should work, unless you have something like example.com/~user/map/mcweather. NO TRAILING SLASH!

// --END OF CONFIG--
// just set some defaults while initalizing the variables.
var day = true;
var imgCurrent = 'd_r0_l0.png';
var current = '<img src="'+workingDIR+'/'+imgCurrent+'" height="30" width="30">';
var imgForecast = 'd_r0_l0.png';
var txtForecast = 'Loading Forecast';
var forecast = '<img src="'+workingDIR+'/'+imgCurrent+'" height="30" width="30">'+ txtForecast;

var rainTime = 0;
var thunderTime = 0;
var gameTime = 0;


// sync with the actual clock from the server
function mcw_sync() {
 	$.getJSON(workingDIR+'/getServerWeather.php',
		function(data) {
            rainTime = data['rainTime'] /20;
            thunderTime = data['thunderTime'] /20;
            gameTime = data['time'];
            
            $('#mcwdebug').text("R: " + rainTime + " T: " + thunderTime + " D: " + gameTime);
            
            // current conditions
            if (day) {
                // day
                if (data['raining']) {
                    // day wet
                    if(data['thundering']) {
                        // day storm
                        imgCurrent = 'd_r1_l1.png';
                    } else {
                        // day wet only
                        if (rainTime > 60) {
                            // lots of rain
                            imgCurrent = 'd_r2_l0.png';
                        } else {
                            // light rain, almost over
                            imgCurrent = 'd_r1_l0.png';
                        }
                    }
                } else {
                    // day dry
                    if (rainTime < 300) {
                        // getting cloudy, could rain in less than 5 minutes
                        imgCurrent = 'd_r0_l1.png';
                    } else {
                        // sunny!
                        imgCurrent = 'd_r0_l0.png';
                    }
                }
            } else {
                //night
                if (data['raining']) {
                    // night wet
                    if(data['thundering']) {
                        // night storm
                        imgCurrent = 'n_r1_l1.png';
                    } else {
                        // night wet only
                        imgCurrent = 'n_r1_l0.png';
                    }
                } else {
                    // night dry
                    imgCurrent = 'n_r0_l0.png';
                }

            }
            current = '<img src="'+workingDIR+'/'+imgCurrent+'" height="50" width="50">';
            $('#mcwcurrent').html(current);

            // forecast
            if (data['raining']) {
                //currently raining
                if (thunderTime >= rainTime) {
                    // 0% chance of storm, before stop rain
                    imgForecast = 'd_r0_l0.png';
                    txtForecast = 'clear skies ('+Math.floor(rainTime)/60+':'+Math.floor(rainTime)%60+')';
                } else {
                    // 100% chance of storm before end of rain
                    imgForecast = 'd_r1_l1.png';
                    txtForecast = '100% chance storm('+Math.floor(thunderTime/60)+':'+Math.floor(thunderTime%60)+')';
                }
            } else {
                // currently dry
                if (thunderTime >= rainTime) {
                    // This can be expanded to provide something close to a % chance of thunder
                    if (Math.abs(rainTime-thunderTime) < forecastAccuracy) {
                       // reasonable chance of it thundering AND raining
                        imgForecast = 'd_r1_l1.png';
                        txtForecast = '70% chance rain('+Math.floor(rainTime)+'sec) then storm('+Math.floor(thunderTime)+'sec)';
                    } else {
                        // Only rain
                        imgForecast = 'd_r2_l0.png';
                        txtForecast = 'High chance rain('+Math.floor(rainTime)+'sec)';
                    }
                } else {
                    // Only rain
                    imgForecast = 'd_r2_l0.png';
                    txtForecast = 'High chance rain ('+Math.floor(rainTime/60)+':'+Math.floor(rainTime%60)+')';
                } 
            }

            forecast = '<img src="'+workingDIR+'/'+imgForecast+'" height="50" width="50"><br />'+txtForecast;
            $('#mcwforecast').html(forecast);
        });
}

function mcw_clock() {
	if (gameTime === 0) return;

    rainTime = rainTime-1;
    thunderTime = thunderTime-1;
    gameTime = gameTime + 20;
    if(rainTime < 1 || thunderTime < 1 || gameTime > 23999) mcw_sync();
    
    // sunrise is at 22800, noon at 6000, sunset at 13200, and midnight at 18000
    if (gameTime < 13200 || gameTime > 22800) {
        // day
        if(!day) {
            day = true;
            mcw_sync();
        }
    } else {
        // night
        if(day) {
            day = false;
            mcw_sync();
        }
    }
	
    // will change the debug div to output the rain, thunder and server times.
    $('#mcwdebug').text("R: " + rainTime + " T: " + thunderTime + " G: " + gameTime );
}

function convertTime() {
    return 
}

$(document).ready(
	function() {
		mcw_sync();
		setInterval(mcw_sync, 1000*120); // will sync with the server every 2 minutes by default.
		setInterval(mcw_clock, 1000); // update the internal clocks and debug info every 1 second.
	});
