// PlayerMarkers for use with MapMarkers bukkit plugin.
// Original code by TJ09, modified and cleaned up for use with Overviewer 0.1.3+ by Michael Writhe and Hauke Schade
// https://github.com/overviewer/Minecraft-Overviewer-Addons
// Please make sure you have markers.json accessable to the web.

var playerMarkers = null;
var infoWindowsArray = null;
var foundPlayerMarkers = null;
var refreshTime = 15;
var avatarserver = "http://cerato.writhem.com/player-avatar.php?player=";

setInterval(updatePlayerMarkers, 1000 * refreshTime);
setTimeout(updatePlayerMarkers, 1000);

function prepareInfoWindow(infoWindow, item) {
	var c = "<div class=\"infoWindow\" style='width: 300px'><img src='"+avatarserver+item.msg+"'/><h1>"+item.msg+"</h1><p style='text-align: left;'>X: "+item.x+"<br />Y: "+item.y+"<br />Z: "+item.z+"</p></div>";
	if (c != infoWindow.getContent())
		infoWindow.setContent(c);
}

function updatePlayerMarkers() {
	$.getJSON('markers.json?'+Math.round(new Date().getTime()), function(data) {
		if(playerMarkers == null)
			playerMarkers = [];
		if (infoWindowsArray == null)
			infoWindowsArray = [];
		var foundPlayerMarkers = [];
		for (i in playerMarkers)
			foundPlayerMarkers.push(false);

		for (i in data) {
			var item = data[i];
			if(item.id != 4) continue; //this is some compatibility
			var converted = overviewer.util.fromWorldToLatLng(item.x, item.y, item.z);

			//if found, change position
			var found = false;
			for (player in playerMarkers) {
				if(playerMarkers[player].getTitle() == item.msg) {
					foundPlayerMarkers[player] = found = true;
					playerMarkers[player].setPosition(converted);
					if(playerMarkers[player].getMap() == null)
						playerMarkers[player].setMap(overviewer.map);
					prepareInfoWindow(infoWindowsArray[player], item);
					break;
				}
			}
			//elsenew marker
			if(!found) {
				var marker =  new google.maps.Marker({
					position: converted,
					map: overviewer.map,
					title: item.msg,
					icon: avatarserver+item.msg,
					visible: true,
					zIndex: 999
				});
				playerMarkers.push(marker);
				var infowindow = new google.maps.InfoWindow({content: item.msg});
				prepareInfoWindow(infowindow, item);
				google.maps.event.addListener(marker, 'click', function(event) {
					var i = 0;
					for (playerindex in playerMarkers) {
						if (playerMarkers[playerindex].title == this.title) {
							i = playerindex;
							break;
						}
					}
					if(infoWindowsArray[i].getMap()){
						infoWindowsArray[i].close()
					} else {
						infoWindowsArray[i].open(overviewer.map, playerMarkers[i]);
					}
				});
				infoWindowsArray.push(infowindow);
				foundPlayerMarkers.push(true);
			}
		}

		//remove unused markers
		for (i in playerMarkers) {
			if (!foundPlayerMarkers[i]) {
				playerMarkers[i].setMap(null);
			}
		}
	});
}
