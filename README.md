[![Build Status](https://semaphoreci.com/api/v1/diaz/open_list/branches/master/badge.svg)](https://semaphoreci.com/diaz/open_list)

# Sanction lists parser

## DESCRIPTION

This project is only a parser of public financial sanction lists. The target is to get a common and flexible format that can be extracted to different formats and imported in other tools.

Economic sanctions are domestic penalties applied unilaterally by one country (or multilaterally, by a group of countries) on another country (or group of countries). Economic sanctions may include various forms of trade barriers and restrictions on financial transactions. Economic sanctions are not necessarily imposed because of economic circumstances — they may also be imposed for a variety of political and social issues. Economic sanctions can be used for achieving domestic political gain. [Wikipedia](https://en.wikipedia.org/wiki/Economic_sanctions)

Since the lists are used in 'sensible' projects, the objective is not to import as many lists as possible, but to ensure that they are extracted well and without any missing data that could be relevant. 

## INCLUDED LISTS

* UK. HM Treasury [More info](https://www.gov.uk/government/publications/financial-sanctions-consolidated-list-of-targets/consolidated-list-of-targets)

* US. Department of the Treasury. SDN List [More info](https://www.treasury.gov/resource-center/sanctions/SDN-List/Pages/consolidated.aspx)

## IN PROCESS

* Un. Consolidated United Nations Security Council Sanctions List [More info](https://www.un.org/sc/suborg/en/sanctions/un-sc-consolidated-list)

## SOON

* PEPs


## HOW TO INSTALL

1. Install with [composer](https://getcomposer.org/download/), for example 
`php composer.phar install`

2. Create folder lists and output
 `mkdir lists`
 `mkdir output`

## HOW TO USE

1. Receive the lists
`php open_list.php receive`

2. Process the lists
`php open_list.php process`

* Note, you can see the available commands with
`php open_list.php`

* Execute tests
`php vendor/phpunit/phpunit/phpunit`

