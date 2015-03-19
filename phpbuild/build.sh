#!/bin/bash
# build PHP7 on CentOS7
set -e

yum -y update
yum -y install wget tar make gcc autoconf bison re2c
yum -y install libxml2-devel
yum -y install git
yum clean all

git clone git://github.com/php/php-src
cd php-src
./buildconf
./configure



