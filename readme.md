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
		"wordpress/wporg-repo-tools": "dev-trunk"
	}
}
```

## Scripts

TBD
