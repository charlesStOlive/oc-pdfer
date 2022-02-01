# Installation de PDFER V2
Pdfer v2 travail avec spatie/browsershot et puppeteer
* composer va installer automatiquement spatie/browsershot
* il faut installer manuellement puppeteer

## Installation sur un serveur Linux de forge. 
Npm install ne fonctionnera pas, il faut installer manuellement le package. 
* Vous pouvez rester dans le répertoire de l'application. L'application s'installera dans le bon repertoire sur forge. 
* Il faut être sur ubuntu
```
curl -sL https://deb.nodesource.com/setup_14.x | sudo -E bash -
sudo apt-get install -y nodejs gconf-service libasound2 libatk1.0-0 libc6 libcairo2 libcups2 libdbus-1-3 libexpat1 libfontconfig1 libgbm1 libgcc1 libgconf-2-4 libgdk-pixbuf2.0-0 libglib2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libpangocairo-1.0-0 libstdc++6 libx11-6 libx11-xcb1 libxcb1 libxcomposite1 libxcursor1 libxdamage1 libxext6 libxfixes3 libxi6 libxrandr2 libxrender1 libxss1 libxtst6 ca-certificates fonts-liberation libappindicator1 libnss3 lsb-release xdg-utils wget libgbm-dev libxshmfence-dev
sudo npm install --global --unsafe-perm puppeteer
sudo chmod -R o+rx /usr/lib/node_modules/puppeteer/.local-chromium
```

## installation sur windows. 
L'installation global ne fonctione pas à cause de droits. 
Il faut lancer visual studio ou laragoon en mode exe
```
Il faut utiliser npm install -g puppeteer
```
