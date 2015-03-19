#!/bin/bash
# build PHP7 on CentOS7
set -e

yum -y update
yum -y install wget tar make gcc autoconf bison re2c
yum -y install libxml2-devel
yum clean all


