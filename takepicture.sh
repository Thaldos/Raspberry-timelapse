#!/bin/sh

today=$(date +"%Y-%m-%d")
raspistill -q 8 -o /home/pi/timelapse/pictures/$today.jpg