COVERAGE ?= 

all: tests

coverage:
	$(MAKE) -C . tests COVERAGE='--coverage-html coverage'

tests:
	./vendor/bin/phpunit $(COVERAGE)

.PHONY: tests coverage
