<?php 

define( "MUSICAL_PANCAKE", "musical-pancake" );

require( "_tools.php" );

var_dump( $_GET );

$guid = $_GET["guid"];

# Check GUID
if ( ! $guid ) { die(); }
if ( ! preg_match( "/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$/", $guid ) ) { die(); }

var_dump( $guid );

# Establish database connection
$pdo = newDatabaseConnection()  or  die();

# Get database row
$stmt = $pdo->prepare( "SELECT * FROM systems WHERE dn = ? AND guid = ?" );
if ( ! $stmt->execute( array( $_SERVER['DN'], $guid ) ) ) { die(); }
$data = $stmt->fetch( PDO::FETCH_ASSOC );

# There's no such row?
if ( ! $data ) { die(); }

# Check id field
if ( $data['id'] <= 0 ) { die(); }

# Update row
$stmt = $pdo->prepare( "UPDATE systems SET last_contact = CURRENT_TIMESTAMP where id = ?" );
$stmt->execute( array( $data['id'] ) );

if ( $stmt->rowCount() != 1 ) { die(); }


