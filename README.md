GeoJSON to SVG
==============

[![Build Status](https://travis-ci.org/jmleroux/geojson2svg.svg?branch=master)](https://travis-ci.org/jmleroux/geojson2svg)

A simple PHP library to convert geojson file to svg file.

Rewrite from chrishadi/geojson2svg.

Usage
-----
See `examples/transform.php` 

Parameters
----------
Converter options:
* **canvasWidth**
* **canvasHeight**
* **top** - defines top offset from the canvas (0,0) point
* **left** - defines left offset from the canvas (0,0) point

Feature renderer options:
* **strokeWidth**.
* **fillColor**.
* **strokeColor**.

Text renderer options:
* **minXYPixel** - minimum width or height of the polygon in pixel for the label to be drawn. 
If a polygon has width or height less than this value, label won't be drawn over it.
* **fontSize** - defines the font size for the label.
* **lineSpacing** - defines the line spacing of the label if it spans multiple lines.
