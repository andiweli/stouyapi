# Building stouyapi
**A static API for the OUYA gaming console that still lets you sign in and install games, despite the OUYA server shutdown in 2019.**

*The whole procedure was done - mainly for personal use - on Raspbian Lite Linux on a Raspberry Pi 3 and 4.<br>
Therefore it now makes this an* **ouyaPI** 🤭

> [!NOTE]
> You can copy/paste the needed commands. Some commands need root privileges; root commands are those beginning with *sudo*.

## 1 - Installing the dependencies

To build the stouyapi API and HTML files on Raspbian you need to install the following packages:

- imagemagick
- exiftool
- qrencode
- ttf-mscorefonts-installer

To run the server, you need to install the following packages:

- apache2
- libapache2-mod-php
- php-sqlite

To install the packages on Raspbian, just use the following command:

```
apt install imagemagick exiftool qrencode ttf-mscorefonts-installer apache2 libapache2-mod-php php-sqlite3
```

> [!IMPORTANT]
> The above listing is not definitive and may vary if you use another distro such as Fedora, CentOS, etc. Make sure you have the package installed on your distribution.


## 2 - Building the API and HTML files

We use the directory ``/srv`` in root as a base directory for our OUYA server files where Apache will point to.

> [!NOTE]
> Using* ``/srv`` *as base directory is just an example. You can put the code anywhere you want; just make sure to use the correct path you use in all configuration files.

In a terminal, create a new directory by typing:
```
sudo mkdir /srv
```

We need permissions on this folder. To get these use:
```
sudo chown <username>:<username> /srv
sudo chmod 755 /srv
```

> [!NOTE]
> ``<username>`` is the user you're logging in to your Pi. For me it is the standard ``pi`` user.

Now first download the stouyapi code and files to your Raspberry Pi.

Go to the new directory. We will store the OUYA data inside this folder:
```
cd /srv
git clone https://github.com/andiweli/stouyapi.git
```

This will create the ``stouyapi`` folder inside our server files directory ``/srv``.

Now enter the stouyapi directory and download the ouya-game-data code and files:
```
cd stouyapi
git clone https://github.com/ouya-saviors/ouya-game-data.git
```

> [!IMPORTANT]
> If you want to add a game/program to your local store, now is the time: Just include the json file of the game/program in the "ouya-game-data/new" folder, before importing the data.

Now, before creating the API files and HTML files, you must rename and - **if you wish** - edit the config.php.dist file.

The config.php file:

- Changes all links pointing to the archive.org site to point to static.ouya.world;
- Configures the list of indicated games that appears on the OUYA home screen (where we have the options PLAY, DISCOVER, etc.);
- Configures a list of suggested games, which appears within DISCOVER, below the list of new games.

Rename the config.php.dist file to config.php:
```
cp config.php.dist config.php
```

If you want to edit it, open it with nano or any text editor of your choice:
```
nano config.php
```

You should only change the following two sections:

The first section is my personal game recommendations within DISCOVER, below the listing named "Favorites":
```
$GLOBALS['packagelists']["Favorites"] = [
	'com.retroarch.ra32',
	'com.realtechvr.nogravity',
	'com.madfingergames.shadowgun',
	'com.Lightstorm3D.GeneEffect',
];
```

If you want:

- Change the title "Favorites", keeping the double quotes,
- Change/include a game by informing the name of the game's JSON file between single quotes and a comma at the end, following the same formatting as above. If you want to Delete a game, just delete the line.

The session below are indications of games that appear on the OUYA home screen at the top. I named that section "Best Rated Games":
```
$GLOBALS['home']['Best Rated Games'] = [
	'com.realtechvr.nogravity',
	'com.madfingergames.shadowgun',
	'com.fde.avpevolution',
	'com.Lightstorm3D.GeneEffect',
	'com.whitewhalegames.godofblades',
	'com.digitalreality.sinemora',
	'com.tastypoisongames.neonshadow',
	'com.retroarch.ra32',
];
```

Edit in the same way, but note that on the home screen the title of the recommendations is enclosed in single quotes.
Do not change any other field in the file and after making changes, save it.

Now generate the API files:
```
./bin/import-game-data.php ouya-game-data/folders
```

Creating the files takes a while. Wait to finish.

<img width="685" height="96" alt="image" src="https://github.com/user-attachments/assets/af0b7e99-2895-43e3-a110-4a815974d3bb" />

When finished, we create the HTML files:
```
./bin/build-html.php
```

## 3 - Setting up the site

So far, apache is already running. If you type in the browser http://localhost the default apache website will appear. Now let's create the settings for the STOUYAPI.

In the terminal, type:
```
cd /etc/apache2/sites-available/
```
Now, copy the apache default site file and rename it however you want but keep the ".conf" extension. I left it with the name of stouyapi:
```
sudo cp 000-default.conf stouyapi.conf
```

The file we copied is a file with minimal apache default settings for virtual hosts.

Now let's edit it with nano:
```
sudo nano stouyapi.conf
```

Now, look for the line that looks like below:
```
#ServerName www.example.com
```

It tells apache the address of the site.<br>
This is usually the hostname of your Raspberry Pi - some name it ```raspberry``` some ```myserver```.<br>
Uncomment it (remove the #) and change the address to your Pi's host name. Here I left it like this:
```
ServerName stouyapi.local
```

Now find a line that looks like below:
```
DocumentRoot /var/www/html
```

That line basically tells apache where the site's files are. For our little server the files in the following path:
```
DocumentRoot /srv/stouyapi/www
```

> [!CAUTION]
> You can use any directory name you want, but remember that the path you enter must be complete until the folder that contains the files and folders on the server. They are all those that are inside the www directory, inside the stouyapi folder where we generate the API files and HTML files.

Now let's go to the end of the file, and before the line below:
```
</VirtualHost>
```

Include the following lines:
```
Script PUT /empty-json.php
Script DELETE /api/v1/queued_downloads_delete.php

<Directory /srv/stouyapi/www>
	AllowOverride All
	Require all granted
</Directory>
```

> [!CAUTION]
> Pay attention that the path in "DocumentRoot" and "<Directory>" should be the same.

In the end, disregarding all the comment lines that the file has, it will look like this:
```
<VirtualHost *:80>

	ServerName stouyapi.local

	ServerAdmin webmaster@localhost
	DocumentRoot /srv/stouyapi/www

	    ErrorLog ${APACHE_LOG_DIR}/error.log
	    CustomLog ${APACHE_LOG_DIR}/access.log combined

	Script PUT /empty-json.php
	Script DELETE /api/v1/queued_downloads_delete.php

	<Directory /srv/stouyapi/www>
		AllowOverride All
		Require all granted
	</Directory>

</VirtualHost>
```

Save the file and close.


## 4 - Activating the apache modules and the website.

With the configuration file created and the site files in place, let's activate the modules and the site.

First set the permissions of the new website:
```
sudo adduser <username> www-data
sudo chown -R www-data:www-data /srv/stouyapi
sudo chmod -R g+rw /srv/stouyapi
```

> [!NOTE]
> ``<username>`` is the user you're logging in to your Pi. For me it is the standard ``pi`` user.

Second the modules, enter the following command:
```
sudo a2enmod actions expires php8.4 rewrite
```

This will activate the necessary modules. Don't worry if any of them are already active (php8.4 will be), as apache just tells you that it's already configured.

> [!NOTE]
> In my case PHP 8.4 was the most recent PHP version available. Check with your installation which version is installed!

At third we restart apache, showing the command to run which is:
```
sudo systemctl restart apache2
```

Finally, to activate the site, type:
```
sudo a2ensite stouyapi
```

> [!NOTE]
> If you used another name for the site configuration file, change the name in the above command. If you just type a2ensite and press enter it will show you all the sites available to activate and you just type the name of the site and press enter.

And now to reload apache we will use the command:
```
sudo systemctl reload apache2
```

With that we finish the settings and the site is already running.

To check if everything is ok, type into the terminal:

*To check if normal API routes work, type...*
```
curl -I http://stouyapi.local/api/firmware_builds
```

*To check if rewritten API routes work, type...*
```
curl -I http://stouyapi.local/api/v1/discover/discover
```

*To check if PHP routes work, type...*
```
curl -I http://stouyapi.local/api/v1/gamers/me
```

All curl commands above should return ``HTTP/1.1 200 OK`` with some other information.


## 5 - Configuring the files in the OUYA

We must access the OUYA through adb, either in the case of an installation after a factory reset or to use the local stouyapi, and edit the hosts file located in /etc (/etc/hosts) and include a line with the format below::
```IP-APACHE-SERVER STOUYAPI-SITE-NAME```

It will look like this (in my case where the stouyapi Server has IP 10.1.0.30):
```
127.0.0.1 localhost
10.1.0.30 stouyapi.local
```

> [!CAUTION]
> The hosts file already has a line that refers to localhost and it should not be deleted. Also, you must leave a blank line after your stouyapi address.

And the ouya_config.properties file, which is in /sdcard, will look like this:
```
OUYA_SERVER_URL=http://stouyapi.local
OUYA_STATUS_SERVER_URL=http://stouyapi.local/api/v1/status
```

> [!CAUTION]
> The site to be used, which in the above case is stouyapi.local, is the one that we inform in the apache configuration file, in the line that starts with "ServerName".

With this, the OUYA will use the local stouyapi immediately.
If it do not, reboot the OUYA once.


## 6 - OUYA setup

1. User registration: "Existing account"
2. Enter any username, leave password empty. Continue.
3. Skip credit card registration

The username will appear on your ouya main screen.
