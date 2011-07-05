[WIP] PlayerList - Minecraft-Player Positions and List addon for Minecraft-Overviewer
=============

![mcpl](http://i.imgur.com/k9SVF.jpg)

This quick plugin will generate a player specific avatar and show that player's current position on your overviewer map.

Requirements
-------

* [Overviewer 0.1.3+](https://github.com/brownan/Minecraft-Overviewer) -- Map generation in the format of google maps, can be most forks of this project, but brownan's is typically the main project I support
* [JSONAPI] (https://github.com/alecgorge/jsonapi) - json-based interface permitting near calling of pre-defined near arbitary functions via the bukkit API.

Installation
-------

1.) Install the MapMarkers bukkit plugin into the plugins directory for your server. If you have obtained a newer version of this plugin directly from TJ09's website, you only need the MapMarker.jar file from his archive.

2.) Configure MapMarker's `config.yml` to output the markers.json file into your overviewer web directory.
	Alternatively don't edit config.yml, make a symlink (Linux only):
        
    ln -s path/to/minecraft/server/bin/world/markers.json path/to/minecraft/map/markers.json
        
3.) Reference the playermarkers.js in Overviewer by locating in your `index.html` (Default location for this is in the `web_assets` folder in your Overviewer directory). This line will be inserted anywhere before the `</head>` of your index file:

    <script type="text/javascript" src="playermarkers/playermarkers.js"></script>

4.) If you havn't already: Copy the playermarkers folder to your overviewer output folder or the `web_assets` folder of Overviewer.

5.) If you havn't already: Copy the plugins directory to the root of your bukkit enabled server directory.
    
Support
-------

Any inquires can be opened in the issues tab above, or join us on irc.freenode.net in [#overviewer](http://webchat.freenode.net?channels=overviewer)!