const { join } = require( 'path' );

module.exports = {
	blockTemplatesPath: join(__dirname, "block"),
	pluginTemplatesPath: join(__dirname, "plugin"),
	defaultValues: {
		namespace: 'wporg',
		author: 'The WordPress Meta Team',
		version: '0.1.0',
		slug: 'example-block',
		title: 'Example Block',
		description: 'A block for use across the whole wp.org network.',
		dashicon: 'networking',
		editorStyle: false,
		style: 'file:./style.css',
		npmDependencies: [],
		npmDevDependencies: [],
		wpScripts: false,
		wpEnv: false,
	},
};
