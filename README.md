# musical-pancake

Transmit data from various servers to a central database using "default" components (Nginx, PHP).
I'm trying to secure the transmission using client certificates.

Needed components on the server's side:
- nginx
- php-fpm
- fcgiwrap (instead of php-fpm for Perl?)

Generate certificates:

    # Create directory for the certificates
    mkdir certs
    
    # Create CA's key and certificate
    openssl req -nodes -newkey rsa:4096 -x509 -days 365 -keyout certs/ca.key -out certs/ca.crt -subj "/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestOU/CN=example.com"
    
    # Create the server's key and certificate
    openssl req -nodes -newkey rsa:2048 -keyout certs/server.key -out certs/server.csr -subj "/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestServerOU/CN=example.com"
    openssl x509 -req -days 365 -in certs/server.csr -CA certs/ca.crt -CAkey certs/ca.key -set_serial 01 -out certs/server.crt
    
    # Create the client's key and certificate
    openssl req -nodes -newkey rsa:2048 -keyout certs/client-01.key -out certs/client-01.csr -subj "/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestClient01/CN=client01"
    openssl x509 -req -days 365 -in certs/client-01.csr -CA certs/ca.crt -CAkey certs/ca.key -set_serial 01 -out certs/client-01.crt
    
    # Create another client's key and certificate
    openssl req -nodes -newkey rsa:2048 -keyout certs/client-02.key -out certs/client-02.csr -subj "/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestClient02/CN=client02"
    openssl x509 -req -days 365 -in certs/client-02.csr -CA certs/ca.crt -CAkey certs/ca.key -set_serial 01 -out certs/client-02.crt


## Testing with curl

### Simple example

curl  -k  --cacert certs/ca.crt  --cert certs/client-01.crt  --key certs/client-01.key  https://localhost:33443/index.php
(#TODO: omit the -k flag and make it work!)

Having <?php print phpinfo(); ?> in the index.php file, the result is ...

    <tr><td class="e">$_SERVER['SCRIPT_NAME']</td><td class="v">/index.php</td></tr>
    <tr><td class="e">$_SERVER['DN']</td><td class="v">/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestClient01/CN=example.com</td></tr>
    <tr><td class="e">$_SERVER['VERIFIED']</td><td class="v">SUCCESS</td></tr>

### Transfer data

Transfer data to the server (index.php):

    <?php
    print file_get_contents( $_FILES[ "inputfile" ][ "tmp_name" ] );

... when calling this PHP file with:
    pacman -Q | curl  -k  -F inputfile=@-  --cacert certs/ca.crt  --cert certs/client-02.crt  --key certs/client-02.key  https://localhost:33443/index.php

... curl prints the transmitted list back to stdout.


## Client scenario

    hostname | curl  -d @-  --cacert certs/ca.crt  --cert certs/client-02.crt  --key certs/client-02.key  "https://localhost:33443/register.php"
    curl  --cacert certs/ca.crt  --cert certs/client-02.crt  --key certs/client-02.key  "https://localhost:33443/ping.php?guid=9f8f2492-04a5-4622-b25b-9cea618de500"
    pacman -Q | curl  -F inputfile=@-  --cacert certs/ca.crt  --cert certs/client-02.crt  --key certs/client-02.key  "https://localhost:33443/transmit.php?guid=9f8f2492-04a5-4622-b25b-9cea618de500&type=installed-packages"

Store the GUID returned from the register.php call and supply it to the ping.php and transmit.php call.


## Hints ##

If you get an "Input file not specified" answer from curl, maybe the directory is not specified in the open_basedir option. As soon as I added the directory to the open_basedir option in the /etc/php/php.ini and restarted nginx and php-fpm services, it worked :-)


## Database

I'd like to store the data in a MariaDB/MySQL database.

My steps in phpMyAdmin (see file database/db-dump.sql):
- create a database ("musical-pancake", collation utf8-bin)
- create a table ("systems")
- create a user ("musical-pancake", password "aEfV7I5n0tJfgCZ0")
- create a table ("installed_packages") for storing pacman information
- create a table ("saved_data") for grouping data in the installed_packages table


## TODO

- Delete old rows from installed_packages before storing new data (transmit.php)
- Return JSON formatted status back to the client
- Describe how to re-new certificates (CA/server/client)
- Add statistic pages (system with most packages, packages most installed, ...)
- Add relationship keys/foreign keys in database (example: delete all appropriate rows when row in saved_data is deleted)


## DATABASE LAYOUT

    systems                  saved_data              installed_packages
    -------                  ----------              ------------------
    id          <---+        id           <---+      id
    guid            +----    systems_id       +----  saved_data_id
    dn                       datetime                package_name
    servername               successful              package_version
    created                  type         ----+
    last_contact                              !
                                              !
                                              +-----> references to array $data_tables_list in transmit.php:
                                                      0 --> table installed_packages

In the systems table, all systems (clients) are recorded. Each registering client gets a random GUID assigned.

When data is transferred (so far: pacman's installed packages) a new record is created in the saved_data table. The field successful is initialized with false. The packages are added to the installed_packages table, referencing the saved_data's id. After the insert has been completed, the field successful is set to true.

At the moment, the records are added. There's no garbage collection.


## LICENSE

All files and/or data is released under GNU General Public License v2.0 (GPLv2)
See LICENSE file for details
  
