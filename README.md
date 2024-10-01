# Prerequisites

- Laragon (v6.0) <- local development environment for this project
- Composer (v2.5.8) <- a dependency manager for PHP. It helps you to manage the libraries and frameworks that your PHP projects depend on
- PHP v8.2.2 <- as of making this project
- MongoDB v8 <- NoSQL Database used as of making this project


- ## Laragon
1. Install laragon v6.0 , you may download it in their website: https://laragon.org/download/index.html. 
2. Laragon comes with a "bin" folder containing essential and vital tools needed for software development. Check your server and user environment variables in your computer
3. Compare if those tools that are in the "bin" folder are included in "Path".

![Environmental Variables](https://i.imgur.com/iXtzx63.png)
![Laragon Bin Directory](https://i.imgur.com/E31hkdm.png)


## Composer Version 
Download and Install Composer v2.5.8 in this website https://getcomposer.org/download/. You can type "composer -v" in the bash terminal to check its version.

![ComposerVersionImage](https://i.imgur.com/Fa1z9SD.png)

## PHP Version
1. Go to this website https://windows.php.net/downloads/releases/archives/ and download the php v8.2.2 
2. Place it inside the php folder in your laragon "bin" directory, extract it there, and add it to your device's 'Path'. If you have a bash open, close it. 
3. Run Laragon and click Menu located at the upper left corner of the window
4. Hover your mouse to PHP and it'll open a side dropdown menu
5. Hover your mouse to "Version" and select 8.2.2 version. 
6. Repeat 3 and 4, then click php.ini. A notepad++ window will open
7. In the text document search for "memory_limit" and set that to -1.
8. Save the file and close the window.

![PhpVersionImage](https://i.imgur.com/cqPfaaV.png)

## MongoDB Version and PyMongo
1. I'm using Windows, so what I did was install MongoDB v8.0 in my system first and followed the installation wizard.
