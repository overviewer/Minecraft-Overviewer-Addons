#!/usr/bin/env python2

"""
Removes unnecessary chunks from the ExMaple.
"""

import os
import pymclevel.regionfile as regionfile

REGIONS = {
        # regionfile : (minx, minz, maxx, maxz)
        "r.-1.0.mca" : (11, 1, 22, 10),
}

if __name__ == "__main__":
    path = os.path.join(os.path.dirname(__file__), "region")
    for filename in os.listdir(path):
        regionpath = os.path.join(path, filename)
        if filename not in REGIONS:
            print "Removing region %s ..." % filename
            os.remove(regionpath)
            continue
        
        print "Cropping region %s ..." % filename,
        cropped = 0
        region = regionfile.MCRegionFile(regionpath, (42, 42))
        region_new = regionfile.MCRegionFile(regionpath + ".tmp", (42, 42))
        minx, minz, maxx, maxz = REGIONS[filename]
        for chunkx in xrange(32):
            for chunkz in xrange(32):
                if not region.containsChunk(chunkx, chunkz):
                    continue
                if not (chunkx < minx or chunkx > maxx or chunkz < minz or chunkz > maxz):
                    region_new.copyChunkFrom(region, chunkx, chunkz)
                else:
                    cropped += 1
        print "removed %d chunks" % cropped
        
        region.close()
        region_new.close()
        os.remove(regionpath) 
        os.rename(regionpath + ".tmp", regionpath) 
