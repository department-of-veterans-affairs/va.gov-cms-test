#!/usr/bin/env bash

if [ -z "${1}" ]; then
  echo "Please enter the type of test to run, e.g. accessibility or behavioral."
  exit 1
fi

# Exit immediately if a command fails with a non-zero status code
set -e

# Allow script to be run anywhere in git repo
cd "$(git rev-parse --show-toplevel)"

[ -d node_modules ] || (echo "Please run 'npm install' before running cypress tests" && exit 1)
./node_modules/.bin/cypress install
npm run test:${1} "${@:2}"
