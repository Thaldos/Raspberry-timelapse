#!/bin/sh

today=$(date +"%Y-%m-%d")
raspistill -o /home/pi/pictures/$today.jpg