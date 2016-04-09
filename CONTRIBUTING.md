Contributing
============

Your contribution is welcome!

Before proposing a pull request, check the following:

* Your code should follow the [PSR-1,PSR-2 and PSR-4](https://github.com/php-fig/fig-standards) (and use [php-cs-fixer](https://github.com/fabpot/PHP-CS-Fixer) to fix inconsistencies).
* Unit tests should still pass after your patch
* As much as possible, add unit tests for your code
* If you commit a new feature, be prepared to help maintaining it. Watch the project on GitHub, and please comment on issues or PRs regarding the feature you contributed.

Once your code is merged, it is available for free to everybody under the MIT License. Publishing your Pull Request on this GitHub repository means that you agree with this license for your contribution.

Thank you for your contribution!


Steps
--------
1. Receive the lists
`php open_list.php receive`

2. Process the lists
`php open_list.php process`


How to add a new list
--------
1. Add the info to the config file of the lists
2. Execute the Receiver, it should make the folder and download the list. `php src/OpenList/Receiver.php`
3. Create a new parser inside of the folder parsers and implements the IList interface
4. Execute the Processor and check it works `php src/OpenList/Processor.php`
5. Create tests for that parsers
6. (Optional) Create a helper to create the same sanction file with fake content (Check helpers folder). And execute the helper, in case of the Ofac list is  `php src/OpenList/helpers/OfacCreator.php`


Useful commands
--------

1. Php cs fixer
`php vendor/fabpot/php-cs-fixer/php-cs-fixer fix src`

2. Check the code with codesniffer
`php vendor/squizlabs/php_codesniffer/scripts/phpcs src`

3. Execute tests
`php vendor/phpunit/phpunit/phpunit`
Or execute a particular test with --filter, example: `php vendor/phpunit/phpunit/phpunit --filter Usofac*`
*Xdebug must be enabled for the coverage detail