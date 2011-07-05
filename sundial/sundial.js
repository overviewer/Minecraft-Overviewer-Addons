/*

This software is copyright by sunkid <sunkid@iminurnetz.com> and is distributed under a dual license:

 Non-Commercial Use:
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
 Commercial Use:
    Please contact sunkid@iminurnetz.com

*/
var sundialworkingDir = '/sundial';  // default should work, unless you have something like example.com/~user/map/playerlist. NO TRAILING SLASH!

// how many times per second to run the rotate function below
var syncEvery = 1;

var d = new Date();
var serverClock = 0;

// milliseconds of wall time to be added for server "date"
// sunrise is at 22800, noon at 6000, sunset at 13200, and midnight at 18000
var tAdjust = 21600*1000;
	
// rotate the dial and refresh the date/time information
function sundial_rotate() {
	// are we ready yet?
	if (serverClock === 0) return;

	// refresh the server "date" and time
	d.setHours(Math.floor(serverClock/1000));	// 100 ticks to the hour, 24 hours to the day
	d.setMinutes(Math.floor((serverClock%1000)/50)); // 20 ticks per RL second
	d.setSeconds(Math.floor((serverClock%1000)%50));
	//d.setTime(serverClock*3600+tAdjust);
	$('#date').text(d.toUTCString().substr(0,16));
	$('#time').text(d.toUTCString().substr(17,8));

	// rotate the dial
	var deg = (serverClock%24000)*-0.015-30;
	$('#dial').rotate(deg);

	// increment the server clock
	serverClock = serverClock + syncEvery*20;
}

// sync with the actual clock from the server
function sundial_sync() {
 	$.getJSON('./json.php?q=time&c=',
		function(data) {
			var newServerClock = data['time'];
			$('#debug').text("drift: " + (serverClock - newServerClock ));
			serverClock = newServerClock;
		});
}

$(document).ready(
	function() {
        try {
            if ($('#mcdial')) {
            
                var mcplStyleDiv = document.createElement("DIV");
                mcplStyleDiv.innerHTML =
'    <div id="mcdial" class="mcdial">'+
'      <img src="'+sundialworkingDir+'/mask.png" id="mask">'+
'      <img src="'+sundialworkingDir+'/dial.png" id="dial">'+
'      <div id="date" class="datetime">DATE</div>'+
'      <div id="time" class="datetime">TIME</div>'+
//'      <div id="debug" class="datetime" style="visibility: hidden;">&nbsp;</div>'+
'      <div id="debug" class="datetime" >&nbsp;</div>'+
'      <div id="sdcredits" class="datetime">'+
'        <a class="sdlink" href="https://github.com/sunkid/minecraft-sundial">minecraft sundial</a>'+
'      </div>'+
'  </div>';
                $(mcplStyleDiv).appendTo('body');
            }

		setInterval(sundial_sync, 360000);
		setInterval(sundial_rotate, syncEvery*1000);

		sundial_sync();
            overviewer.util.debug('[plugin] sundial loaded');
        } catch (e) {
            overviewer.util.debug('[plugin] sundial NOT loaded: '+e);
        }
	});
