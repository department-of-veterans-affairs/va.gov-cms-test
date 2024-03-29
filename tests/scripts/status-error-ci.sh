#!/usr/bin/env bash

set -exo pipefail

# This runs the status-error test with some additional functionality for CI.
repo_root="$(git rev-parse --show-toplevel)"
pushd "${repo_root}" > /dev/null

: "${GITHUB_COMMENT_TYPE:=unset}"

result="$(drush core-requirements --ignore='update_core,update_contrib,\"update status\"' --severity=2 --format=json | jq '. | length')"
exit_code="${result}"
if [ "${exit_code}" -ne 0 ]; then
  if [ "${GITHUB_COMMENT_TYPE}" == "pr" ]; then
    requirements="$(drush $DRUSH_ALIAS core-requirements --ignore='update_core,update_contrib,\"update status\"' --severity=2 --format=sections)"
    requirements_escaped="$(printf '%q' "${requirements}")"
    comment="$(printf 'va/tests/status-error:<br /><br /><pre>%b</pre>' "${requirements}")"
    github-commenter \
      -delete-comment-regex="va/tests/status-error" \
      -comment="${comment}"
  fi
fi

popd > /dev/null

exit "${exit_code}"
