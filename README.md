baskit - PHP Build Environment
==============================

![baskit logo](http://baskit.h.isotoma.com/baskit.png)

About
-----
_baskit_ is a zc.buildout/virtualenv/rvm style sandboxed build environment for PHP, based on [PEAR](http://pear.php.net/ "PEAR") and [Phing](http://phing.info/ "Phing").

Using baskit you can create a build environment separate from your system, that uses your system installed PHP, but with it's own dependencies (e.g. specific versions of PEAR installable packages), and run automated build tasks against this environment with Phing.

Installation
------------
The requirements for _baskit_ are:

 * [PHP](http://www.php.net/ "PHP") v5.0+
 * [PEAR](http://pear.php.net/ "PHP Extension and Application Repository") (not PEAR2, but maybe in the future)
 * A Unix shell like bash or zsh (in the future it should be easy to support others, like CMD or PowerShell on Windows)

### Using Git submodules

    git submodule add https://github.com/isotoma/baskit.git baskit
    git commit -m 'Added baskit'

    # You probably want a specific version
    cd baskit
    git checkout 1.0.8
    cd ..
    git add baskit
    git commit -m 'Switched to baskit 1.0.8'

When you come to bootstrap your environment you would reference the `baskit` script as being in `baskit`,
e.g. in your project base you would run `php baskit/baskit`.

### Using Subversion externals

    svn propget svn:externals . > /tmp/baskit_svn_externals
    echo "baskit https://svn.github.com/isotoma/baskit.git" >> /tmp/baskit_svn_externals
    svn propset svn:externals -F /tmp/baskit_svn_externals
    svn ci -m 'Added baskit'
    svn up

**Caveat emptor**: Because this is a checkout from Github's SVN mirroring service, there is no support for pinning to
branches or tags, so using this you'd be stuck with whatever is at `HEAD`.

You *can* pin the external to a specific commit in the Github virtual SVN repo, e.g.:

    baskit -r 100 https://svn.github.com/isotoma/baskit.git

However you'll have to work out which commit to pin to yourself as it would be very difficult to update this document every release with the correct revision reported by svn.github.com.

### Using PEAR
To install using system installed PEAR:

    sudo pear channel-discover baskit.h.isotoma.com
    sudo pear install baskit/baskit

To install locally only, drop `sudo`:

    pear channel-discover baskit.h.isotoma.com
    pear install baskit/baskit

Then you should be able to run from anywhere as a system command:

    baskit

If this doesn't work you may need to run from PHP:

    php baskit

### Using the Debian/Ubuntu package

    curl -C - -O https://github.com/isotoma/baskit/downloads/baskit-1.0.8-1_all.deb
    sudo dpkg -i baskit-1.0.8-1_all.deb

### Using the Fedora/Redhat/CentOS package

    curl -C - -O https://github.com/isotoma/baskit/downloads/baskit-1.0.8.noarch.rpm
    sudo yum install baskit-1.0.0.noarch.rpm

Usage
-----
To create a new environment run the `baskit` script in your project base dir, e.g.
if installed via PEAR, Deb or RPM:

    baskit

Or if you've copied in via Git or SVN:

    php baskit/baskit


Which will give you a `./bin` directory with scripts for PEAR, Phing and PHP:

    bin/pear version
    bin/phing -version
    bin/php -v
    
    # This will install PEAR's Net_URL2 into the local build environment
    bin/pear install Net_URL2

And a `./parts` directory containing PEAR itself:

    ls parts/pear/php/

To make building your project and installing dependencies easier and more maintainable, _baskit_ includes some Phing tasks and targets:

### Phing Tasks

 * **PearInstallTask** - A Phing task to install a depenedency into the local PEAR sandbox, also supports installing custom PEAR channels.
 * **PhpMigrateTask** - A version of the core Phing **DbDeployTask** that allows running migrations written in PHP rather than SQL, you can use this yourself or use the pre-written `migrations.xml` target (see *Phing Targets*).

### Phing Targets

 * **install_requirements.xml** - Installs a list of non-PEAR dependencies from web URLs, with version checking, caching and support for zips and gzipped tars.
 * **migrations.xml** - Target for deploying all undeployed SQL and PHP database migrations (currently missing "undo" target for reverting to previous migration)..
 * **wordpress.xml** - Target for installing a Wordpress blog from scratch, and any Wordpress plugins, with version checking and caching.
 * **apache.xml** - Convenience target for generating an Apache2 virtualhost conf file in `./var`.

For a more information on using the _baskit_ Phing build components take a look at the example project in `./example` (read `./example/README.md` first).

Building baskit yourself
--------------------------
_baskit_ can be installed via a number of different packaging forms, as well as used directly from the PHP/XML source, to generate these packages yourself (such as the PEAR package or RPM) you can use _baskit_ to bootstrap and build itself from a checkout:

    git checkout https://github.com/isotoma/baskit.git
    cd baskit
    ./baskit
    bin/phing
    
    # You can also build a specific package by naming one of the following targets
    # build_pear
    # build_deb
    # build_rpm
    # e.g.
    bin/phing build_rpm

The Debian and RPM targets require some system packages to be present, e.g. on a Debian/Ubuntu system we would install:

    sudo apt-get install dpkg-dev dh-make rpm

