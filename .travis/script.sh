#!/usr/bin/env bash
#
# set -x;
set -e;
set -o pipefail;
#
thisFile="$(readlink -f ${0})";
thisFilePath="$(dirname ${thisFile})";
#
if [ "${PHP_SYNTAX}" = '1' ]; then
    [ $(find . \( -path "./vendor" \) -prune -o -type f \( -name "*.ctp" -o -name "*.php" \) -print | xargs -L1 -i{} php -l {} | grep -vc 'No syntax errors') -eq 0 ];
elif [ "${PHP_CS}" = '1' ]; then
    excludePaths=( \
        'vendor/*' \
    );
    excludePathsJoined=$(printf ",%s" "${excludePaths[@]}");
    excludePathsJoined=${excludePathsJoined:1};

    vendor/bin/phpcs \
        --config-set installed_paths "${PWD}/vendor/cakephp/cakephp-codesniffer,${PWD}/vendor/oefenweb/cakephp-codesniffer" \
    ;
    vendor/bin/phpcs . --standard=CakePHPOefenweb --extensions=ctp,php --ignore="${excludePathsJoined}";
elif [ "${PHP_CPD}" = '1' ]; then
    vendor/bin/phpcpd . --names *.php,*.ctp --exclude vendor --no-interaction --fuzzy .;
elif [ "${PHP_MD}" = '1' ]; then
    excludePaths=( \
        '**/vendor' \
    );
    excludePathsJoined=$(printf ",%s" "${excludePaths[@]}");
    excludePathsJoined=${excludePathsJoined:1};

    vendor/bin/phpmd . text phpmd.xml --suffixes php --exclude "${excludePathsJoined}";
elif [ "${PHP_COVERAGE}" = '1' ]; then
    vendor/bin/phpunit --coverage-clover=clover.xml;
else
    vendor/bin/phpunit;
fi
