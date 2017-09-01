# Raspberry-Timelapse
## Result


## Prerequisites
To do this timelpase, you'll need:
* [Raspberry pi 3 model B](https://www.adafruit.com/product/3055) (40€)
* [SD cart](http://boutique.semageek.com/fr/773-micro-sd-16-gb-avec-adaptater-sd-et-os-noobs.html) (12€)
* [Alimentation](http://boutique.semageek.com/fr/723-alimentation-raspberry-pi3-5v-25a-micro-usb.html) (15€)
* [Camera 8MP V2](http://boutique.semageek.com/fr/781-module-camera-8mp-v2-pour-raspberry-pi.html) (35€)
* [Cable Camera 60cm](http://boutique.semageek.com/fr/365-cable-flex-610mm-pour-camera-raspberry-pi.html) (3€)
* [Dissipateur thermique](https://www.adafruit.com/product/3082) (2€)
* A wifi connection
* A google drive account
* Patience and passion

Total : 92€

## Installation
### Install Raspbian on your Raspberry pi
https://www.raspberrypi.org/learning/hardware-guide/equipment/

### Install php 7.1 on your Raspberry pi
https://www.noobunbox.net/serveur/auto-hebergement/installer-php-7-1-sous-debian-et-ubuntu

Check if it is ok by typing in terminal `php -v`. 
### Fix Raspberry pi IP for wifi
Type in terminal :
`sudo nano /etc/network/interfaces` then set this content :
```
# interfaces(5) file used by ifup(8) and ifdown(8)
# Please note that this file is written to be used with dhcpcd
# For static IP, consult /etc/dhcpcd.conf and 'man dhcpcd.conf'
# Include files from /etc/network/interfaces.d:
source-directory /etc/network/interfaces.d
auto lo
iface lo inet loopback
iface eth0 inet manual
allow-hotplug wlan0
iface wlan0 inet manual
wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf
allow-hotplug wlan1
iface wlan1 inet manual
wpa-conf /etc/wpa_supplicant/wpa_supplicant.conf
```

Then `sudo nano /etc/dhcpcd.conf` and put at the end of file this content :
```
# Configuration ip fix wlan :
interface wlan0
static ip_address=192.168.1.201/24 #replace 201 by your wish
static routers=192.168.1.1
static domain_name_servers=192.168.1.1
```
> [More details on this step here](http://limen-arcanum.fr/2016/03/raspberry-3-et-ip-fixe-en-wifi/)


### Check if your ip is well fixed
Reboot then check your local ip : `hostname -I` 

Reboot again and recheck your local ip

If it is not same : have patience and good luck.


### Install VNC server on Rasberry pi
Follow this good tutorials :

https://www.raspberrypi.org/forums/viewtopic.php?t=123457

### Install VNC viewer on Windows 
https://www.realvnc.com/en/connect/download/viewer/

And connect to `192.168.1.201:1`

> Important note : Be sure te be in same wifi network on both side.


### Setup files on Raspberry pi
#### Files
#### Cron
Type in terminal `crontab -e` and add line :
```
0 14 * * * /home/pi/timelapse/takepicture.sh 2>&1
0 16 * * * php /home/pi/timelapse/uploadpictures.php 2>&1
```

### Create Google API account
Go to https://console.developers.google.com/start/api?id=drive
and create a new project.



https://developers.google.com/drive/v3/web/quickstart/php
 https://github.com/google/google-api-php-client/blob/master/README.md

