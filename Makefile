#--------------------------------------------------------------------------------
# PHPCSFIXER
#--------------------------------------------------------------------------------
phpcs-fixer:
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v

phpcs-fixer-dry:
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v --dry-run

phpcs-fixer-stop:
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php -v --dry-run --stop-on-violation
