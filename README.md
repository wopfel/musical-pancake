# musical-pancake

Transmit data from various servers to a central database using "default" components (Nginx, PHP).
I'm trying to secure the transmission using client certificates.

Needed components on the server's side:
- nginx
- php-fpm

Generate certificates:
- mkdir certs
- openssl req -nodes -newkey rsa:4096 -x509 -days 365 -keyout certs/ca.key -out certs/ca.crt -subj "/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestOU/CN=example.com"
- openssl req -nodes -newkey rsa:1024 -keyout certs/server.key -out certs/server.csr -subj "/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestServerOU/CN=example.com"
- openssl x509 -req -days 365 -in certs/server.csr -CA certs/ca.crt -CAkey certs/ca.key -set_serial 01 -out certs/server.crt
- openssl req -nodes -newkey rsa:1024 -keyout certs/client-01.key -out certs/client-01.csr -subj "/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestClient01/CN=example.com"
- openssl x509 -req -days 365 -in certs/client-01.csr -CA certs/ca.crt -CAkey certs/ca.key -set_serial 01 -out certs/client-01.crt


Testing with curl:
curl  -k  --cacert certs/ca.crt  --cert certs/client-01.crt  --key certs/client-01.key  https://localhost:33443/index.php
(#TODO: omit the -k flag and make it work!)

Having <?php print phpinfo(); ?> in the index.php file, the result is ...

    <tr><td class="e">$_SERVER['SCRIPT_NAME']</td><td class="v">/index.php</td></tr>
    <tr><td class="e">$_SERVER['DN']</td><td class="v">/C=DE/ST=Test1/L=Test2/O=TestO/OU=TestClient01/CN=example.com</td></tr>
    <tr><td class="e">$_SERVER['VERIFIED']</td><td class="v">SUCCESS</td></tr>


Tip:
If you get an "Input file not specified" answer from curl, maybe the directory is not specified in the open_basedir option. As soon as I added the directory to the open_basedir option in the /etc/php/php.ini and restarted nginx and php-fpm services, it worked :-)

