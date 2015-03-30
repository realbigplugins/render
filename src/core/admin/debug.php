<?php
/**
 * Outputs basic debugging info for Render.
 *
 * @since {{VERSION}}
 */

echo "Render Debug Log File\n";
printf( "Generated on %s\n", date( 'D F dS, Y: H:i:s') );

echo "\nServer Info:\n";
printf( "PHP Version: %s\n", phpversion() );

$mysql = mysqli_connect('localhost', DB_USER, DB_PASSWORD);

if (mysqli_connect_errno()) {
	printf("Connection failed: %s\n", mysqli_connect_error());
} else {
	printf( "MySQL Version: %s\n", mysqli_get_server_info($mysql) );

}
mysqli_close($mysql);

echo "\nRender Info:\n";
printf( "Version: %s\n", RENDER_VERSION );