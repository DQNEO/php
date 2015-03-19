#!/bin/bash
# build PHP7 on CentOS7
set -e

yum -y update

# to fetch and extract source
yum -y install wget tar

# for compilation
yum -y install make gcc autoconf bison re2c

# for extensions
yum -y install libxml2-devel libcurl-devel libpng-devel

yum clean all

yum -y install git
git clone git://github.com/php/php-src
cd php-src
./buildconf
./myconfigure

