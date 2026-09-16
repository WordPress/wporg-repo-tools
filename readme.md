This is a repository of common configurations and scripts used in projects for WordPress.org Meta.

## Installation

Before installing, please sync any useful changes that have recently been made to projects that depend on this repo. Otherwise it partially defeats the point of having a central repo template.

Include this in a project via Composer with something like this in your composer.json file:

```json
{
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/WordPress/wporg-repo-tools"
		}
	],
	"require-dev": {
		"wporg/wporg-repo-tools": "dev-trunk"
	},
	"minimum-stability": "alpha",
	"prefer-stable": true,
	"config": {
		"allow-plugins": {
			"dealerdirect/phpcodesniffer-composer-installer": true
		}
	}
}
```

### Coding standards

This package requires the standards that `configs/phpcs.xml.dist` refers to, so projects
get them transitively and should not list them separately:

- `squizlabs/php_codesniffer`, which provides the `phpcs` and `phpcbf` binaries
- `wp-coding-standards/wpcs`
- `phpcompatibility/phpcompatibility-wp`
- `dealerdirect/phpcodesniffer-composer-installer`, which registers the standards' paths with PHPCS

Two of the settings above are required, and neither can be inherited from this package —
Composer reads both only from the root `composer.json`:

- `allow-plugins`, without which the installer plugin is blocked and PHPCS will not find the
  `WordPress` or `PHPCompatibilityWP` standards.
- `minimum-stability`/`prefer-stable`, without which `phpcompatibility-wp` cannot resolve. We
  track its 3.0 alpha because the last stable release wraps a PHP engine from 2019 that knows
  nothing about PHP 8: against `testVersion 8.4-` it misses implicitly nullable parameters,
  `E_STRICT`, `utf8_encode` and more, and reports `each()` as merely deprecated rather than
  removed.

  Note that `minimum-stability` applies to the whole root, not just to this package.
  `prefer-stable` expresses a preference, not a guarantee: resolution still favours a stable
  release wherever one satisfies a constraint, but a dependency whose constraint can only be
  satisfied by a pre-release will now resolve to that pre-release instead of failing.

## Scripts

### update-configs

This scripts provides a quick way to copy the configs in this repo into the correct places in a project, as well as sync changes to them when this repo is updated.

When this repo is included in a project via Composer, you can run the following command:

```bash
TEXTDOMAIN=wporg composer exec update-configs
```

...to copy/update the config files. Replace `wporg` if the project uses a different text domain. Any config file that already exists will trigger a prompt asking if you want to replace it.
