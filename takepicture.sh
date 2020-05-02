#!/bin/sh

today=$(date +"%Y-%m-%d")
raspistill -q 8 -o /home/pi/Raspberry-timelapse/pictures/$today.jpg