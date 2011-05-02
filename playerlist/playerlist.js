var playerMarkers = null;
var warpMarkers = [];
var PlayerNames = null;
var PlayerCount = 0;
var positionTop = '120px';
var positionRight = '14px';
var positionWidth = '150px';
var mcplworkingDIR = '/playerlist';  // default should work, unless you have something like example.com/~user/map/playerlist. NO TRAILING SLASH!


function deletePlayerMarkers() {
  if (playerMarkers) {
    for (i in playerMarkers) {
      playerMarkers[i].setMap(null);
    }
    playerMarkers = null;
	PlayerNames = null;
	PlayerCount = 0;
  }
}

function preparePlayerMarker(marker,item) {
	var c = '<div class="infoWindow" style="width: 240px;" align="center"><img src="http://cerato.writhem.com/player-avatar.php?player=' + item.msg + '&usage=info"><font style="font-family:Arial; font-size:20px;"><b>' + item.msg + '</b></font></div>';
	var infowindow = new google.maps.InfoWindow({content: c});
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(overviewer.map,marker);
	});
}

function loadPlayerMarkers() {
    $.getJSON('markers.json', function(data) {
        deletePlayerMarkers();
        playerMarkers = [];
		PlayerNames = [];
		PlayerCount = 0;

        for (i in data) {
            var item = data[i];
            var converted = overviewer.util.fromWorldToLatLng(item.x, item.y, item.z);
			
			var perPixel = 1.0 / (overviewerConfig.CONST.tileSize * Math.pow(2, overviewerConfig.map.maxZoom));

			var lng = 0.5 - (1.0 / Math.pow(2, overviewerConfig.map.maxZoom + 1));
			var lat = 0.5;
					
			lng += 12 * item.x * perPixel;
			lat -= 6 * item.x * perPixel;
					
			lng += 12 * item.z * perPixel;
			lat += 6 * item.z * perPixel;
					
			lat += 12 * (128 - item.y) * perPixel;

			lng += 12 * perPixel;
			lat += 18 * perPixel;
			
			PlayerNames.push('<!-- ' + item.msg.toLowerCase() + ' -->&nbsp;<a href="javascript:overviewer.map.panTo(overviewer.util.fromWorldToLatLng('+item.x+','+item.y+','+item.z+'));"><img src="http://cerato.writhem.com/player-avatar.php?player=' + item.msg + '&usage=list" border="0" /></a>&nbsp;' + item.msg + '<br /> ');
			PlayerCount++;
            
			var marker = new google.maps.Marker({
                    position: converted,
                    map: overviewer.map,
                    title: item.msg,
                    icon: 'http://cerato.writhem.com/player-avatar.php?player=' + item.msg + '&usage=marker'
            });
			playerMarkers.push(marker);
			preparePlayerMarker(marker, item);
         }
		
		$("#mcplOnline").empty();

        var mcplOutput = "loading";
		if(PlayerCount == 0) {
			mcplOutput = '&nbsp;<a href="javascript:overviewer.map.panTo(overviewer.util.fromWorldToLatLng('+
            overviewerConfig.map.center[0]+','+
            overviewerConfig.map.center[1]+','+
            overviewerConfig.map.center[2]+'));"><img src="'+mcplworkingDir+'/home-list.png" border="0" /></a>&nbsp;No Players Online';
            $('#mcplOnline').html(mcplOutput);
		} else if (PlayerCount == 1) {
			PlayerNames.sort();
			
            mcplOutput = '&nbsp;<a href="javascript:overviewer.map.panTo(overviewer.util.fromWorldToLatLng('+overviewerConfig.map.center[0]+','+overviewerConfig.map.center[1]+','+overviewerConfig.map.center[2]+'));"><img src="'+mcplworkingDir+'/home-list.png" border="0" /></a>&nbsp;<font color="black">' + PlayerCount + '</font> Player Online:<br /><br />' + PlayerNames.join(" ");
            $('#mcplOnline').html(mcplOutput);
            overviewer.util.debug('[plugin] PlayerList.mcplOnline = '+PlayerCount);
		} else {
			PlayerNames.sort();
			
			$("#mcplOnline").html('&nbsp;<a href="javascript:overviewer.map.panTo(overviewer.util.fromWorldToLatLng('+
            overviewerConfig.map.center[0]+','+
            overviewerConfig.map.center[1]+','+
            overviewerConfig.map.center[2]+'));"><img src="'+mcplworkingDir+'/home-list.png" border="0" /></a>&nbsp;<font color="black">' + PlayerCount + '</font> Players Online:<br /><br />' + PlayerNames.join(" "));
		}
	});
}

$(document).ready(
	function() {
        try {
            if ($('#mcplOnline')) {
                /* OLD FORMAT
                <div id="mcplayerlist" style="position:absolute; top:120px; right:14px; width:150px; height:*;border:solid;border-color:#FFFFFF;border-width:1px;color:#333;font-family:Arial;    background-color: rgba(255,255,255,0.55);">
                    <strong>
                        <div align="center" style="font-size:80%; position:relative; top:5px;">&nbsp;Online Players&nbsp;</div>
                        <hr style="color:#FFFFFF; background:#FFFFFF; heigth:1px;" />
                        <div style="font-size:80%; left:10px; bottom:10px; top:5px;" id="Spieler"></div>
                    </strong>
                </div>
                */
                var mcplStyleDiv = document.createElement("DIV");
                mcplStyleDiv.style.position = 'absolute';
                mcplStyleDiv.style.top = positionTop;
                mcplStyleDiv.style.right = positionRight;
                mcplStyleDiv.style.width = positionWidth;
                mcplStyleDiv.style.border = 'solid';
                mcplStyleDiv.style.bordercolor = '#333';
                mcplStyleDiv.style.borderwidth = '1';
                mcplStyleDiv.style.color = '#FFFFFF';
                mcplStyleDiv.style.fontFamily = 'Arial,Sans-Serif';
                mcplStyleDiv.style.fontSize = '11px';
                mcplStyleDiv.style.backgroundcolor = 'rgba(255,255,255,0.55)';
                mcplStyleDiv.id = 'mcplayerlist';

                var mcplStrong = document.createElement("STRONG");
                
                var mcplTitleDiv = document.createElement("DIV");
                mcplTitleDiv.align = 'center';
                mcplTitleDiv.style.position = 'relative';
                mcplTitleDiv.style.height = '1px';
                mcplTitleDiv.innerHTML = "&nbsp;Online Players&nbsp;";
                mcplStrong.appendChild(mcplTitleDiv);
                
                var mcplHR = document.createElement("HD");
                mcplHR.style.color = '#FFFFFF';
                mcplHR.style.background = '#FFFFFF';
                mcplHR.style.height = '1px';
                mcplStrong.appendChild(mcplHR);

                var mcplOnlineDiv = document.createElement("DIV");
                mcplOnlineDiv.id = 'mcplOnline';
                mcplOnlineDiv.style.left = '10px';
                mcplOnlineDiv.style.bottom = '10px';
                mcplOnlineDiv.style.top = '25px';
                mcplOnlineDiv.innerHTML = "loading... ";
                mcplStrong.appendChild(mcplOnlineDiv);

                mcplStyleDiv.appendChild(mcplStrong);
                $(mcplStyleDiv).appendTo('body');
            }
            
            setInterval(loadPlayerMarkers, 1000 * 3);
            setTimeout(loadPlayerMarkers, 1000);

            loadPlayerMarkers();
            
            overviewer.util.debug('[plugin] PlayerList loaded');
        } catch (e) {
            overviewer.util.debug('[plugin] PlayerList NOT loaded: '+e);
        }
    });
