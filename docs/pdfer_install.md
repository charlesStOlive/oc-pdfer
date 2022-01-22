# Installation de PDFER
L'installation de pdfer peut être compliqué. 
L'application exploite wkhtmltopdf et à besoin pour cela d'avoir sur le serveur une version de wktrml. 

## Installation sur un serveur Linux. 
Etape issue de la page : https://websiteforstudents.com/how-to-install-wkhtmltopdf-wkhtmltoimage-on-ubuntu-18-04-16-04/
1: Verifiez la dernière version disponible ( ici v 2018 )
https://wkhtmltopdf.org/downloads.html
2. Munissez vous de votre code sudo
3. Mettez vous à la racine du serveur ( à coupt de cd ..)
4. Entrez les commandes suivantes:
```
sudo apt update 
sudo apt install wget xfonts-75dpi
```
Ensuite :
```
cd /tmp
wget https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.bionic_amd64.deb
sudo dpkg -i wkhtmltox_0.12.5-1.bionic_amd64.deb
```
Si il y a un problème, utilisez la commande suivante : 
```
sudo apt -f install
```
Verifiez si tout à fonctionné en entrant la commande suivante :
```
wkhtmltopdf --version
```
5. Votre installation est terminé, il faut maintenant ajouter dans votre fichier .env le repertoire ou à été installé wkhtmltopdf 
```
WKHTML_PDF_BINARY=/usr/local/bin/wkhtmltopdf
WKHTML_IMG_BINARY=/usr/local/bin/wkhtmltoimage
```
## installation sur windows. 
