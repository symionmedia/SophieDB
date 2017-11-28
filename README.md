# sophiedb
PHP based JSON database
************
SophieDB
========

SophieDB is an opensource JSON based database accessed by a RESTful API.
Direct access to json files is currently prohibited by the .htacces file.

RPC Service, powerd by YAR, is currently in developent.

The SophieDB\Core consists of 3 types of functions:
- Set functions:
    These functions set values for variables.
- Print functions:
    These functions print responses out to the user.
- Return functions:
    These functions return a value to the script that insantiated it. 

Full API documentation @ docs.sophiedb.ga/V2 
Trello Development board: https://trello.com/b/6LPRYUoi/sophiedb
Github Repo: https://github.com/symionmedia/sophiedb

Features
--------

- Create Database
- Delete Database
- Query Database
- Search:
    - by Value
    - by Key
    - by Key & Value pair
- Post 
    - an Array
    - a String
    - an Integer

- Auto database backup.

System Requiremnts
------------------

- Apache 2.4 or similar webserver
- PHP 7.0.25 or higher
- mod_rewrite Support

Installation
------------

Pretty much no istallation.
Follow these steps to get going:

    - Copy the ZIP release to your webserver.
    - Unzip.
    - Edit: ./SophieCore/user_db.json
        - Add User to user_db.json:
            {
                "USER": "",
                "KEY": "",
                "TOKEN": "",
                "DATABASE": ""
            }
    - Enjoy.

And have a read of the source code.
It'll give you a pretty good idea of how it works. (:

Contribute
----------

- Issue Tracker: github.com/symionmedia/sophiedb/issues
- Source Code: github.com/symionmedia/sophiedb

Support
-------

If you are having any issues, please let me know.
Contact: issue@sophiedb.ga


License
-------

SophieDB is licensed under the MIT license.




-------------------------------------------------------------
API REFRENCE
-------------------------------------------------------------

Baseurl:            https://sophiedb.ga/V2/
Query format:       TYPE / DATABASE / (FUNCTION SPECIFIC VARIABLES)
