WG2OvR - WorldGuard 2 Overviewer Regions generation.
=============

![WG2OvR](http://i.imgur.com/DLD8W.jpg)

A simple php exporter of the WorldGuard regions formats into the Overviewer regions format. This script will generate visible isometric cubes on their Overviewer maps of where their WorldGaurd/WorldEdit regions are.

Requirements
-------

* [WorldEdit](http://www.sk89q.com/projects/worldedit/) -- Required by WorldGuard
* [WorldGuard](http://www.sk89q.com/projects/worldguard/) -- Define your regions with WorldGuard.
* [Overviewer](https://github.com/brownan/Minecraft-Overviewer) -- Map generation in the format of google maps, can be most forks of this project, but brownan's is typically the main project I support
* [PHP](http://php.net/) -- Your host/server must be able to parse the php language in order to support this script
* [spyc](http://code.google.com/p/spyc/) -- A Simple PHP YAML Class for reading WorldGuard version 5+ files. Comes included with this script (only required if you are using WG5).

Installation
-------

### WorldGuard version 4-  `regions.txt`

Edit the `regions4.js.php` in a text editor:
    
    $file = file('<serverdir>\plugins\WorldGuard\regions.txt.old'); //regions.txt    
    $color_chestdeny = "#880000"; // what color should the region be when the flag chestaccess-deny is found?    
    $color_normal = "#FFAA00"; // what color should normal regions be colored as?
    
Reference the regions4.js.php in Overviewer by locating in your index.html (Default location for this is in the `web_assets` folder in your Overviewer directory). Replace the first line with the second line below.

    - <script type="text/javascript" src="regions.js"></script>
    + <script type="text/javascript" src="regions4.js.php"></script>
    
Place the personalized `regions4.js.php` into the `web_assets` folder, or your html folder where your overviewer map files are accessed.
    
### WorldGuard version 5+  `regions.yml`

Edit the `regions5.js.php` in a text editor:
    
    $yml = @'<serverdir>\plugins\WorldGuard\worlds\<worldname>\regions.yml';  //regions.yml
    $color_chestdeny = "#880000"; // what color should the region be when the flag chestaccess-deny is found?
    $color_normal = "#FFAA00"; // what color should normal regions be colored as?
    
Reference the regions5.js.php in Overviewer by locating in your index.html (Default location for this is in the `web_assets` folder in your Overviewer directory). Replace the first line with the second line below.

    - <script type="text/javascript" src="regions.js"></script>
    + <script type="text/javascript" src="regions5.js.php"></script>
    
Place the personalized `regions5.js.php` and the `spyc.php` file into the `web_assets` folder, or your html folder where your overviewer map files are accessed.

Support
-------

Any inquires can be opened in the issues tab above, or join us on irc.freenode.net in [#overviewer](http://webchat.freenode.net?channels=overviewer)!