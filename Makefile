COVERAGE ?= 
PHP ?= /bin/env php

all: tests

coverage:
	$(MAKE) -C . tests COVERAGE='--coverage-html coverage'

tests:
	$(PHP) ./vendor/bin/phpunit $(COVERAGE)

.PHONY: tests coverage
