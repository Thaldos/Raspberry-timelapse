# Raspberry-timelapse
## Timelapse result

![Raspberry Timelapse](timelapse.gif)

<br>

## Prerequisites
To build this project, you'll need:
* [A Raspberry Pi 3 model B](https://www.adafruit.com/product/3055) (40€)
* [A SD card](http://boutique.semageek.com/fr/773-micro-sd-16-gb-avec-adaptater-sd-et-os-noobs.html) (12€)
* [A Power Supply](http://boutique.semageek.com/fr/723-alimentation-raspberry-pi3-5v-25a-micro-usb.html) (15€)
* [A 8MP Camera V2 for Raspberry](http://boutique.semageek.com/fr/781-module-camera-8mp-v2-pour-raspberry-pi.html) (35€)
* [A 60cm Cable Camera 60](http://boutique.semageek.com/fr/365-cable-flex-610mm-pour-camera-raspberry-pi.html) (3€)
* Patience and passion (prices not yet available)

Total: 105€

<br>

## Raspberry Pi installation
### Installation of Raspbian
Download the NOOBS OS : https://downloads.raspberrypi.org/NOOBS_latest

Extract the archive.

Copy past the files on your SD cart.

Insert the SD cart in your Raspberry Pi and start it.

Follow the installations steps.

### Installation of PHP 7.3
<code>nano /etc/apt/sources.list</code> 

Uncomment the line : 
<code>deb-src http://raspbian.raspberrypi.org/raspbian/ buster main contrib non-free rpi</code> 

<code>apt-get update</code> 

<code>apt-get install -t buster php7.3 php7.3-curl php7.3-gd php7.3-fpm php7.3-cli php7.3-opcache php7.3-mbstring php7.3-xml php7.3-zip</code> 

Test by typing <code>php -v</code> in your terminal. You should have something like :

```
PHP 7.3.4-7 (cli) ( NTS )  
Copyright (c) 1997-2016 The PHP Group  
Zend Engine v3.0.0, Copyright (c) 1998-2016 Zend Technologies  
with Zend OPcache v7.3.6-dev, Copyright (c) 1999-2016, by Zend Technologies
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
static ip_address=192.168.8.203/24 #replace 203 by your wish
static routers=192.168.8.1
static domain_name_servers=192.168.8.1
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

## Project installation
### Copying of the project files on your Raspberry pi
Copy all this project files to your Raspberry in `/home/pi/Raspberry-timelapse/` by typing :
```
cd /home/pi/
git clone https://github.com/Thaldos/Raspberry-timelapse.git
```

### Configuration
Copy `.env.dist` to `.env` and customize the constants in the `.env` file as your wish.

### Download the vendors 
Then type in Raspberry terminal :

```
cd /home/pi/Raspberry-timelapse/ 
composer install
```

Chmod file `/home/pi/Raspberry-timelapse/takepicture.sh` to 777.

Chmod file `/home/pi/Raspberry-timelapse/sendpictures.php` to 777.

Chmod directory `/home/pi/Raspberry-timelapse/pictures/` to 777.

### Set a cron tab
On your Raspberry, in terminal, type `crontab -e` and add that line:
```
0 14 * * * /home/pi/Raspberry-timelapse/takepicture.sh 2>&1
0 0 1 * * php /home/pi/Raspberry-timelapse/sendpictures.php 2>&1
```

### Enjoy!
Your Raspberry pi will take a picture every day, and send to you the pictures every month by e-mail.
