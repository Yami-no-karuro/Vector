# Vector
## A simple HttpFoundation framework for PHP.  

### Installation (Docker)
[Download](https://github.com/Yami-no-karuro/Vector/archive/refs/heads/master.zip) and extract the source code.  
Run `` echo `id -u`:`id -g` `` to retrieve your local user and group id.  
If the echo result is different from `` 1000:1000 `` open the `` Dockerfile `` in the project root and override `` usermod `` and `` groupmod `` values.  
Run `` docker-compose up -d `` to initialize the container.  
Once the container is up and running attach to the php terminal and run `` composer install ``.  
If you run into permissions problems be sure that the `` src/var `` is owned by `` www-data `` (attach to the php terminal and run `` chown -R www-data ./src/var/ ``).  

### Encore Setup
In the project root run `` npm install `` to install node dependencies.  
Run `` npm run watch ``  to start the watcher on the `` assets `` folder.  
By default webpack compiles inside `` public/assets/build `` but you can customize the defaul configuration in `` webpack.config.js `` located in the project root.  
See `` package.json `` to explore encore available scripts.  