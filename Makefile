phpstan:
	./vendor/bin/phpstan -vvv analyse src/ --level 8

psalm:
	./vendor/bin/psalm --show-info=true

phpspec:
	./vendor/bin/phpspec run -v --format=pretty
