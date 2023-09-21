#!/bin/bash

set -o allexport
. /vault/secrets/keyingapp.properties
set +o allexport

apt-get update \
&& apt-get install -y git gettext-base

cat /ap/ecrash/keying/conf/environment_temp.php | envsubst > /ap/ecrash/keying/conf/environment.php
cat /ap/ecrash/keying/conf/secure/secure_temp.php | envsubst > /ap/ecrash/keying/conf/secure/secure.php
cat /ap/ecrash/keying/conf/_temp.htaccess | envsubst > /ap/ecrash/keying/conf/.htaccess

rm /ap/ecrash/keying/conf/environment_temp.php
rm /ap/ecrash/keying/conf/secure/secure_temp.php
rm /ap/ecrash/keying/conf/_temp.htaccess

apt-get remove -y gettext-base

set -e
if [ -z "$CI" ]; then
    # if not in CI pipeline
    if [ $# -gt 0 ]
    then
        "$@"
    fi
    exec docker-php-entrypoint apache2-foreground
else
    exec docker-php-entrypoint "$@"
fi