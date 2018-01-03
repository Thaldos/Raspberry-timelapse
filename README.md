# Raspberry-timelapse
## Video result
Click to play on youtube :

[![Raspberry Timelapse](https://i.ytimg.com/vi/mRkYEhcqUxs/sddefault.jpg)](https://www.youtube.com/watch?v=mRkYEhcqUxs)

## My hardware installation

[![Raspberry Timelapse installation 1](https://img11.hostingpics.net/pics/990605raspberrypitimelapse.jpg)](https://www.hostingpics.net/viewer.php?id=990605raspberrypitimelapse.jpg)
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
* A Wi-Fi connection
* Patience and passion

Total: 105€
##
## Raspberry Pi installation
### Install Raspbian
https://www.raspberrypi.org/documentation/installation/installing-images/

### Install PHP 7.1
https://www.noobunbox.net/serveur/auto-hebergement/installer-php-7-1-sous-debian-et-ubuntu (French link)

Check success by typing in terminal `php -v`. This should tell you the current PHP version.

### Connect and enable your camera
https://www.raspberrypi.org/documentation/configuration/camera.md

### Wi-Fi autoconnecting

`sudo editor /etc/wpa_supplicant/wpa_supplicant.conf`
Append as many networks as you want - here are a few examples:

```
# Secure Wi-Fi example:
network={
    ssid="{your-ssid}"
    psk="{your-key}"
}

# Open Wi-Fi example:
network={
    ssid="muenchen.freifunk.net"
    key_mgmt=NONE
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


### Install VNC server on the Raspberry
To easily access to your Raspberry every time, you should install VNC. You have to install VNC server on your Raspberry and VNC viewer on you desktop. Follow this good tutorial:

https://www.raspberrypi.org/forums/viewtopic.php?t=123457

### Install VNC viewer on your desktop
https://www.realvnc.com/en/connect/download/viewer/

Launch VNC viewer and add a new connection to `192.168.1.201:1`

> Important note: be sure te be on same Wi-Fi network on both sides.

### Install SMTP
Follow this good tutorial:

https://hotfirenet.com/blog/1704-envoyer-mail-depuis-le-raspberry-pi/ (French link)


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
