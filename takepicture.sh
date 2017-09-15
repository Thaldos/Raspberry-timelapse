#!/bin/sh

today=$(date +"%Y-%m-%d")
raspistill -o /home/pi/timelapse/pictures/$today.jpg