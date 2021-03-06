# ![image](https://raw.github.com/tmaiaroto/blackprint/master/webroot/img/blackprint-logo-square-64.png) Blackprint CMS

#### Features

 * Built on top of, Lithium, a rapid application development framework for PHP for organization, maintenance, testability, and scale.
 * Takes advantage of Bower for installing themes and managing front-end assets for those themes (which can override ALL templates, including the admin back-end).
 * Uses MongoDB for its schema flexibility, high performance and scalability.
 * Comes with a bunch of helpers and utilities for increased development speed.
 * Uses Twitter Bootstrap and Font Awesome right out of the box for increased development speed.
 * Strong password encryption methods thanks to the Lithium framework.
 * Support for 9 third party OAuth services completely integrated with the system's user authentication for flexibility and social awareness.
 * Utilizes Composer for PHP library management and support. You can quickly leverage the power of practically any PHP library! Even if it's not PSR-0 compliant, using Lithium's autoloader, you can "transform" class names to load in non-compliant or older libraries.

**Quickstart** ```bash <(curl -s https://raw.github.com/tmaiaroto/blackprint/master/clone.sh) && bash install.sh```

Note: If you want to then push this to your own repository, I would highly recommend renaming the "origin" remote to "blackprint" or something easy to remember. Then add a new "origin" remote for your repository. This will make it easy to recieve any updates from this repository, fork the configuration, and anything else you may need. I would also suggest trying to keep your code in a library for portability and organization.

#### Requirements

 * PHP 5.3+ (Blackprint uses the [Lithium PHP Framework](http://lithify.me))
 * [MongoDB](http://www.mongodb.org/)
 * A web server of course (Nginx preferred)
 * [Composer](http://getcomposer.org/) (this will be downloaded for you if you don't have it when running ```install.sh```)
 * [Bower](http://bower.io/) (this means Node.js and NPM)
 * Git

Note: Your target production server need not have Bower or Composer if you force add the libraries and front-end assets under 
```bower_components``` to your own repository. Understand by doing that your repository will need to update each dependency so 
that your production server can get any new versions because at that point, using Bower or Composer may create conflicts for 
your cloned Git repository on your production server. You may wish to branch that out or something. I leave deployment up to you.

**Recommended**    
It is also recommended that you use utilize an opcode cache solution such as APC, though it is not necessarily required (you'd
just get some crazy looks). 

#### Installation
You could clone this repository and then run ```install.sh``` to get going...Or, if you're on Linux or OS X, you could simply create 
a new directory for your site, go to the root of it (ensure it's empty) and run    
```bash <(curl -s https://raw.github.com/tmaiaroto/blackprint/master/clone.sh) && bash install.sh```

Alternatively, you could clone the repository, setup Composer yourself and then run ```composer install``` 
(or run composer however you have it setup). Then you'd need to ```chmod 777``` the ```resources``` directory recursively. 
Then you'd need to setup symlinks in ```webroot``` for the ```li3b_core``` library.

Note that everything in Blackprint works as a library and so everything you add to it will be located under the ```libraries``` 
directory and if you have assets (CSS, JavaScript, images, etc.) you wish to expose to the webroot, you will need to symlink 
them as you go along. The ```libraries``` directory is also under ```.gitignore``` just so you know. I'd suggest submodules or using 
Composer packages.

*Note to Windows users:* I'm so sorry for you. Things will work for you I'm sure, but I rely on symlinks so you're going to have to figure out 
how to get around that on your own. See the ```config/media.php``` file and ensure it's included in the ```config/bootstrap.php``` file. 
You're essentially going to be serving assets through PHP instead of directly from the webroot via your web server. Unless you can 
get symlinks of some sort working, then great.
