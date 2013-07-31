<?php

include( 'chess.php' );

session_start();

$size= $_SESSION['bla'];
$chess = $_SESSION['blubb'];
$from = $_SESSION['from'];
$to = $_SESSION['to'];
ChessBoard::$_taken_fields = $_SESSION['static_taken_fields'];

$pieces_img = array(
	'P' => imagecreatefrompng( 'images/white_pawn.png' ),
	'R' => imagecreatefrompng( 'images/white_rook.png' ),
	'N' => imagecreatefrompng( 'images/white_knight.png' ),
	'B' => imagecreatefrompng( 'images/white_bishop.png' ),
	'K' => imagecreatefrompng( 'images/white_king.png' ),
	'Q' => imagecreatefrompng( 'images/white_queen.png' ),
	'p' => imagecreatefrompng( 'images/black_pawn.png' ),
	'r' => imagecreatefrompng( 'images/black_rook.png' ),
	'n' => imagecreatefrompng( 'images/black_knight.png' ),
	'b' => imagecreatefrompng( 'images/black_bishop.png' ),
	'k' => imagecreatefrompng( 'images/black_king.png' ),
	'q' => imagecreatefrompng( 'images/black_queen.png' )
);

$pieces_img_width = imagesx( $pieces_img['K'] );
$pieces_img_height = imagesy( $pieces_img['K'] );

$image = imagecreatetruecolor(9 * $size, 9 * $size);

$light = imagecolorallocate( $image, 244, 200, 89 );
$dark = imagecolorallocate( $image, 151, 79, 7 );
$from_color = imagecolorallocate( $image, 10, 120, 255 );
$to_color = imagecolorallocate( $image, 0, 60, 200 );
$black = imagecolorallocate( $image, 0, 0, 0 );
$transparent = imagecolorallocatealpha( $image, 255, 255, 255, 127 );

imagefill( $image, 0, 0, $light );

function getXpos( $size, $offset, $field ) {
	return ( ord( strtolower( substr( $field, 0, 1 ) ) ) - ord( 'a' ) ) * $size + $size / 2 + $offset;
}

function getYpos( $size, $offset, $field ) {
	return ( 8 - ( ( int ) substr( $field, 1, 1 ) ) ) * $size + $size / 2 + $offset;
}

/* dark fields
10,30,50,70
01,21,41,61
12,32,52,72
03,23,43,63
14,34,54,74
05,25,45,65
16,36,56,76
07,27,47,67
*/
for( $col = 0; $col < 8; ++$col ) {
	for( $row = 0; $row < 8; ++$row ) {
		$xpos = $row * $size;
		$ypos = $col * $size;
		if( $col % 2 ) {
			if( !( $row % 2 ) ) {
				imagefilledrectangle( $image, $xpos+($size/2), $ypos+($size/2), $xpos+$size+($size/2), $ypos+$size+($size/2), $dark );
			}
		} else {
			if( $row % 2 ) {
				imagefilledrectangle( $image, $xpos+($size/2), $ypos+($size/2), $xpos+$size+($size/2), $ypos+$size+($size/2), $dark );
			}
		}
	}
}

$xpos = getXpos( $size, 0, $from );
$ypos = getYpos( $size, 0, $from );
imagefilledrectangle( $image, $xpos, $ypos, $xpos + $size, $ypos + $size, $from_color );
$xpos = getXpos( $size, 0, $to );
$ypos = getYpos( $size, 0, $to );
imagefilledrectangle( $image, $xpos, $ypos, $xpos + $size, $ypos + $size, $to_color );

for( $i = 0; $i < 9; ++$i ) {
	imageline($image, $i*$size+($size/2), 0, $i*$size+($size/2), 9*$size, $black);
	imageline($image, 0, $i*$size+($size/2), 9*$size, $i*$size+($size/2), $black);
}

$fieldname = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H' );
$fontsize = 5;

$fontwidth = imagefontwidth( $fontsize );
$fontheight = imagefontheight( $fontsize );

for( $i = 1; $i <= 8; ++$i ) {
	imagechar($image, $fontsize, $i*$size, $size/4-$fontheight/2, $fieldname[$i-1], $black);
	imagechar($image, $fontsize, $i*$size, $size*9-$size/4-$fontheight/2, $fieldname[$i-1], $black);
	imagechar($image, $fontsize, $size/4-$fontwidth/2, $i*$size, 9-$i, $black);
	imagechar($image, $fontsize, $size*9-$size/4-$fontwidth/2, $i*$size, 9-$i, $black);
}

//offset calculate size difference of piece and field
$offset = floor( ( $size - $pieces_img_width ) / 2 );
$pieces = $chess->_pieces;
foreach( $pieces as $piece_type => $piece) {
	foreach( $piece as $piece_obj ) {
		//echo $piece_type.' auf '.$piece_obj->_field;
		//imagechar( $image, 5, $size, $size, $piece_type, $black );
		$field = $piece_obj->_field;
		$xpos = getXpos( $size, $offset, $field );
		$ypos = getYpos( $size, $offset, $field );
		imagecopyresampled ( $image, $pieces_img[$piece_type], $xpos, $ypos, 0, 0, $pieces_img_width, $pieces_img_width, $pieces_img_width, $pieces_img_height );
	}
}

//save the image as a png and output 
header( 'Content-Type: image/png' );
header( 'Content_Disposition: inline; filename=board.png' );

imagepng( $image );
 
//Clear up memory used
foreach( $pieces_img as $color ) {
	foreach( $color as $piece ) {
		imagedestroy( $piece );
	}
}
imagedestroy($image);
?>
