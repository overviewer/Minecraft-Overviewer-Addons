// PlayerMarkers for use with MapMarkers bukkit plugin.
// Original code by TJ09, modified and cleaned up for use with Overviewer 0.1.3+ by Michael Writhe
// https://github.com/pironic/Minecraft-Overviewer-Addons
// Please make sure you have markers.json accessable to the web.

var playerMarkers = null;
var warpMarkers = [];
var refreshTime = 15;

function deletePlayerMarkers() {
  if (playerMarkers) {
    for (i in playerMarkers) {
      playerMarkers[i].setMap(null);
    }
    playerMarkers = null;
  }
}

setInterval(loadPlayerMarkers, 1000 * refreshTime);
setTimeout(loadPlayerMarkers, 1000);

function preparePlayerMarker(marker,item) {
	var c = "<div class=\"infoWindow\" style='width: 300px'><img src='http://cerato.writhem.com/player-avatar.php?player="+item.msg+"'/><h1>"+item.msg+"</h1></div>";
	var infowindow = new google.maps.InfoWindow({content: c});
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(overviewer.map,marker);
	});
}

function loadPlayerMarkers() {
	$.getJSON('markers.json', function(data) {
		deletePlayerMarkers();
		playerMarkers = [];
		for (i in data) {
			var item = data[i];
			if(item.id != 4) continue;
			var converted = overviewer.util.fromWorldToLatLng(item.x, item.y, item.z);
			var marker =  new google.maps.Marker({
				position: converted,
				map: overviewer.map,
				title: item.msg,
				icon: 'http://cerato.writhem.com/player-avatar.php?player='+item.msg,
				visible: true,
				zIndex: 999
			});
			playerMarkers.push(marker);
			preparePlayerMarker(marker,item);
		}
	});
}