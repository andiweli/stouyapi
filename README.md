# stouyapi - Static OUYA API

A static API for the OUYA gaming console that still lets you sign in and install games, despite the OUYA server shutdown in 2019.

> [!NOTE]
> This is a modified repository - *mainly for personal use* - from [cweiske's stouyapi](https://github.com/cweiske/stouyapi) specifically for Raspberry Pi and Raspbian Lite OS. So I can use my own small tiny Raspberry Pi at home, modify and add my own library of [Playjam's GameStick](https://en.wikipedia.org/wiki/GameStick) games.
> That's what I am currently working on: Implementing [some selected](https://github.com/andiweli/gamestick-assets) Playjam GameStick games into the OUYA API store.

<img width="2048" height="769" alt="image" src="https://github.com/user-attachments/assets/9cc41859-976f-475f-95e3-14ec3e349d1f" />


## Setup

> [!NOTE]
> Step-by-step setup instructions specific for Raspberry Pi can be found in the [HOWTO-SETUP](https://github.com/andiweli/stouyapi/blob/master/HOWTO-SETUP.md).


## Creating new JSON game files

For a convenient editing I've added a browser-based JSON editor. It can load and save the neccessary data for a game file on the OUYA.
Game files in JSON format generated with this editor can be used when compiling the www-data in the [HOWTO (step 3 Building API and HTML files)](https://github.com/andiweli/stouyapi/blob/master/HOWTO-SETUP.md#3---building-the-api-and-html-files).

<img width="1889" height="892" alt="image" src="https://github.com/user-attachments/assets/bf7e8c01-04bc-46b1-93a1-aba4fdb4b70b" />


## Push to my OUYA

stouyapi's HTML game detail page have a "Push to my OUYA" button that allows anyone to tell his own OUYA to install that game.
It works without any user accounts, and is only based on IP addresses.

If your PC that you click the Push button on and your OUYA have the same public IP address (IPv4 NAT), or the same IPv6 64bit prefix, then the OUYA will install the game within 5 minutes.

It will also work if you run stouyapi inside your local network, because all private IP addresses are mapped to a special "local" address.

You can inspect your own download queue by simply opening ``/api/v1/queued_downloads`` in your browser.


## See also

- https://gitlab.com/devirich/BrewyaOnOuya - alternative storefront
- https://archive.org/details/ouyalibrary - Archived OUYA games
- https://github.com/ouya-saviors/ouya-game-data/ - OUYA game data repository

## Discoveries

- data/data/tv.ouya/cache/ion/

  - image cache for main menu image

- Don't put a trailing slash into ``OUYA_SERVER_URL`` - it will make double slashes
