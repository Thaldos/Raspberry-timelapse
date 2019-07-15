# Raspberry-timelapse
## Result

[![Raspberry Timelapse](http://masterofolympus.com/timelapse.gif)](http://masterofolympus.com/timelapse.gif)

## My hardware installation

[![Raspberry Timelapse installation 2](https://i.imgur.com/9k6PtO4.jpg)](https://i.imgur.com/9k6PtO4.jpg)
[![Raspberry Timelapse installation 3](https://i.imgur.com/ypKQSn0.jpg)](https://i.imgur.com/ypKQSn0.jpg)
[![Raspberry Timelapse installation 4](https://i.imgur.com/hfO7SQW.jpg)](https://i.imgur.com/hfO7SQW.jpg)
[![Raspberry Timelapse installation 5](https://i.imgur.com/iuf27CX.jpg)](https://i.imgur.com/iuf27CX.jpg)

## Prerequisites
To build this project, you'll need:
* [A Raspberry Pi 3 model B](https://www.adafruit.com/product/3055) (40€)
* [A SD card](http://boutique.semageek.com/fr/773-micro-sd-16-gb-avec-adaptater-sd-et-os-noobs.html) (12€)
* [A Power Supply](http://boutique.semageek.com/fr/723-alimentation-raspberry-pi3-5v-25a-micro-usb.html) (15€)
* [A 8MP Camera V2 for Raspberry](http://boutique.semageek.com/fr/781-module-camera-8mp-v2-pour-raspberry-pi.html) (35€)
* [A 60cm Cable Camera 60](http://boutique.semageek.com/fr/365-cable-flex-610mm-pour-camera-raspberry-pi.html) (3€)
* Patience and passion (prices not yet available)

Total: 105€
##
## Raspberry Pi installation
### Install Raspbian
https://www.raspberrypi.org/documentation/installation/installing-images/

### Installation of PHP 7
<code>nano /etc/apt/sources.list</code> 

Uncomment the line : 
<code>deb-src http://raspbian.raspberrypi.org/raspbian/ stretch main contrib non-free rpi</code> 

<code>apt-get update</code> 

<code>apt-get install -t stretch php7.0 php7.0-curl php7.0-gd php7.0-fpm php7.0-cli php7.0-opcache php7.0-mbstring php7.0-xml php7.0-zip</code> 

Test by typing <code>php -v</code> in your terminal. You should have something like :

```
PHP 7.0.4-7 (cli) ( NTS )  
Copyright (c) 1997-2016 The PHP Group  
Zend Engine v3.0.0, Copyright (c) 1998-2016 Zend Technologies  
with Zend OPcache v7.0.6-dev, Copyright (c) 1999-2016, by Zend Technologies
```


### Connect and enable your camera
https://www.raspberrypi.org/documentation/configuration/camera.md

### Wi-Fi autoconnecting
Type in your terminal :
<code>sudo nano /etc/wpa_supplicant/wpa_supplicant.conf</code>
 
 And append some thing like :
 
```
network={  
    ssid="Livebox-12345"  
    psk="123456789AZERTY"  
}
```

### Fixed IP
Type in Raspberry terminal :
`sudo nano /etc/network/interfaces` then set the contents to this values at least:
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

Then `sudo nano /etc/dhcpcd.conf` and append this to the file:
```
# Configuration ip fix wlan :
interface wlan0
static ip_address=192.168.1.201/24 #replace 201 by your wish
static routers=192.168.1.1
static domain_name_servers=192.168.1.1
```
> [More details on this step here](http://limen-arcanum.fr/2016/03/raspberry-3-et-ip-fixe-en-wifi/) (French link)


### Check if your IP address is set well:
Reboot then check your local IP : `hostname -I`

Reboot again and re-check your local IP

If it is not same: have patience and good luck. :)



### Installation of VNC server on the Raspberry
To easily access to your Raspberry every time, you should use VNC. You have to enable VNC server on your Raspberry and install VNC viewer on you desktop.

Menu > Preference > Raspberry configuration > Interfaces > Enable VNC


[![Raspberry VNC](https://image.ibb.co/cMPMny/raspberry_vnc.jpg)](https://image.ibb.co/cMPMny/raspberry_vnc.jpg)

### Install VNC viewer on your desktop
https://www.realvnc.com/en/connect/download/viewer/

Launch VNC viewer and add a new connection to `192.168.1.201`, with the user `pi` and password `raspberry`.

> Important note: be sure te be on same Wi-Fi network on both sides.

### Install SMTP
Follow this good tutorial:

https://hotfirenet.com/blog/1704-envoyer-mail-depuis-le-raspberry-pi/ (French link)


If, like me, you use gmail, this is a good configuration :
```
hostname=anexistingwebdomain.com
root=monLogin@gmail.com
mailhub=smtp.gmail.com:587
AuthUser=monLogin@gmail.com
AuthPass=monbeauPaSsWoRd
FromLineOverride=YES
UseSTARTTLS=YES
```

<br>

### Copy project files
Copy all project files to your Raspberry in `/home/pi/timelapse/`.

Chmod file `/home/pi/timelapse/takepicture.sh` to 777.

Chmod file `/home/pi/timelapse/sendpictures.php` to 777.

Chmod directory `/home/pi/timelapse/pictures/` to 777.

### Customize config.php
Customize the constants in `config.php`

### Set a cron tab
On your Raspberry, in terminal, type `crontab -e` and add that line:
```
0 14 * * * /home/pi/timelapse/takepicture.sh 2>&1
0 0 1 * * php /home/pi/timelapse/sendpictures.php 2>&1
```

### Enjoy!
Your Raspberry pi will take a picture every day, and send you pictures every month by e-mail.
