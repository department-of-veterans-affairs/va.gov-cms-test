# Drush

In local development environments, these commands should normally be run with a `composer` or `ddev` prefix, e.g. `composer drush sqlq show tables"`.  For other usage notes regarding local development environments, see [Local](./local.md).

## Custom Drush Commands

### Site Status

See [SiteStatusCommands.php](../docroot/modules/custom/va_gov_build_trigger/src/Commands/SiteStatusCommands.php).

- `va-gov:disable-deploy-mode` -- Sets the Deploy Mode flag to FALSE. It is not normally necessary to perform this operation manually.
- `va-gov:enable-deploy-mode` -- Sets the Deploy Mode flag to TRUE. It is not normally necessary to perform this operation manually.
- `va-gov:get-deploy-mode` -- Indicates whether the CMS is currently in Deploy Mode, which is a precautionary measure used to prevent content changes while content is being deployed.

### Content Release

There is a gradual migration taking place from the existing system to a refactored version intended to decouple behavior from environment, accomodate multiple frontends, etc.

#### Current

The current system's commands mostly relate to continuous builds and management of the content release state machine:

See [ContentReleaseCommands.php](../docroot/modules/custom/va_gov_build_trigger/src/Commands/ContentReleaseCommands.php).

- `va-gov:content-release:advance-state` -- Advance the state like an external system would do through HTTP.
- `va-gov:content-release:check-scheduled` -- Make sure builds are going out at least hourly during business hours.
- `va-gov:content-release:check-stale` -- If the state is stale, reset the state.
- `va-gov:content-release:get-frontend-version` -- Get the frontend version that was requested by the user.
- `va-gov:content-release:get-state` -- Get the current release state.
- `va-gov:content-release:is-continuous-release-enabled` -- Check continuous release state.
- `va-gov:content-release:reset-frontend-version` -- Reset the content release frontend version.
- `va-gov:content-release:reset-state` -- Reset the content release state.
- `va-gov:content-release:toggle-continuous` -- Toggle continuous release.

#### New

Housed in the new system:

See [RequestCommands.php](../docroot/modules/custom/va_gov_content_release/src/Commands/RequestCommands.php).

- `va-gov-content-release:request:submit` -- Request a frontend build (but do not initiate it).

See [FrontendVersionCommands.php](../docroot/modules/custom/va_gov_content_release/src/Commands/FrontendVersionCommands.php).

- `va-gov-content-release:frontend-version:get` -- Get the currently selected version of the selected frontend (defaults to `content_build`).
- `va-gov-content-release:frontend-version:reset` -- Reset (to default, or `main`) the currently selected version of the selected frontend (defaults to `content_build`).
- `va-gov-content-release:frontend-version:set` -- Set the currently selected version of the selected frontend (defaults to `content_build`).

### Metrics

See [MetricsCommands.php](../docroot/modules/custom/va_gov_backend/src/Commands/MetricsCommands.php).

- `va-gov:metrics:send` -- Send various application metrics to DataDog.

### Outdated Content

See
[CMS Notification System](https://github.com/department-of-veterans-affairs/va.gov-cms/tree/main/docroot/modules/custom/va_gov_notifications/README.md) for commands to test or
execute the various outdated content sends.
