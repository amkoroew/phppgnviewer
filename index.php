<?php

include( 'chess.php' );

session_start();

if( isset( $_GET['start'] ) ) {
	unset( $_SESSION['blubb'] );
}

if( isset( $_SESSION['blubb'] ) ) {
	$chess = $_SESSION['blubb'];
	ChessBoard::$_taken_fields = $_SESSION['static_taken_fields'];
	/*if( isset( $_GET['zurueck'] ) ) {
		$tmp_chess = new Chess();
		$tmp_chess->readFen();
		$tmp_chess->readPGN();
		foreach( $chess->_pieces as $chess_piece => $piece_arr ) {
			foreach( $piece_arr as $piece ) {
				$piece->setMovePossibilities();
			}
		}
		$tmp_chess->_total_half_moves = $_GET['zurueck'];
		while( $tmp_chess->move() ) {
			foreach( $tmp_chess->_pieces as $chess_piece => $piece_arr ) {
				foreach( $piece_arr as $piece ) {
					$piece->setMovePossibilities();
				}
			}
		}
		$chess = $tmp_chess;
	} else {*/
		if( $chess->move() ) {
			foreach( $chess->_pieces as $chess_piece => $piece_arr ) {
				foreach( $piece_arr as $piece ) {
						$piece->setMovePossibilities();
				}
			}
		} else {
			echo '<br />Ergebnis: '.$chess->_info['Result'];
		}
	//}
	
} else {
	$chess = new Chess();
	$chess->readFen();
	$chess->readPGN( 'mypgn.pgn' );
	foreach( $chess->_pieces as $chess_piece => $piece_arr ) {
		foreach( $piece_arr as $piece ) {
			$piece->setMovePossibilities();
		}
	}

	$_SESSION['bla'] = 45;
	$_SESSION['blubb'] = $chess;
}
$_SESSION['static_taken_fields'] = ChessBoard::$_taken_fields;
echo '<a href="index.php?start">Start</a> ';
if( $chess->_half_move > 1 ) {
	//echo '<a href="index.php?zurueck='.($chess->_half_move - 1).'">Zur&uuml;ck</a> ';
}
echo '<a href="index.php">Weiter</a>';
echo '<br />';
echo '<img src="board.php" alt="" />';
echo '<br />';
include( 'partiezettel.php' );
