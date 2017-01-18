<?php 

define( "MUSICAL_PANCAKE", "musical-pancake" );

require( "_tools.php" );

# Check HTTP request method
if ( $_SERVER["REQUEST_METHOD"] != "POST" ) { die( "Wrong request method!" ); }

var_dump( $_POST );

# Get first key of POST data (happens when calling "hostname | curl -d @- ...")
reset( $_POST );
$postdata = key( $_POST );
var_dump( $postdata );

if ( ! $postdata ) { die(); }

# Establish database connection
$pdo = newDatabaseConnection()  or  die();

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



