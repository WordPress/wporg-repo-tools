#!/usr/bin/php
<?php

namespace WordPressdotorg\Repo_Tools\Bin\I18N;

function get_workspace_path() {
	$workspace_path = getenv( 'GITHUB_WORKSPACE' );
	if ( ! $workspace_path ) {
		$workspace_path = __DIR__ . '/../../../..';
	}
	return $workspace_path;
}

/**
 * Perform an HTTP GET request using file_get_contents.
 *
 * @param string $url The URL to fetch.
 *
 * @return object Object with status_code, body, and headers properties.
 */
function http_get( $url ) {
	$context = stream_context_create(
		[
			'http' => [
				'user_agent'      => 'WordPress.org Repo Tools/1.0; https://github.com/WordPress/wporg-repo-tools',
				'ignore_errors'   => true,
			],
		]
	);

	$body        = file_get_contents( $url, false, $context );
	$status_code = 0;
	$headers     = [];

	if ( isset( $http_response_header ) ) {
		foreach ( $http_response_header as $header ) {
			if ( preg_match( '|^HTTP/\S+\s+(\d+)|', $header, $match ) ) {
				$status_code = intval( $match[1] );
			} elseif ( str_contains( $header, ':' ) ) {
				[ $key, $value ]                    = explode( ':', $header, 2 );
				$headers[ strtolower( trim( $key ) ) ] = trim( $value );
			}
		}
	}

	return (object) [
		'status_code' => $status_code,
		'body'        => $body,
		'headers'     => $headers,
	];
}

class Generate_Translation_Strings {

	/**
	 * The URL to use for REST API queries.
	 *
	 * @var string $endpoint_base
	 */
	public $endpoint_base = 'https://wordpress.org/wp-json/wp/v2/';

	/**
	 * An allowed-list of taxonomies to fetch.
	 *
	 * @var string[] $valid_taxonomies
	 */
	public $valid_taxonomies = array( 'post_tag', 'category' );

	/**
	 * An allowed-list of post types to fetch.
	 *
	 * @var string[] $valid_post_types
	 */
	public $valid_post_types = array( 'post', 'page' );

	/**
	 * The textdomain to use.
	 *
	 * @var string $textdomain
	 */
	public $textdomain = 'wporg';

	/**
	 * Initialze the class, set up the properties based on CLI args.
	 */
	public function __construct() {
		$options = array( 'url::', 'taxonomies::', 'no_taxonomies', 'post_types::', 'no_post_types', 'textdomain::' );
		$args = getopt( '', $options );

		if ( ! empty( $args['url'] ) && filter_var( $args['url'], FILTER_VALIDATE_URL ) ) {
			$this->endpoint_base = $args['url'];
		}

		if ( ! empty( $args['taxonomies'] ) ) {
			$this->valid_taxonomies = explode( ',', $args['taxonomies'] );
		}

		if ( isset( $args['no_taxonomies'] ) ) {
			$this->valid_taxonomies = array();
		}

		if ( ! empty( $args['post_types'] ) ) {
			$this->valid_post_types = explode( ',', $args['post_types'] );
		}

		if ( isset( $args['no_post_types'] ) ) {
			$this->valid_post_types = array();
		}

		if ( ! empty( $args['textdomain'] ) ) {
			// Pulled from `sanitize_title_with_dashes`.
			$textdomain = strip_tags( $args['textdomain'] );
			$textdomain = strtolower( $textdomain );
			$textdomain = preg_replace( '/&.+?;/', '', $textdomain );
			$textdomain = str_replace( '.', '-', $textdomain );
			$textdomain = preg_replace( '/[^%a-z0-9 _-]/', '', $textdomain );
			$textdomain = preg_replace( '/\s+/', '-', $textdomain );
			$textdomain = preg_replace( '|-+|', '-', $textdomain );
			$textdomain = trim( $textdomain, '-' );
			$this->textdomain = $textdomain;
		}
	}

	/**
	 * Get data about taxonomies from a REST API endpoint.
	 *
	 * @return array
	 */
	public function get_taxonomies() {
		$endpoint = $this->endpoint_base . 'taxonomies';

		$response = http_get( $endpoint );

		if ( 200 !== $response->status_code ) {
			die( 'Could not retrieve taxonomy data.' );
		}

		$taxonomies = json_decode( $response->body, true );

		if ( ! is_array( $taxonomies ) ) {
			die( 'Taxonomies request returned unexpected data.' );
		}

		$taxonomies = array_filter(
			$taxonomies,
			function( $tax ) {
				return in_array( $tax['slug'], $this->valid_taxonomies, true );
			}
		);

		return $taxonomies;
	}

	/**
	 * Get data about a taxonomy's terms from a REST API endpoint.
	 *
	 * @param array $taxonomy
	 *
	 * @return array
	 */
	public function get_taxonomy_terms( $taxonomy ) {
		$endpoint    = $this->endpoint_base . $taxonomy['rest_base'] . '?per_page=100';
		$terms       = array();
		$page        = 1;
		$total_pages = 1;

		$response = http_get( $endpoint );

		if ( isset( $response->headers['x-wp-totalpages'] ) ) {
			$total_pages = intval( $response->headers['x-wp-totalpages'] );
		}

		while ( $page <= $total_pages ) {
			if ( 'cli' === php_sapi_name() ) {
				echo sprintf(
					"Page %d... \n",
					$page // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			if ( 200 !== $response->status_code ) {
				die(
					sprintf(
						'Could not retrieve terms for %s.',
						$taxonomy['slug'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					)
				);
			}

			$more_terms = json_decode( $response->body, true );

			if ( ! is_array( $more_terms ) ) {
				die(
					sprintf(
						'Terms request for %s returned unexpected data.',
						$taxonomy['slug'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					)
				);
			}

			$terms = array_merge( $terms, $more_terms );

			$links = array();
			if ( isset( $response->headers['link'] ) ) {
				$links = $this->parse_link_header( $response->headers['link'] );
			}

			if ( ! empty( $links['next'] ) ) {
				$response = http_get( $links['next'] );
			}

			$page ++;
		}

		return $terms;
	}

	/**
	 * Get data about taxonomies from a REST API endpoint.
	 *
	 * @return array
	 */
	public function get_post_types() {
		$endpoint = $this->endpoint_base . 'types';

		$response = http_get( $endpoint );

		if ( 200 !== $response->status_code ) {
			die( 'Could not retrieve taxonomy data.' );
		}

		$types = json_decode( $response->body, true );

		if ( ! is_array( $types ) ) {
			die( 'Taxonomies request returned unexpected data.' );
		}

		$types = array_filter(
			$types,
			function( $type ) {
				return in_array( $type['slug'], $this->valid_post_types, true );
			}
		);

		return $types;
	}

	/**
	 * Get data about posts (of a specific type) from a REST API endpoint.
	 *
	 * @return array
	 */
	public function get_posts( $post_type ) {
		$endpoint    = $this->endpoint_base . $post_type['rest_base'] . '?per_page=100';
		$posts       = array();
		$page        = 1;
		$total_pages = 1;

		$response  = http_get( $endpoint );

		if ( isset( $response->headers['x-wp-totalpages'] ) ) {
			$total_pages = intval( $response->headers['x-wp-totalpages'] );
		}

		while ( $page <= $total_pages ) {
			if ( 'cli' === php_sapi_name() ) {
				echo sprintf(
					"Page %d... \n",
					$page // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			if ( 200 !== $response->status_code ) {
				die(
					sprintf(
						'Could not retrieve posts for %s.',
						$post_type['slug'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					)
				);
			}

			$more_posts = json_decode( $response->body, true );

			if ( ! is_array( $more_posts ) ) {
				die(
					sprintf(
						'Posts request for %s returned unexpected data.',
						$post_type['slug'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					)
				);
			}

			$posts = array_merge( $posts, $more_posts );

			$links = array();
			if ( isset( $response->headers['link'] ) ) {
				$links = $this->parse_link_header( $response->headers['link'] );
			}

			if ( ! empty( $links['next'] ) ) {
				$response = http_get( $links['next'] );
			}

			$page++;
		}

		return $posts;
	}

	/**
	 * Parse a link header from a WP REST API response into an array of prev/next URLs.
	 *
	 * @param string $link_header
	 *
	 * @return array Associative array of links, with possible keys of next and prev, values are URLs.
	 */
	public function parse_link_header( $link_header ) {
		$links = explode( ',', $link_header );

		return array_reduce(
			$links,
			function( $carry, $item ) {
				$split = explode( ';', trim( $item ) );
				preg_match( '|<([^<>]+)>|', $split[0], $url );
				preg_match( '|rel="([^"]+)"|', $split[1], $rel );

				if ( ! empty( $url[1] ) && ! empty( $rel[1] ) ) {
					$carry[ $rel[1] ] = filter_var( $url[1], FILTER_VALIDATE_URL );
				}

				return $carry;
			},
			array()
		);
	}

	/**
	 * Run the script.
	 */
	public function main() {
		if ( 'cli' === php_sapi_name() ) {
			echo "\n";
			echo "Retrieving taxonomies...\n";
		}

		$taxonomies = $this->get_taxonomies();

		if ( 'cli' === php_sapi_name() ) {
			echo "Retrieving terms...\n";
		}

		$terms_by_tax = array();
		foreach ( $taxonomies as $taxonomy ) {
			if ( 'cli' === php_sapi_name() ) {
				echo sprintf(
					'%s... ',
					$taxonomy['name'] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			$terms = $this->get_taxonomy_terms( $taxonomy );

			if ( 'cli' === php_sapi_name() ) {
				echo "\n";
			}

			if ( count( $terms ) > 0 ) {
				$terms_by_tax[ $taxonomy['name'] ] = $terms;
			}

			unset( $terms );
		}

		if ( 'cli' === php_sapi_name() ) {
			echo "\n";
		}

		$file_content = '';
		foreach ( $terms_by_tax as $tax_label => $terms ) {
			$label = addcslashes( $tax_label, "'" );

			foreach ( $terms as $term ) {
				$name = addcslashes( $term['name'], "'" );
				$file_content .= "_x( '{$name}', '$label term name', '{$this->textdomain}' );\n";

				if ( 'cli' === php_sapi_name() ) {
					echo "$name\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				if ( $term['description'] ) {
					$description = addcslashes( $term['description'], "'" );
					$file_content .= "_x( '{$description}', '$label term description', '{$this->textdomain}' );\n";
				}
			}
		}

		if ( 'cli' === php_sapi_name() ) {
			echo "\n";
			echo "Retrieving post types...\n";
		}

		$post_types = $this->get_post_types();

		foreach( $post_types as $post_type ) {
			if ( 'cli' === php_sapi_name() ) {
				echo "\n";
				echo sprintf(
					"Retrieving %s...\n",
					$post_type['name']
				);
			}

			foreach ( $this->get_posts( $post_type ) as $page ) {
				$title = addcslashes( $page['title']['rendered'], "'" );
				$file_content .= "_x( '{$title}', '{$post_type['slug']} title', '{$this->textdomain}' );\n";

				if ( 'cli' === php_sapi_name() ) {
					echo "$title\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			}
		}

		$path = get_workspace_path() . '/extra';
		if ( ! is_writeable( $path ) ) {
			mkdir( $path );
		}

		$file_name = 'translation-strings.php';
		$file_header = <<<HEADER
<?php
// phpcs:disable
/**
 * Generated file for translation strings.
 *
 * Used to import additional strings into the GlotPress translation project.
 *
 * ⚠️ This is a generated file. Do not edit manually. See bin/i18n.php.
 * ⚠️ Do not require or include this file anywhere.
 */


HEADER;

		file_put_contents( $path . '/' . $file_name, $file_header . $file_content );

		echo $file_header . $file_content;

		if ( 'cli' === php_sapi_name() ) {
			echo "\n";
			echo "Done.\n";
		}
	}
}

$runner = new Generate_Translation_Strings();
$runner->main();
