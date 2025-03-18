# Vector

## A simple HttpFoundation framework for PHP.  

### Installation

The project is based on the [LAMP](https://it.wikipedia.org/wiki/LAMP) stack and can be intalled and executed both natively or via [Docker](https://www.docker.com/) containers.  
To install and run the project on a native environment follow the steps below:  

- Download and extract the source code.  
- Run `` composer install ``.  
- Run `` bin/console vector:install ``.  
- Enjoy!

To install and run the project via [Docker](https://www.docker.com/) containers follow the steps below:  

- Download and extract the source code.  
- Run `` docker compose up -d --build `` to initialize the project environment.  
- Once the project is up run `` docker compose exec php-apache composer install `` and `` docker compose exec php-apache bin/console vector:install ``.  
- Enjoy!

### Encore

The project provides a working [Webpack](https://webpack.js.org/) environment via [Encore](https://symfony.com/doc/current/frontend/encore/index.html).  
The configuration supports [TypeScript](https://www.typescriptlang.org/), [SCSS](https://sass-lang.com/) and [React](https://react.dev/).  
To complete the setup follow the steps below:  

- Run `` npm install `` to install node dependencies.  
- Run `` npm run watch ``  to start the watcher on the `` assets `` folder.  
- By default webpack compiles inside `` public/assets/build `` but you can customize the defaul configuration in `` webpack.config.js `` located in the project root.  
- See `` package.json `` to explore available scripts.

### React
The [Webpack](https://webpack.js.org/) compiler is configured to handle [React](https://react.dev/) applications.  

