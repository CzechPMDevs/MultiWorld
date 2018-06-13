<h1 align="center"> MultiWorld </h1>

<p align="center"> MultiWorld Ported to PocketMine </p>
<br>

<p align="center">
	<a href="https://poggit.pmmp.io/p/MultiWorld">
		<img src="https://poggit.pmmp.io/shield.state/MultiWorld">
	</a>
	<a href="https://poggit.pmmp.io/p/MultiWorld">
		<img src="https://poggit.pmmp.io/shield.api/MultiWorld">
	</a> 
	<a href="https://poggit.pmmp.io/p/MultiWorld">
		<img src="https://poggit.pmmp.io/shield.dl.total/MultiWorld">
	</a> 
	<a href="https://poggit.pmmp.io/p/MultiWorld">
		<img src="https://poggit.pmmp.io/shield.dl/MultiWorld">
	</a> 
	<a href="https://poggit.pmmp.io/p/MultiWorld">
		<img src="https://poggit.pmmp.io/ci.shield/CzechPMDevs/MultiWorld/MultiWorld">
	</a>
</p>

<p align="center">World Management commands ✔️</p>
<p align="center">MultiLanguage support ✔️</p>
<p align="center">More generators ✔️</p>
<p align="center">Latest PocketMine-MP API Support ✔️</p>
<br>


### Latest Version:
- 1.4.1
	- Updated to API 3.0.0-ALPHA12
	- Fixed throwing players from server while removing default level
	- Added Japanese Language (by @fuyutsuki)

### Releases:

- **Stable Builds:**

| Version | Download (PHAR) | Download (ZIP) |
| --- | --- | --- |
| 1.4.3 | Under development | Under development |
| 1.4.2 | [Poggit Releases](https://poggit.pmmp.io/r/28962/MultiWorld.phar) | [GitHub](https://github.com/CzechPMDevs/MultiWorld/archive/1.4.2.zip) |
| 1.4.1 | [Poggit-CI Downloads](https://poggit.pmmp.io/r/27881/MultiWorld_dev-99.phar) | [GitHub](https://github.com/CzechPMDevs/MultiWorld/archive/1.4.1.zip) |
| 1.4.0 | [Poggit Releases](https://poggit.pmmp.io/r/25218/MultiWorld.phar) | [GitHub](https://github.com/CzechPMDevs/MultiWorld/archive/be4083eae06adc249e3d4a8968ea0992d42f929c.zip) |
| 1.3.2 | [Poggit-CI Downloads](https://poggit.pmmp.io/r/11536/MultiWorld_dev-86.phar) | [GitHub](https://github.com/CzechPMDevs/MultiWorld/archive/5237db27b69fbf9a9aac66075fd81e9e804f180c.zip) |
| 1.3.1 | [GitHub](https://github.com/CzechPMDevs/MultiWorld/releases/download/1.3.1/MultiWorld.phar) | [GitHub](https://github.com/CzechPMDevs/MultiWorld/archive/1.3.1.zip) |
| 1.3.0 | [Poggit-CI Downloads](https://poggit.pmmp.io/r/10889/MultiWorld.phar) | [GitHub](https://github.com/CzechPMDevs/MultiWorld/archive/1.3.0.zip) |

<br>

- **Other released versions [here](https://github.com/CzechPMDevs/MultiWorld/releases)**
- **Latest released version on Poggit [here](https://poggit.pmmp.io/p/MultiWorld/)**
-  **Latest developement build on Poggit [here](https://poggit.pmmp.io/ci/CzechPMDevs/MultiWorld/MultiWorld)**

### Commands:

**All commands in MultiWorld:**

- Create Command:
	- usage: /mw create <level> [seed] [generator]
	- permission: mw.cmd.create
	- aliases: add, new, generate
	- generators: Normal, Flat, Nether

- Teleport Command:
	- usage: /mw teleport <level> [player]
	- permission: mw.cmd.teleport
	- aliases: tp

- List Command:
	- usage: /mw list
	- permission: mw.cmd.list
	- aliases: ls

- Help Command:
	- usage: /mw help
	- permission: mw.cmd.help
	- aliases: ?

- Delete Command:
	- usage: /mw delete <level>
	- permission: mw.cmd.delete
	- aliases: rm, del, remove

- Load Command:
	- usage: /mw load <level>
	- permission: mw.cmd.load
	- aliases: ld

- Unload Command:
	- usage: /mw unload <level>
	- permission: mw.cmd.unload
	- aliases: unld

- Update Command:
	- usage: /mw update <spawn|lobby|defaultlevel> [options]
	- permissions: mw.cmd.updete
	- aliases: upte, ue

- Info Command:
	- usage: /mw info
	- permissions: mw.cmd.info
	- aliases: i


### Permissions:

- Permission to all command: **mw. cmd**

- **Other** permissions:
	- mw.cmd.help:
		- Allows player to use /mw help
		- Default: op
	- mw.cmd.create:
		- Allows player to use /mw create
		- Default: op
	- mw.cmd.teleport:
		- Allows player to use /mw teleport
		- Default: op
	- mw.cmd.list:
		- Allows player to use /mw list
		- Default: op
	- mw.cmd.load:
		- Allows player to use /mw load
		- Default: op
	- mw.cmd.unload:
		- Allows player to use /mw unload
		- Default: op
	- mw.cmd.delete:
		- Allows player to use /mw delete
		- Default: op
	- mw.cmd.info:
		- Allows player to use /mw info
		- Default: op
	- mw.cmd.update:
		- Allows player to use /mw update
		- Default: op

### API:

- **Api will be added in version 1.4.4**
