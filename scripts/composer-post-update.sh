#!/usr/bin/env bash

# Composer post-update-cmd hook
# See also:
# - ./composer-post-install.sh
# - ./composer-pre-update.sh
# - ./composer-pre-install.sh
repo_root="$(git rev-parse --show-toplevel)";
pushd "${repo_root}" > /dev/null;

cp -r ./hooks/git/* .git/hooks/;
chmod 0777 ./.git/hooks/pre-commit;

rm -r ./vendor;

composer va:remove-git-dirs;

popd > /dev/null;