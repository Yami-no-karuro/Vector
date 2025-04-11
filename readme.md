# Vector

## A simple HttpFoundation framework for PHP

### Requirements

- *nix Operating System, such as [Ubuntu](https://www.ubuntu-it.org/), [Debian](https://www.debian.org/index.it.html) or [MacOS](https://www.apple.com/it/mac/).
- [Docker](https://www.docker.com/) or a [LAMP](https://it.wikipedia.org/wiki/LAMP) stack.
- [NodeJS](https://nodejs.org/en) and [NPM](https://www.npmjs.com/).

### Installation

The project is based on the [LAMP](https://it.wikipedia.org/wiki/LAMP) stack and can be intalled and executed both natively or via [Docker](https://www.docker.com/) containers.  
For the development environment, it is recommended to use [Docker](https://www.docker.com/) to take full advantage of the stack's malleability.  
Follow the steps below to get started.

- Download and extract the source code.
- Run `` sudo (?) docker compose up -d --build `` to initialize the project environment.
- Once the environment is up run `` sudo (?) docker compose exec php-apache composer install `` and `` sudo (?) docker compose exec php-apache bin/console vector:install ``.
- Finally, to compile the public resources run ` npm install && npm run build `.
- To verify that the installation was successful visit `http://localhost:8080`.

### Encore

The project provides a working [Webpack](https://webpack.js.org/) environment via [Encore](https://symfony.com/doc/current/frontend/encore/index.html).  
The configuration supports [TypeScript](https://www.typescriptlang.org/), [SCSS](https://sass-lang.com/) and [React](https://react.dev/).  
See `` package.json `` to explore available scripts.
