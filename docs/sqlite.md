### Sqlite

> Notice: This will only work with very recent versions of ruby-sequel

It is easy to convert CosCMS to sqlite or postgresql project using the ruby tool sequel. 

install sequel, sqlite, mysql:

    sudo aptitude install ruby-sequel libsqlite3-ruby libmysql-ruby

sequel mysql://root:password@localhost/database -C sqlite://sqlite/database.sql

change database url in config/config.ini

    url = "sqlite:/home/dennis/www/default/sqlite/database.sql"    

Make sure sqlite/databse.sql is writable and readable: 

    chmod -R 777 sqlite/database.sql

If the database is in the web space you may consider which group can read and write, e.g. apache2 (www-data)
