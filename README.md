# Subtitle Finder (cli)

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Tests](https://github.com/onursimsek/lara-subs/workflows/tests/badge.svg)](https://github.com/onursimsek/lara-subs/actions)
[![Quality Score](https://img.shields.io/scrutinizer/g/onursimsek/lara-subs.svg?style=flat-square)](https://scrutinizer-ci.com/g/onursimsek/lara-subs)

You can search and download a subtitle with this cli tool.

## Installation
Download lara-subs file and create config file in same directory. Example config file below.

```dotenv
OPENSUBTITLE_API_KEY=
OPENSUBTITLE_API_NAME=
OPENSUBTITLE_USERNAME=
OPENSUBTITLE_PASSWORD=
```

## How to use?
### Search for Subtitles in a Directory
To search for subtitles for movies or TV series in a specific directory, use the --path parameter.

```shell
lara-subs --path "/Movies/The.Matrix"
lara-subs --path "/Movies/How.I.Met.Your.Mother"
```
![screenshot.02.png](arts%2Fscreenshot.02.png)

### Search for Subtitles by Title
Use the --title parameter to search by movie or series title.

```shell
lara-subs --title "the natrix"
lara-subs --title "how i met your mother"
```
![screenshot.01.png](arts%2Fscreenshot.01.png)

## Requirements
[OpenSubtitles](https://opensubtitles.com) account and an [api consumer](https://www.opensubtitles.com/en/consumers)
