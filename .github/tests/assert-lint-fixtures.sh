#!/bin/bash
#
# Assert that the shared configs catch what they are meant to catch.
#
# A config that failed to load, or lost a rule, would still let a green lint run
# through. So point the linters at files violating rules this package
# configures, and assert the specific rule that fires.
#
# Run from the staged fixture root, after `setup:tools`. The legacy eslintrc
# config is not covered; see issue #50.

set -uo pipefail

fixtures="action-source/.github/tests/fixture-consumer/lint-fixtures"
failures=0

# Report a passing assertion. $1 - what held.
pass() {
	echo "  ok: $1"
}

# Report a failing assertion. $1 - what did not hold.
fail() {
	echo "::error::$1"
	failures=$(( failures + 1 ))
}

# Assert a linter rejected a file, citing a rule. $1 label, $2 rule, $3 status, $4 output.
assert_violation() {
	local label="$1" rule="$2" status="$3" output="$4"

	if [ "$status" -eq 0 ]; then
		fail "$label: expected a non-zero exit, got 0. The config is not being applied."
		echo "$output"
		return
	fi

	if ! grep -qF "$rule" <<<"$output"; then
		fail "$label: exited non-zero but never reported '$rule'."
		echo "$output"
		return
	fi

	pass "$label reported $rule"
}

# Assert a linter accepted a file. $1 label, $2 status, $3 output.
assert_clean() {
	local label="$1" status="$2" output="$3"

	if [ "$status" -ne 0 ]; then
		fail "$label: expected conforming code to pass, got exit $status."
		echo "$output"
		return
	fi

	pass "$label accepted conforming code"
}

echo "Asserting the ESLint config is in effect..."
out=$( ./node_modules/.bin/wp-scripts lint-js "$fixtures/bad.js" 2>&1 ); status=$?
assert_violation "eslint id-length" "id-length" "$status" "$out"
assert_violation "eslint text domain" "@wordpress/i18n-text-domain" "$status" "$out"
assert_violation "eslint object-shorthand" "object-shorthand" "$status" "$out"

echo "Asserting the stylelint config is in effect..."
out=$( ./node_modules/.bin/wp-scripts lint-style "$fixtures/bad.pcss" 2>&1 ); status=$?
assert_violation "stylelint rule-empty-line-before" "rule-empty-line-before" "$status" "$out"
assert_violation "stylelint max-line-length" "@stylistic/max-line-length" "$status" "$out"

echo "Asserting the PHPCS standard is in effect..."
out=$( ./vendor/bin/phpcs --standard=phpcs.xml.dist --no-colors -s "$fixtures/bad.php" 2>&1 ); status=$?
assert_violation "phpcs text domain" "WordPress.WP.I18n.TextDomainMismatch" "$status" "$out"
assert_violation "phpcs PHP version target" "PHPCompatibility.FunctionDeclarations.RemovedImplicitlyNullableParam" "$status" "$out"

echo "Asserting the standard's exclusions are in effect..."
out=$( ./vendor/bin/phpcs --standard=phpcs.xml.dist --no-colors -s "$fixtures/clean.php" 2>&1 ); status=$?
assert_clean "phpcs short array syntax and short ternaries" "$status" "$out"

if [ "$failures" -gt 0 ]; then
	echo "::error::$failures lint-config assertion(s) failed."
	exit 1
fi

echo "All lint-config assertions held."
