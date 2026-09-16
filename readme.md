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

This package ships a PHPCS standard named `wporg`, and requires the packages it builds on, so
projects get them transitively and should not list them separately:

- `squizlabs/php_codesniffer`, which provides the `phpcs` and `phpcbf` binaries
- `wp-coding-standards/wpcs`
- `phpcompatibility/phpcompatibility-wp`
- `dealerdirect/phpcodesniffer-composer-installer`, which registers the standards' paths with PHPCS

`composer install` registers the `wporg` standard automatically, so a project's `phpcs.xml.dist`
only has to reference it by name:

```xml
<rule ref="wporg" />
<config name="text_domain" value="your-text-domain" />
```

Set `text_domain`. Without it `WordPress.WP.I18n` stops checking text domains altogether rather
than reporting a violation, so a missing value weakens the lint silently.

If your project already lists any of the four packages above in its own `require-dev`, remove
those entries. Leaving a stale constraint behind is the most likely upgrade failure: a project
still pinning `dealerdirect/phpcodesniffer-composer-installer` at `^0.7.0`, for example, cannot
resolve against the `^1.0` required here, and Composer reports it as a conflict on that package
rather than anything to do with this one.

`allow-plugins` and `minimum-stability` are both required, and neither can be inherited from
this package — Composer reads them only from the root `composer.json`. `prefer-stable` is not
strictly required, but is strongly recommended alongside `minimum-stability`:

- `allow-plugins`, without which the installer plugin is blocked and PHPCS will not find the
  `wporg`, `WordPress` or `PHPCompatibilityWP` standards.
- `minimum-stability`, without which `phpcompatibility-wp` cannot resolve. We
  track its 3.0 alpha because the last stable release wraps a PHP engine from 2019 that knows
  nothing about PHP 8: against `testVersion 8.4-` it misses implicitly nullable parameters,
  `E_STRICT`, `utf8_encode` and more, and reports `each()` as merely deprecated rather than
  removed.

  Note that `minimum-stability` applies to the whole root, not just to this package.
  `prefer-stable` expresses a preference, not a guarantee: resolution still favours a stable
  release wherever one satisfies a constraint, but a dependency whose constraint can only be
  satisfied by a pre-release will now resolve to that pre-release instead of failing.

### How the configs reach a project

Projects extend the configs in this package rather than holding a copy of their contents. The
files `update-configs` writes are short stubs that point back into `vendor/`, so the actual
rules arrive with `composer update` and a project's own copies rarely need to change:

| Project file | Extends |
| --- | --- |
| `phpcs.xml.dist` | the `wporg` PHPCS standard, registered by Composer |
| `eslint.config.js` | `configs/eslint.js` (ESLint 9+ / `@wordpress/scripts` 32+) |
| `.eslintrc.js` | `configs/eslintrc.js` (ESLint 8) |
| `.prettierrc.js` | `configs/prettier.js` |
| `.stylelintrc` | `configs/stylelint.js` |

Both ESLint configs are generated from the same rule set in `configs/rules.js`, so a rule only
has to change in one place. A project needs whichever one matches its ESLint version — ESLint 9
ignores `.eslintrc.js`, and ESLint 8 ignores `eslint.config.js`.

Project-specific additions go in the generated file, alongside the call it already contains.
`update-configs` still prompts before overwriting, and a non-interactive shell answers that
prompt with "no" — but since the stubs change far less often than the rules inside them, a
stale stub is now much less likely to matter.

## Scripts

### update-configs

This scripts provides a quick way to copy the configs in this repo into the correct places in a project, as well as sync changes to them when this repo is updated.

When this repo is included in a project via Composer, you can run the following command:

```bash
TEXTDOMAIN=wporg composer exec update-configs
```

...to copy/update the config files. Replace `wporg` if the project uses a different text domain. Any config file that already exists will trigger a prompt asking if you want to replace it.
