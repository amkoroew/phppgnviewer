<?php
	include( 'chess.php' );

	session_start();

	$move = isset( $_GET['move'] ) ? $_GET['move'] : 0 ;

	$chess = new Chess();
	$chess->readFen();
	$chess->readPGN( 'mypgn2.pgn' );

	$total_moves = $chess->_total_half_moves;
	$chess->_total_half_moves = ( $move > $chess->_total_half_moves ) ? $chess->_total_half_moves : $move ;
	
	while( $chess->move() );

	$_SESSION['bla'] = 50;
	$_SESSION['blubb'] = $chess;

	echo $chess->_ep_field;

	echo '<a href="index2.php?move=0">Start</a> ';
	if( $chess->_half_move > 1 ) {
		echo '<a href="index2.php?move='.($move-1).'">Zur&uuml;ck</a> ';
	} else {
		echo 'Zur&uuml;ck ';
	}
	if( $chess->_half_move <= $total_moves ) {
		echo '<a href="index2.php?move='.($move+1).'">Weiter</a><br />';
	} else {
		echo 'Weiter<br />';
	}
	echo '<img src="board.php" alt="" /><br />';
	include( 'partiezettel.php' );
