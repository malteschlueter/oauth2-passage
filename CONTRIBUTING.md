# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/malteschlueter/oauth2-passage).

## Pull Requests

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the README and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow SemVer. Randomly breaking public APIs is not an option.

- **Create topic branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

- **Ensure tests pass!** - Please run the tests (see below) before submitting your pull request, and make sure they pass. We won't accept a patch until all tests pass.

- **Ensure no coding standards violations** - Please run PHP CS Fixer with the configuration from the project (see below) before submitting your pull request. A violation will cause the build to fail, so please make sure there are no violations. We can't accept a patch if the build fails.

## Running PHPUnit

``` bash
make phpunit
```
or
``` bash
php dev-ops/ci/vendor/bin/phpunit --configuration dev-ops/ci/config/phpunit.xml.dist
```

## Running PHPStan

``` bash
make phpstan
```
or
``` bash
php dev-ops/ci/vendor/bin/phpstan analyse --configuration=dev-ops/ci/config/phpstan.neon
```

## Running PHP CS Fixer

``` bash
make cs-fix
```
or
``` bash
php dev-ops/ci/vendor/bin/php-cs-fixer fix --config=dev-ops/ci/config/php-cs-fixer.dist.php
```

**Happy coding**!
