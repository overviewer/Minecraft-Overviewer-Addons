Minecraft ExMaple!
=============

This is just an example map that shows nearly every combination of every block, plus a directional compass and some generated terrain with some typical architecture (the great wall of china). The intent of this map is not to be played, but to test the rendering capabilities of external programs, such as [Overviewer](https://github.com/brownan/Minecraft-Overviewer) 

Download directly from: http://goo.gl/FMDjO
Sample render of the map can be found at: http://overviewer.org/example/

Contents
-------

- 1 minecraft region stored in roughly 12x10 chunks, as generated on an SMP server. (time: morning, weather: clear)
- player.dat for 4 different players in different locations
- Biome information as generated with [Biome Extractor Tool](http://www.minecraftforum.net/viewtopic.php?f=1022&t=80902)
- WorldGuard 5 regions file, with 3 regions of different sizes definned. (These are identified by the stone wireframe cubes in the map, the points defined are the wool blocks on the corners)
- MapMarkers typical markers.json file to indicate current player position for 1 player.
- Minecraft 1.6 SMP Generated Nether files (`exmaple_nether` needs to be moved 1 directory higher to be valid)

Changing the map
-------

If you would like to add or alter this map in any way, please keep the following rules true:

- 12 chunks x 10 chunks total size
- 3 chunks x 3 chunks have been removed in the north west corner
- remove any trees that may grow on the north border as a result of any rendering. Trees block the view of the blocks ... and that's bad!
- player dat files must exist on the map, relocate them into the map region manually if necessary.

Contributers
-------

* pironic - Michael Writhe - michael [at] writhem [dot] com
* Eminence32 - Andrew Chin
* Fenixin - Alejandro Aguilera
* ramiel - Alex Headley 

Version/Updates
-------

version 1.04 - corrected north border, removed some cliff face as not to interfere with dead shrub view.

version 1.03 - added a map_0.dat for 1.6 as well as all 1.6 blocks. Lots of tall grass this time.

version 1.02 - moved buttons, switches, added orientation for diodes.

version 1.01 - Added polygon region

version 1.0 - All blocks added as of minecraft 1.5_02 BETA