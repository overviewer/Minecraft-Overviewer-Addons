// PlayerMarkers for use with MapMarkers bukkit plugin.
// https://github.com/overviewer/Minecraft-Overviewer-Addons
// Please make sure you have markers.json accessable to the web.

var JSONFile            =    'markers.json'; //The JSON file containing the player data
var refreshTime         =   5; //How many seconds should we wait between updating the JSONFile.
var avatarserver        =   'http://overviewer.org/avatar/<playername>/head'; //The address for the player avatar script. 

var showPlayerMarkers   =   true; // Should we show the players moving around on the map?
var playerMarkers       =    []; //The array of player objects

var showPlayerList      =   true; // Use the built in player list format and show it on right?
var showPlayerCoords     =   false; // If true, will show the ingame coordinates of the player.
var playerListElement   =    '#player_list'; // default: #player_list. This option can be used to set a custom div format that has been inserted into the index page for player lists. If showPlayerList is true, this should be #player_list though.

var filterPlayersByWorld =  true; // if this is false, it will show all players, regardless of the world they are in. This should not be true if you are running muli-verse. Set this to false if you are having trouble showing any players online and are only running 1 world. default = true;

/**
 * Create a new Player Marker
 *
 * @param    location    google.maps.LatLng    The initial location of the marker
 * @param    map            google.maps.Map        The map where the marker shoudl be displayed (the overviewer map)
 * @param    name        string                The name of the player
 * @param    icon        string                The image icon of the player
 * @param    visible        boolean                True if the marker should be displayed
 * @return    google.maps.Marker
 */
function createPlayerMarker(location,map,name,icon,elevation,visible) {
    var marker =  new google.maps.Marker({
        position: location,
        map: map,
        title: name,
        icon: createMarkerImage(elevation,icon),
        visible: (showPlayerMarkers ? visible : false),
        zIndex: 999
    });
    return marker;
}

/**
 * Create a new MarkerImage, based on the elevation of the player. The Higher, the bigger.
 *
 * @param    elevation    int    The elevation (y) of the player ingame.
 * @param    icon        string                The image icon of the player
 * @return    google.maps.MarkerImage
 */
function createMarkerImage(elevation,icon) {
    // do a little error checking, make sure the player isn't flying into outerspace. if they are, render the size based as if they were at sky, not space.
    if(elevation < 0) elevation = 0;
    if(elevation > 256) elevation = 256;
    var markerSize = Math.round(0.0859375 * elevation + 10); //http://goo.gl/sf94W thanks Wolfram|Alpha
    //console.log(icon + " - " + elevation + " : " + markerSize); // debug only.
    
    var size                = new google.maps.Size(markerSize,markerSize);
    var markerImage         =  new google.maps.MarkerImage(icon);
    markerImage.size        = size;
    markerImage.scaledSize  = size;
    
    return markerImage;
}

/**
 *  Create a new Informational Window for a Player Marker
 *
 *  @param    name    string    The name of the player
 *  @return    goolge.maps.InfoWindow
 */
function createInfoWindow(name) {
    var html        = "<div class=\"infoWindow\" style='width: 300px'><img src='"+getAvatarURL(name)+"'/><h1>"+name+"</h1></div>";
    var infoWindow  = new google.maps.InfoWindow({content: html});
    return infoWindow;
}

/**
 * Create a new Listener for the Marker
 *
 * @param    marker        google.maps.Marker
 * @param    infoWindow    google.maps.InfoWindow
 * @return    google.maps.event.MapEventListener
 */
function createInfoWindowListener(marker,infoWindow) {
    var listener = google.maps.event.addListener(marker, 'click', function() {
        infoWindow.open(marker.getMap(),marker);
    });
    return listener;
}

/**
 * Create a new Player Listing
 *
 * @param    list    string    The html <ul> element ID to display the listing
 * @param    name    string    The name of the player
 * @return    jQuery
 */
function createPlayerListing(list,name) {
    $(list).append('<li id="li_'+name+'" style="background-image: url('+getAvatarURL(name)+'); background-repeat: no-repeat; padding-left: 18px;color: white;">'+name+' (hidden)</li>');
    return $('#li_'+name);
}

/**
 * Load the players JSON file and update the map
 *
 * @return void
 */
function loadPlayers() {
    $.ajax({
        url:JSONFile,
        dataType: 'json',
        cache: false,
        success: function(data) {
            for (var i in data) {
                var curTileSet = overviewer.mapView.options.currentTileSet;
                if (overviewerConfig.map.debug)
                    //console.log('Updating ' +data[i].msg + ' from  ' + curTileSet.get("world").get("name"));
                if (data[i].world != curTileSet.get("world").get("name") && filterPlayersByWorld) continue;
                if (data[i].id != 4) continue;

                var item            =    data[i];
                var name            =    item.msg;
                var world           =    item.world;
                var x               =    item.x;
                var y               =    item.y;
                var z               =    item.z;
                var display         =    item.display;
                var timestamp       =    new Date(item.timestamp);
                var icon            =    getAvatarURL(name);
                var location        =    overviewer.util.fromWorldToLatLng(x,y,z, curTileSet);
                var visible         =    (display!="hidden");

                /**
                 * If we receive a player that is not in the list, it must be created
                 */
                if (playerMarkers[name] == undefined) {
                    var marker      =    createPlayerMarker(location,overviewer.map,name,icon,y,visible); //create the marker
                    var infoWindow  =    createInfoWindow(name); //create the info window
                    var listener    =    createInfoWindowListener(marker,infoWindow); //create the listener on the marker for the info window
                    var listing     =    createPlayerListing(playerListElement,name,icon); //create the player listing

                    /**
                     * The player object
                     */
                    playerMarkers[name]    =    {
                        name:           name, //The player's name
                        marker:         marker, //The player's map marker
                        infoWindow:     infoWindow, //The player's informational window
                        listener:       listener, //The map marker listener
                        listing:        listing, //The player's listing in the <ul>
                        location:       location, //The player's map location'
                        timestamp:      timestamp, //The timestamp sent from the server
                        updated:        new Date(), //The last time JS updated (heard from) the player
                        removed:        false, //Has the player been removed from the map
                        x:              x, //The player's in-game X coordinate
                        y:              y, //The player's in-game Y coordinate
                        z:              z, //The player's in-game Z coordinate
                        icon:           icon, //The player's image icon
                        visible:        visible, //Is the player visible
                        world:          world //The player's in-game world.
                    }
                }
                playerMarkers[name].location    =    location; //Update the player's location
                playerMarkers[name].x           =    x;
                playerMarkers[name].y           =    y;
                playerMarkers[name].z           =    z;
                playerMarkers[name].visible     =    visible; //Update if the player is visible
                playerMarkers[name].updated     =    new Date(); //Update the last time JS heard from the player

                updatePlayer(name); //Update the player on the map
            }
            checkPlayers(); //Check for offline players
        }
    });
}

/**
 * Update the player on the map
 *
 * @param    name    string    The name of the player
 * @return    void
 */
function updatePlayer(name) {
    var player = playerMarkers[name];

    player.marker.setPosition(player.location); //Set the marker position on the map
    player.marker.setVisible((showPlayerMarkers ? player.visible : false)); //Set the marker visibility on the map
    player.marker.setIcon(createMarkerImage(player.y,player.icon)); //Set the icon again, with proper sizing
    player.infoWindow.setPosition(player.location); //Set the InfoWindow position on the map
    player.listing.toggle(true); //Set the listing to visible (default)

    /**
     * If the player has been removed from the map (went offline) and is now back
     *
     * We wouldn't be here unless the player is now online, but we had already created
     * this player, and we don't want to re-create it because that would be wasteful :)
     */
    if (player.removed) {
        player.marker.setMap(overviewer.map); //Set the marker's map (google's way of enabling the marker)
        player.infoWindow.setMap(overviewer.map); //Set the infoWindow's map (again google's way)
        player.infoWindow.close(); //Close the infoWindow (google automaticly opens an InfoWindow when it's map is set)
    }
    player.removed = false; //The player is no longer removed
    $(player.listing).unbind('click'); //We unbind clicking on the <li> by default (for hidden players)

    /**
     *If the player's visibility is set to false (through the in-game /hide command)
     */
    if (!player.visible) {
        player.infoWindow.close(); //close the InfoWindow (incase it was open at the time)
        $(player.listing).empty().append(player.name+' (hidden)'); //Empty the <li> and re-insert the player with (hidden) instead of the coordinates
    } else if (showPlayerCoords) {
        $(player.listing).empty().append(player.name+' ('+Math.round(player.x)+','+Math.round(player.y)+','+Math.round(player.z)+')'); //Empty the <li> and re-insert the player with their in-game coordinates (rounding for prettyness)
        if (showPlayerMarkers) { // only show the info window if the markers are enabled.
            /**
             *We re-bind the click event only if they are visible
             *This prevents clicking on the <li> to get the player's last location and a pointless InfoWindow
             */
            $(player.listing).click(function(){
                player.infoWindow.open(overviewer.map,player.marker);
            });
        }
    } else {
        $(player.listing).empty().append(player.name); //Empty the <li> and re-insert the player
        if (showPlayerMarkers) { // only show the info window if the markers are enabled.
            /**
             *We re-bind the click event only if they are visible
             *This prevents clicking on the <li> to get the player's last location and a pointless InfoWindow
             */
            $(player.listing).click(function(){
                player.infoWindow.open(overviewer.map,player.marker);
            });
        }
    }
}

/**
 * Check the players for inactivity and remove them if not updated
 *
 * @return void
 */
function checkPlayers() {
    var timeout    =    new Date(new Date()-3000); //The timeout date object
    /**
     *Iterate over all known players to check for their last update
     */
    for (var i in playerMarkers) {
        var player    =    playerMarkers[i];
        /**
         *If the player has not updated within the timeout window
         *They need to be removed, but only if we haven't already removed them
         */
        if (player.updated<timeout && !player.removed) {
            removePlayer(player.name);
        }
    }
}

/**
 * Remove a player from the map
 *
 * @param    name    string    The name of the player
 * @return void
 */
function removePlayer(name) {
    var player    =    playerMarkers[name];
    player.infoWindow.close(); //close the InfoWindow (probably not needed, but let's be consistant)
    player.infoWindow.setMap(null); //Unlink the InfoWindow from the map
    player.marker.setMap(null); //Unlink the marker from the map
    $(player.listing).toggle(false); //Hide the player listing in the <ul>
    player.removed    =    true; //The player has been removed
}

/**
 *  Will build a URL based on the player's name.
 *
 *  @param    name    string    The name of the player
 *  @return    string
 */
function getAvatarURL(name){
    var out = avatarserver.replace('<playername>',name);
    return out;
}

setInterval(loadPlayers, 1000 * refreshTime);

/**
 * Wait until the document is fully loaded, then add the PlayerList Div on the right, if enabled
 */
$(function() {
    if(showPlayerList) {
        console.log("Adding PlayerList div");
        /* OLD FORMAT
        <div id="player_list" style="position:absolute; top:120px; right:14px; width:150px; height:*;border:solid;border-color:#FFFFFF;border-width:1px;color:#333;font-family:Arial;    background-color: rgba(255,255,255,0.55);">
        <strong>
        <div align="center" style="font-size:80%; position:relative; top:5px;">&nbsp;Online Players&nbsp;</div>
        <hr style="color:#FFFFFF; background:#FFFFFF; heigth:1px;" />
        <div style="font-size:80%; left:10px; bottom:10px; top:5px;" id="Players"></div>
        </strong>
        </div>
        */
        var pmStyleDiv                      = document.createElement("DIV");
        pmStyleDiv.id                       = "player_list";
        pmStyleDiv.style.position           = 'absolute';
        pmStyleDiv.style.top                = '120px';
        pmStyleDiv.style.right              = '14px';
        pmStyleDiv.style.width              = '150px';
        pmStyleDiv.style.border             = 'solid white';
        pmStyleDiv.style.borderwidth        = '1px';
        pmStyleDiv.style.color              = 'white' //'#333333';
        pmStyleDiv.style.fontFamily         = "Arial,Sans-Serif";
        pmStyleDiv.style.backgroundColor    = 'rgba(0,0,0,0.55)';

        var pmStrongDiv = document.createElement("Strong");

        var pmTitleDiv = document.createElement("DIV");
        pmTitleDiv.align            = 'center';
        pmTitleDiv.style.fontsize   = '80%';
        pmTitleDiv.style.position   = 'relative';
        pmTitleDiv.style.top        = '5px';
        pmTitleDiv.innerHTML        = "&nbsp;Online Players&nbsp;";
        pmStrongDiv.appendChild(pmTitleDiv);
        
        var pmHRDiv = document.createElement("HR");
        pmHRDiv.style.color     = '#FFFFFF';
        pmHRDiv.style.height    = '1px';
        pmStrongDiv.appendChild(pmHRDiv);

        var pmPlayerDiv             = document.createElement("DIV");
        pmPlayerDiv.id              = "Spieler";
        pmPlayerDiv.style.fontsize  = '80%';
        pmPlayerDiv.style.left      = '10px';
        pmPlayerDiv.style.bottom    = '10px';
        pmPlayerDiv.style.top       = '5px';
        pmStrongDiv.appendChild(pmPlayerDiv);
        
        pmStyleDiv.appendChild(pmStrongDiv);
        document.body.appendChild(pmStyleDiv);
    }
});
console.log('MCO-ADDON: PlayerMarkers has been loaded');
