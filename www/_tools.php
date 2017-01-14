<?php

# Check if called from another PHP file
if ( ! defined( "MUSICAL_PANCAKE" ) ) {
    die("Mismatch");
}



# Establish a new database connection
# Returns the PDO object if successful
function newDatabaseConnection() {

    return new PDO( 'mysql:host=localhost;dbname=musical-pancake', 'musical-pancake', 'aEfV7I5n0tJfgCZ0' );

}


# Credits: http://php.net/manual/de/function.com-create-guid.php#117893
# Use more cryptographically strong algorithm to generate pseudo-random bytes and format it as GUID v4 string
function guidv4() {
    if (function_exists('com_create_guid') === true)
        return trim(com_create_guid(), '{}');

    $data = openssl_random_pseudo_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}


?>
