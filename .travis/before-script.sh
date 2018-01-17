#!/usr/bin/env bash
#
# set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#
composer install --no-ansi --no-progress --no-interaction --optimize-autoloader;

if [ "${PHP_SYNTAX}" = '1' ]; then
    exit 0;
elif [ "${PHP_CS}" = '1' ]; then
    exit 0;
elif [ "${PHP_CPD}" = '1' ]; then
    exit 0;
elif [ "${PHP_MD}" = '1' ]; then
    exit 0;
fi

composer require "cakephp/cakephp:${CAKE_VERSION}"  --dev --no-ansi --no-progress --no-interaction;

mkdir -p vendor/cakephp/cakephp/tests/test_app/config;
cat <<'EOF' > vendor/cakephp/cakephp/tests/test_app/config/routes.php
<?php
EOF
