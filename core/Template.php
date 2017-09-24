<?php
namespace Codeman;
use Exception;

interface Core {
	public function build_project();
	public function show();
}	// end interface

class Template implements Core {
	private $config = null;
	private $structure = null;

	function __construct( array $data = null ) {

		if( is_null( $data ) )
			throw new Exception( 'You need specify the pages' );

		$this -> config = [
			'file'	=>	str_replace( '/', '', $_SERVER[ 'REQUEST_URI' ] ),
			'data'	=>	$data,
			'path'	=>	'structure/',
			'template'	=>	'template.php',
		];

		$this -> config[ 'file' ] = ( $this -> config[ 'file' ] === '' ) ? 'index' : $this -> config[ 'file' ];
	}	// end construct

	private function build( string $file = null ) {
		$page = null;

		if( ! file_exists( $this -> config[ 'path' ] ) )
			throw new \Exception( 'No such file or directory ' . $this -> config[ 'path' ] );

		$this -> config[ 'content' ] = $this -> config[ 'path' ] . 'content/' . ( ( is_null( $file ) ) ? $this -> config[ 'file' ] : $file ) . '.php';

		if(
			! isset( $this -> config[ 'data' ][ $this -> config[ 'file' ] ] ) ||
			! file_exists( $this -> config[ 'content' ] )
		)
			$page = file_get_contents( '404.php' );

		else {
			$this -> structure = [
				'browser-scripting'	=>	file_get_contents( $this -> config[ 'path' ] . 'browser-scripting.php' ),
				'content'			=>	file_get_contents( $this -> config[ 'content' ] ),
				'description'		=>	$this -> config[ 'data' ][ $this -> config[ 'file' ] ][ 'description' ],
				'footer'			=>	file_get_contents( $this -> config[ 'path' ] . 'footer.php' ),
				'head'				=>	file_get_contents( $this -> config[ 'path' ] . 'head.php' ),
				'header'			=>	file_get_contents( $this -> config[ 'path' ] . 'header.php' ),
				'navigation'		=>	file_get_contents( $this -> config[ 'path' ] . 'navigation.php' ),
				'title'				=>	$this -> config[ 'data' ][ $this -> config[ 'file' ] ][ 'title' ],
			];

			$page = file_get_contents( $this -> config[ 'template' ] );

			// TODO: Check &
			foreach( $this -> structure as $index => &$part )
				$page = str_replace( '{' . $index . '}', $part, $page );

			unset( $index, $part );
		}	// end else

		return ( is_null( $file ) ) ? $page : $this -> minified( $page );
	}	// end method

	private function minified( string $data ): string {
		$data = str_replace( "\n", '', $data );
		$data = str_replace( "\t", '', $data );
		return trim( $data );
	}	// end method

	public function build_project() {
		exec( 'sh core/structure.sh' );

		if( ! file_exists( 'build' ) )
			throw new Exception( 'No such directory' );

		foreach( $this -> config[ 'data' ] as $index => &$value )
			file_put_contents( sprintf( 'build/%s.html', $index ), $this -> build( $index ) );

		unset( $index, $value );

		exec( 'sh core/compress.sh' );
		echo 'Successfully built!!!!';
	}	// end method

	public function show() {
		echo $this -> build();
	}	// end method
}	// end class