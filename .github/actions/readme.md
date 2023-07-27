# Composite Actions

These are shared actions, meant to be used in projects' github workflows. Read more about [composite actions on the github docs](https://docs.github.com/en/actions/creating-actions/creating-a-composite-action).

- `actions/setup` This installs dependencies and sets up the project configs.
- `actions/lint` This runs the linters for CSS, JS, and PHP
- `actions/test-php` This runs the php unit tests

They are set up as separate actions to mix and match in projects — in case any given project needs more setup steps before tests can be run, for example.

The scripts assume a few things about a project:

1. The project uses composer, with `wporg-repo-tools`, so that `composer exec update-configs` will work.
2. The project uses yarn and at least one yarn workspace for the theme.
3. Optionally, there is an `.nvmrc`.

The commands in the actions assume a few standard scripts are available. 

The parent project needs:

- `lint:php` for lint action
- `wp-env` for test-php action
- `test:php` for test-php action

All workspace projects need:

- `build` for setup action
- `lint:css` for lint action
- `lint:js` for lint action

## Usage

These are used just like published actions, except you need to use the path to the containing folder since there are multiple actions in this repo.

For example, to run the PHP Unit Tests action, add this to your workflow.

```yml
- name: Test
  uses: WordPress/wporg-repo-tools/.github/actions/test-php
```

The setup action accepts two inputs. The `token` is required for composer to install the dependencies (the secrets are not passed through otherwise). The `textdomain` is only necessary if it's not "wporg".

```yml
- name: Setup
  uses: WordPress/wporg-repo-tools/.github/actions/setup
  with:
    token: ${{ secrets.GITHUB_TOKEN }}
    textdomain: wporg-custom
```
