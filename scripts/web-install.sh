#!/usr/bin/env bash

# Installs the content-build dependencies.

repo_root="$(git rev-parse --show-toplevel)";
pushd "${repo_root}" > /dev/null;

pushd "./bin";
ln -sf ../docroot/libraries/yarn/bin/yarn ./yarn;
popd;

echo "Node $(node -v)";
echo "NPM $(npm -v)";
echo "Yarn $(yarn -v)";

./scripts/composer/check-yarn-version.sh;

pushd "./web";
yarn run install-repos;
export NODE_EXTRA_CA_CERTS=/etc/pki/tls/certs/ca-bundle.crt;
export PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=TRUE; 
yarn install;
popd;

popd > /dev/null;