# Raspberry-Timelapse
## Result
Click to play on youtube :
[![Raspberry Timelapse](https://i.ytimg.com/vi/mRkYEhcqUxs/sddefault.jpg)](https://www.youtube.com/watch?v=mRkYEhcqUxs)


## Prerequisites
To make this project, you'll need:
* [Raspberry pi 3 model B](https://www.adafruit.com/product/3055) (40€)
* [SD cart](http://boutique.semageek.com/fr/773-micro-sd-16-gb-avec-adaptater-sd-et-os-noobs.html) (12€)
* [Alimentation](http://boutique.semageek.com/fr/723-alimentation-raspberry-pi3-5v-25a-micro-usb.html) (15€)
* [Camera 8MP V2](http://boutique.semageek.com/fr/781-module-camera-8mp-v2-pour-raspberry-pi.html) (35€)
* [Cable Camera 60cm](http://boutique.semageek.com/fr/365-cable-flex-610mm-pour-camera-raspberry-pi.html) (3€)
* [Dissipateur thermique](https://www.adafruit.com/product/3082) (2€)
* [A Server](https://www.ovh.com/fr/vps/vps-ssd.xml) (4€/month) with postfix installed
* A wifi connection
* Patience and passion

Total : 92€

## Raspberry pi installation
### Install Raspbian
https://www.raspberrypi.org/learning/hardware-guide/equipment/

### Install php 7.1
https://www.noobunbox.net/serveur/auto-hebergement/installer-php-7-1-sous-debian-et-ubuntu

Check if it is ok by typing in terminal `php -v`.

### Fix IP
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


### Install VNC server on the Raspberry
To easily access to your Rspberry every time, you should install VNC. You have to install VNC server on your Raspberry and VNC viewer on you desktop. Follow this good tutorial :

https://www.raspberrypi.org/forums/viewtopic.php?t=123457

### Install VNC viewer on your desktop
https://www.realvnc.com/en/connect/download/viewer/

Launch VNC viewer and add a new connection to `192.168.1.201:1`

> Important note : Be sure te be on same wifi network on both side.


### Copy project files
Copy all contained in `/raspberry_side` to your Raspberry `/home/pi/timelapse/`.

### Copy project files
Create empty directory `/home/pi/timelapse/pictures/` and chmod it to 777.

### Customize config.php
Customize constants contained in `config.php`
> TOKEN value in `raspberry_side/config.php` and in `server_side/config.php` must be equal.

### Set cron tab
On your Raspberry, type in terminal `crontab -e` and add line :
```
0 14 * * * /home/pi/timelapse/takepicture.sh 2>&1
0 16 * * * php /home/pi/timelapse/uploadpictures.php 2>&1
```

## Server installation
### Copy project files
Copy all contained in `/server_side` to your server in `/var/www/html/yourdomain/`.

### Customize config.php
Customize constants contained in `config.php`
> TOKEN value in `raspberry_side/config.php` and in `server_side/config.php` must be equal.


### Set cron tab
On your server, type in terminal `crontab -e` and add line, where you will replace `yourdomain`:
```
0 18 * * * php /var/www/html/yourdomain/checktodaypicture.php 2>&1
```
