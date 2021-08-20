# Global Header

## Register as a block (for Full Site Editing themes)

Add as a composer dependency, install, then

```php
add_action( 'muplugins_loaded', function() {
	require_once REPO_TOOLS_DIR . '/block.php';
} );
```


## Include directly in PHP (for classic themes)

Add as a composer dependency, install, then

require_once 'vendor/wporg-repo-tools/blocks/global-header/header.php';


## Embed as an iframe (for Trac, Codex, etc)

