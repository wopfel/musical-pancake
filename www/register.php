<?php 

var_dump( $_POST );

# Get first key of POST data (happens when calling "hostname | curl -d @- ...")
reset( $_POST );
$postdata = key( $_POST );
var_dump( $postdata );

if ( ! $postdata ) { die(); }

# Establish database connection
$pdo = new PDO( 'mysql:host=localhost;dbname=musical-pancake', 'musical-pancake', 'aEfV7I5n0tJfgCZ0' )  or  die();

# Get database row
$stmt = $pdo->prepare( "SELECT * FROM systems WHERE dn = ?" );
if ( ! $stmt->execute( array( $_SERVER['DN'] ) ) ) { die(); }
$data = $stmt->fetch( PDO::FETCH_ASSOC );

# There's no row with that DN so far
if ( ! $data ) {

    # Create row
    $stmt = $pdo->prepare( "INSERT INTO systems (id, guid, dn, servername, created) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)" );
    $stmt->execute( array( "", guidv4(), $_SERVER['DN'], $postdata ) );

    # Get database row
    $stmt = $pdo->prepare( "SELECT * FROM systems WHERE dn = ?" );
    if ( ! $stmt->execute( array( $_SERVER['DN'] ) ) ) { die(); }
    $data = $stmt->fetch( PDO::FETCH_ASSOC );

}

var_dump( $data );



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

