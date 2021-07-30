# How to run a test:

Go with the console in your root folder.

Then go into the bin directory of the vendor folder.
```shell
cd vendor/bin
```

Now you can run a test in the commandline with
```shell
phpunit ./../Test/yourFolder/yourTestFile.php
```


If you need help or more information how to test with phpunit 
type in the commandline
```shell
phpunit
```

# Run unittests on commit with private repos

Demo yml

```yml
name: CI

on: [push]

jobs:
  build-test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v2

      - name: Install dependencies
        env:
          COMPOSER_AUTH: '{"github-oauth": {"github.com": "${{ secrets.COMPOSER_AUTH }}"} }'
        run: |
          composer update --prefer-dist --no-interaction --no-suggest
      - name: PHPUnit Tests
        uses: php-actions/phpunit@v2
        with:
          bootstrap: vendor/autoload.php
          configuration: tests/phpunit.xml
          args: --coverage-text
```

Create with your repository main user a personal access token.

Go in your repository settings and ad the token as a new secret with the name COMPOSER_AUTH.

Save the changes and make a commit.
