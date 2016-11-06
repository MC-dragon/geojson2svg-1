GeoJSON to SVG
==============

A simple PHP library to convert geojson file to svg file.

Rewrite from chrishadi/geojson2svg.

Usage
-----
See `examples/transform.php` 

Parameters
----------
Options:
* <b>canvasWidth</b>
* <b>canvasHeight</b>
* <b>top</b> - defines top offset from the canvas (0,0) point
* <b>left</b> - defines left offset from the canvas (0,0) point
* <b>minXYPixel</b> - minimum width or height of the polygon in pixel for the label to be drawn. 
If a polygon has width or height less than this value, label won't be drawn over it.
* <b>fontSize</b> - defines the font size for the label.
* <b>lineSpacing</b> - defines the line spacing of the label if it spans multiple lines.
