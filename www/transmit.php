<?php 

define( "MUSICAL_PANCAKE", "musical-pancake" );

require( "_tools.php" );

# Check HTTP request method
if ( $_SERVER["REQUEST_METHOD"] != "POST" ) { die( "Wrong request method!" ); }

var_dump( $_GET );

$guid = $_GET["guid"];

# Check GUID
if ( ! $guid ) { die(); }
if ( ! preg_match( "/^[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}$/", $guid ) ) { die(); }

var_dump( $guid );

# A list with tables (where the transmitted data is stored), references to field "type" in table "saved_data"
# Don't change the order in this array :-)
$data_tables_list = array( "installed_packages" );

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


if ( ! isset( $_FILES[ "inputfile" ] ) ) { die(); }
$filename = $_FILES[ "inputfile" ][ "tmp_name" ];
if ( ! file_exists( $filename ) ) { die(); }

$content = file_get_contents( $filename );

if ( $content === FALSE ) { die(); }

print $content;

$table_id = array_search( "installed_packages", $data_tables_list );
if ( $table_id === FALSE ) { die( "Table not found. Invalid name." ); }

# Create row
$stmt = $pdo->prepare( "INSERT INTO saved_data (id, systems_id, datetime, successful, type) VALUES (?, ?, CURRENT_TIMESTAMP, FALSE, ?)" );
$stmt->execute( array( "", $data['id'], $table_id ) );

$last_insert_id = $pdo->lastInsertId();


$content = file_get_contents( $filename );

if ( $content === FALSE ) { die(); }

print $content;

foreach ( explode( "\n", $content ) as $line ) {

    print ">>> $line \n";

    $name_and_version = explode( " ", $line );
    $pkg_name    = $name_and_version[0];
    $pkg_version = $name_and_version[1];

    $stmt = $pdo->prepare( "INSERT INTO installed_packages (id, saved_data_id, package_name, package_version) VALUES (?, ?, ?, ?)" );
    $stmt->execute( array( "", $last_insert_id, $pkg_name, $pkg_version ) );

}

# Update row
$stmt = $pdo->prepare( "UPDATE saved_data SET successful = TRUE where id = ?" );
$stmt->execute( array( $last_insert_id ) );

if ( $stmt->rowCount() != 1 ) { die(); }

