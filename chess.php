<?php

class Chess {
	var $_info;
	var $_moves;
	var $_pieces;
	var $_fen_file;
	var $_pgn_file;
	var $_half_move = 1;
	var $_total_half_moves;
	static $_ep_field;
	function move() {
		if( $this->_half_move > $this->_total_half_moves ) {
			return false;
		}

		foreach( $this->_pieces as $chess_piece => $piece_arr ) {
			foreach( $piece_arr as $piece ) {
					$piece->setMovePossibilities();
			}
		}

		//Chess::$_ep_field = null;

		$curr_move = $this->_moves[( ( $this->_half_move - 1 ) / 2 ) + 1][( $this->_half_move - 1 ) % 2];
		$last_move = $this->_moves[( ( $this->_half_move - 2 ) / 2 ) + 1][( $this->_half_move - 2 ) % 2];
		
		if( $this->_half_move % 2 ) {
			$color = 'w';
			$opp_color = 'b';
		} else {
			$color = 'b';
			$opp_color = 'w';
		}

		$curr_move = str_replace( '+', '', $curr_move );
		$curr_move = str_replace( '#', '', $curr_move );

		$promotion = null;
		if( strpos( $curr_move, '=' ) ) {
			$promotion = substr( $curr_move, -2 );
			$curr_move = str_replace( $promotion, '', $curr_move );
		}

		if( strpos( $curr_move, '-' ) ) {
			if( strpos( $curr_move, '-' ) == strrpos( $curr_move, '-' ) ) {
			//kleine Rochade
				if( $color == 'w' ) {
					$_SESSION['from'] = 'e1';
					$_SESSION['to'] = 'g1';
					ChessBoard::unsetTakenField( 'e1' );
					ChessBoard::unsetTakenField( 'h1' );
					$this->_pieces['K'][0]->_field = 'g1';
					foreach($this->_pieces['R'] as $key => $rook ) {
						if( $rook->_field == 'h1' ) {
							$this->_pieces['R'][$key]->_field = 'f1';

						}
					}
				} else {
					$_SESSION['from'] = 'e8';
					$_SESSION['to'] = 'g8';
					ChessBoard::unsetTakenField( 'e8' );
					ChessBoard::unsetTakenField( 'h8' );
					$this->_pieces['k'][0]->_field = 'g8';
					foreach($this->_pieces['r'] as $key => $rook ) {
						if( $rook->_field == 'h8' ) {
							$this->_pieces['r'][$key]->_field = 'f8';
						}
					}

				}
			} else {
			//große Rochade
				if( $color == 'w' ) {
					$_SESSION['from'] = 'e1';
					$_SESSION['to'] = 'c1'; 
					ChessBoard::unsetTakenField( 'e1' );
					ChessBoard::unsetTakenField( 'a1' );
					$this->_pieces['K'][0]->_field = 'c1';
					foreach($this->_pieces['R'] as $key => $rook ) {
						if( $rook->_field == 'a1' ) {
							$this->_pieces['R'][$key]->_field = 'e1';
						}
					}

				} else {
					$_SESSION['from'] = 'e8';
					$_SESSION['to'] = 'c8';
					ChessBoard::unsetTakenField( 'e8' );
					ChessBoard::unsetTakenField( 'a8' );
					$this->_pieces['k'][0]->_field = 'c8';
					foreach($this->_pieces['r'] as $key => $rook ) {
						if( $rook->_field == 'a8' ) {
							$this->_pieces['r'][$key]->_field = 'd8';
						}
					}

				}
			}
		} else {
			$aim_to = substr( $curr_move, -2 );
			foreach( $this->_pieces as $key => $piece_type ) {
				foreach( $piece_type as $pieces => $piece ) {
					if( $aim_to == $piece->_field ) {
						unset( $this->_pieces[$key][$pieces] );
					}
					ChessBoard::setTakenField( $aim_to, $piece->_color );
				}
			}
			
			if( ord( substr( $curr_move, 0, 1 ) ) >= ord( 'a' ) and ord( substr( $curr_move, 0, 1 ) ) <= ord( 'z' ) ) {
				$ep_col = substr( $aim_to, 0, 1 );
				$ep_row = substr( $aim_to, 1, 1 );
				if( $color == 'w' ) {
					if( $ep_row == '4' and !( ChessBoard::getTakenField( $ep_col.'3' ) ) ) {
						Chess::$_ep_field = $ep_col.'3';	
					}
				} else {
					if( $ep_row == '5' and !( ChessBoard::getTakenField( $ep_col.'6' ) ) ) {
						Chess::$_ep_field = $ep_col.'6';	
					}
				}
				$curr_move = 'P'.$curr_move;
			}
			if( $this->_half_move % 2 ) {
				$piece_type = substr( $curr_move, 0, 1 );
			} else {
				$piece_type = strtolower( substr( $curr_move, 0, 1 ) );
			}
			foreach( $this->_pieces[$piece_type] as $piece ) {
				if( in_array( $aim_to, $piece->getMovePossibilities() ) ) {
					$tmp_piece[] = $piece;
				}
			}
			if( count( $tmp_piece ) == 1 ) {
				$_SESSION['from'] = $tmp_piece[0]->_field;
				ChessBoard::unsetTakenField( $tmp_piece[0]->_field );
				$tmp_piece[0]->_field = $aim_to;
				$_SESSION['to'] = $tmp_piece[0]->_field;
				ChessBoard::setTakenField( $tmp_piece[0]->_field, $tmp_piece[0]->_color );
				//$piece->setAttackedFields();
				if( strtolower( substr( $curr_move, 0, 1 ) ) == 'p' and $aim_to == Chess::$_ep_field ) {
					if( substr( $aim_to, 1, 1 ) == 3 ) {
						foreach( $this->_pieces['P'] as $pieces => $piece ) {
							if( $piece->_field == ( substr( $aim_to, 0, 1 ).'4' ) ) {
								unset( $this->_pieces['P'][$pieces] );
							}
						}
						ChessBoard::unsetTakenField( substr( $aim_to, 0, 1 ).'4' );
					} else {
						foreach( $this->_pieces['p'] as $pieces => $piece ) {
							if( $piece->_field == ( substr( $aim_to, 0, 1 ).'5' ) ) {
								unset( $this->_pieces['p'][$pieces] );
							}
						}
						ChessBoard::unsetTakenField( substr( $aim_to, 0, 1 ).'5' );
					}
				}
				$tmp_piece[0]->setMovePossibilities();
			} else if( count( $tmp_piece ) > 1 ) {
				$identifier = substr( $curr_move, 1, 1 );
				foreach( $tmp_piece as $key => $tmp2_piece ) {
					if( $tmp_piece[$key]->getCol() == $identifier ) {
						$_SESSION['from'] = $tmp_piece[$key]->_field;
						ChessBoard::unsetTakenField( $tmp_piece[$key]->_field );
						$tmp_piece[$key]->_field = $aim_to;
						$_SESSION['to'] = $tmp_piece[$key]->_field;
						ChessBoard::setTakenField( $tmp_piece[$key]->_field, $tmp_piece[$key]->_color );
						//$piece->setAttackedFields();
						$tmp_piece[$key]->setMovePossibilities();
					}
				}
			}
			if( $promotion ) {
				foreach( $this->_pieces['P'] as $pieces => $piece ) {
					if( $piece->_field == $aim_to) {
						unset( $this->_pieces['P'][$pieces] );
					}
				}	
				foreach( $this->_pieces['p'] as $pieces => $piece ) {
					if( $piece->_field == $aim_to) {
						unset( $this->_pieces['p'][$pieces] );
					}
				}	
				switch( $promotion ) {
					case '=Q':
						$this->_pieces[(($color == 'w')?'Q':'q')][] = new ChessPieceQueen( $color, $aim_to);
						ChessBoard::setTakenField( $aim_to, $color );
					break;
					case '=R':
						$this->_pieces[(($color == 'w')?'R':'r')][] = new ChessPieceRook( $color, $aim_to );
						ChessBoard::setTakenField( $aim_to, $color );
					break;
					case '=B':
						$this->_pieces[(($color == 'w')?'B':'b')][] = new ChessPieceBishop( $color, $aim_to );
						ChessBoard::setTakenField( $aim_to, $color );
					break;
					case '=N':
						$this->_pieces[(($color == 'w')?'N':'n')][] = new ChessPieceKnight( $color, $aim_to );
						ChessBoard::setTakenField( $aim_to, $color );
					break;
				}
			}

		}
		//echo ChessBoard::getTakenField( 'a1' );
		//echo ChessBoard::getTakenField( 'e8' );
		//echo ChessBoard::getTakenField( 'e2' );
		//echo ChessBoard::getTakenField( 'a8' );
		++$this->_half_move;
		return true;
	}
	function readFEN( $filename = null ) {
		if ( !$filename ) {
			$filename = 'start.fen';
		}
		$this->_fen_file = $filename;
		$fen = file_get_contents( $this->_fen_file );
		list( $piece_placement, $active_color, $castling, $en_passent, $halfmove_clock, $fullmove ) = explode(' ', $fen);
		$trans = array(
			'8' => '        ',
			'7' => '       ',
			'6' => '      ',
			'5' => '     ',
			'4' => '    ',
			'3' => '   ',
			'2' => '  ',
			'1' => ' '
		);
		$int_char = array(
			'0' => 'a',
			'1' => 'b',
			'2' => 'c',
			'3' => 'd',
			'4' => 'e',
			'5' => 'f',
			'6' => 'g',
			'7' => 'h'
		);
		$piece_placement = strtr( $piece_placement, $trans );
		$lines = explode( '/', $piece_placement );
		$line_num = 9;
		foreach( $lines as $line ) {
			preg_match_all( '/\S/', $line, $matches, PREG_OFFSET_CAPTURE );
			--$line_num;
			foreach( $matches[0] as $fields ) {
				$field = strtr( $fields[1], $int_char ).$line_num;
				if( strtoupper( $fields[0] ) == $fields[0] ) {
					$color = 'w';
				} else {
					$color = 'b';
				}
				switch( $fields[0] ) {
					case 'P':
					case 'p':
						$this->_pieces[$fields[0]][] = new ChessPiecePawn( $color, $field );
					break;
					case 'R':
					case 'r':
						$this->_pieces[$fields[0]][] = new ChessPieceRook( $color, $field );
					break;
					case 'N':
					case 'n':
						$this->_pieces[$fields[0]][] = new ChessPieceKnight( $color, $field );
					break;
					case 'B':
					case 'b':
						$this->_pieces[$fields[0]][] = new ChessPieceBishop( $color, $field );
					break;
					case 'K':
					case 'k':
						$this->_pieces[$fields[0]][] = new ChessPieceKing( $color, $field );
					break;
					case 'Q':
					case 'q':
						$this->_pieces[$fields[0]][] = new ChessPieceQueen( $color, $field );
					break;
				}
			}
		}
	}

	function readPGN( $filename ) {
		$pgn = file_get_contents( $filename );
		$delim = strrpos( $pgn, ']' );
		$info_str = substr( $pgn, 0, $delim +1 );
		$move_str = substr( $pgn, $delim + 1 );
		preg_match_all( '/\[[^\]]*\]/', $info_str, $info_arr );
		foreach( $info_arr[0] as $tmp_info ) {
			$info_id = substr( $tmp_info, 1, strpos( $tmp_info, ' ' ) - 1 );
			preg_match( '/"[^"]*"/', $tmp_info, $info[$info_id] );
			$info[$info_id] = $info[$info_id][0];
			$info[$info_id] = str_replace( '"', '', $info[$info_id] );
		}
		$this->_info = $info;
		$results = array(
			'1-0' => '',
			'0-1' => '',
			'1/2-1/2' => '',
			'*' => ''
		);
		//$move_str = strtr( $move_str, $results );
		preg_match_all( '/\d+.\s?\S+\s\S+/', $move_str, $move_arr );
		foreach( $move_arr[0] as $tmp_move ) {
			$move_no = substr( $tmp_move, 0, strpos( $tmp_move, '.' ) );
			preg_match( '/[a-zA-Z].+\s\S+/', $tmp_move, $move[$move_no] );
			$move[$move_no] = explode( ' ', $move[$move_no][0] );
		}
		$this->_moves = $move;
		if( in_array( $this->_moves[$move_no][0], $results ) ) {
			$this->_total_half_moves = ( $move_no - 1 ) * 2;
		} else {
			$this->_total_half_moves = ( $move_no * 2 ) - 1;
		}
	}
}

class ChessBoard extends Chess {
	public static $_taken_fields = array();
	//public static $_attacked_fields = array();
	public static function setTakenField( $field, $color ) {
		self::$_taken_fields[$field] = $color;
	}
	public static function unsetTakenField( $field ) {
		unset( self::$_taken_fields[$field] );
	}
	public static function getTakenField( $field ) {
		if( isset( self::$_taken_fields[$field] ) ) {
			return self::$_taken_fields[$field];
		} else {
			return false;
		}
	}
}

class ChessPiece extends ChessBoard {
	var $_color;
	var $_opponent_color;
	var $_field;
	var $_possible_moves;
	function __construct( $color, $field ) {
		$this->_color = $color;
		if( $this->_color == 'w' ) {
			$this->_opponent_color = 'b';
		} else {
			$this->_opponent_color = 'w';
		}
		$this->_field = $field;
		ChessBoard::setTakenField( $this->_field, $this->_color );
	}
	function setAttackedFields() {
	}
	function setMovePossibilities() {
	}
	function getMovePossibilities() {
		return $this->_possible_moves;
	}
	function getField() {
		return $this->_field;
	}
	function getCol() {
		return substr( $this->getField(), 0, 1 );
	}
	function getRow() {
		return ( int ) substr( $this->getField(), 1, 1 );
	}
	function getNewCol( $col, $offset ) {
		$cols = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h' );
		$col_pos = array_search( $col, $cols );
		$newCol = $col_pos + $offset;
		if( $newCol >= 0 and $newCol <= 7 ) {
			return $cols[$newCol];
		} else {
			return false;
		}
	}
	function getNewRow( $row, $offset ) {
		$newRow = $row + $offset;
		if( $newRow > 0 and $newRow < 9 ) {
			return $newRow;
		} else {
			return false;
		}
	}
}

class ChessPieceKing extends ChessPiece {
	function __construct( $color, $field ) {
		parent::__construct( $color, $field );
		//$this->setAttackedFields();
	}
	function setAttackedFields() {

		$col = $this->getCol();
		$row = $this->getRow();

		$offsets = array(
			array( 'col' => 0, 'row' => 1 ),
			array( 'col' => 0, 'row' => -1 ),
			array( 'col' => -1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 1 ),
			array( 'col' => 1, 'row' => -1 ),
			array( 'col' => -1, 'row' => 1 ),
			array( 'col' => -1, 'row' => -1 )
		);
		
		foreach( $offsets as $offset ) {
			$tmp_col_offset = $offset['col'];
			$tmp_row_offset = $offset['row'];
			$newCol = $this->getNewCol( $col, $tmp_col_offset );
			$newRow = $this->getNewRow( $row, $tmp_row_offset );
			if( $newCol and $newRow ) {
				$tmp_field = $newCol.$newRow;
			//	self::$_attacked_fields[$tmp_field] = $this->_color;
			}
		}
	}
	function setMovePossibilities() {
		$fields = array();

		$col = $this->getCol();
		$row = $this->getRow();

		$offsets = array(
			array( 'col' => 0, 'row' => 1 ),
			array( 'col' => 0, 'row' => -1 ),
			array( 'col' => -1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 1 ),
			array( 'col' => 1, 'row' => -1 ),
			array( 'col' => -1, 'row' => 1 ),
			array( 'col' => -1, 'row' => -1 )
		);
		
		foreach( $offsets as $offset ) {
			$tmp_col_offset = $offset['col'];
			$tmp_row_offset = $offset['row'];
			$newCol = $this->getNewCol( $col, $tmp_col_offset );
			$newRow = $this->getNewRow( $row, $tmp_row_offset );
			if( $newCol and $newRow ) {
				$tmp_field = $newCol.$newRow;
				//echo $this->getField().'=>'.$tmp_field.'<br />';
				if( ChessBoard::getTakenField( $tmp_field ) == $this->_color ) {
					continue;
				}
				$fields[] = $tmp_field;
				//self::$_attacked_fields[$this->_color][] = $tmp_field;
			}
		}
		$this->_possible_moves = $fields;
	}
}

class ChessPieceQueen extends ChessPiece {
	function __construct( $color, $field ) {
		parent::__construct( $color, $field );
		//$this->setAttackedFields();
	}
	function setAttackedFields() {

		$col = $this->getCol();
		$row = $this->getRow();

		$offsets = array(
			array( 'col' => 0, 'row' => 1 ),
			array( 'col' => 0, 'row' => -1 ),
			array( 'col' => -1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 1 ),
			array( 'col' => 1, 'row' => -1 ),
			array( 'col' => -1, 'row' => 1 ),
			array( 'col' => -1, 'row' => -1 )
		);
		
		foreach( $offsets as $offset ) {
			$i = 1;
			while( 1 ) {
				$tmp_col_offset = $offset['col'] * $i;
				$tmp_row_offset = $offset['row'] * $i;
				$newCol = $this->getNewCol( $col, $tmp_col_offset );
				$newRow = $this->getNewRow( $row, $tmp_row_offset );
				if( $newCol and $newRow ) {
					$tmp_field = $newCol.$newRow;
					//echo $this->getField().'=>'.$tmp_field.'<br />';
					//self::$_attacked_fields[$tmp_field] = $this->_color;
					if( ChessBoard::getTakenField( $tmp_field ) ) {
						break;
					}
				} else {
					break;
				}
				++$i;
			}
		}
	}
	function setMovePossibilities() {
		$fields = array();

		$col = $this->getCol();
		$row = $this->getRow();

		$offsets = array(
			array( 'col' => 0, 'row' => 1 ),
			array( 'col' => 0, 'row' => -1 ),
			array( 'col' => -1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 1 ),
			array( 'col' => 1, 'row' => -1 ),
			array( 'col' => -1, 'row' => 1 ),
			array( 'col' => -1, 'row' => -1 )
		);
		
		foreach( $offsets as $offset ) {
			$i = 1;
			while( 1 ) {
				$tmp_col_offset = $offset['col'] * $i;
				$tmp_row_offset = $offset['row'] * $i;
				$newCol = $this->getNewCol( $col, $tmp_col_offset );
				$newRow = $this->getNewRow( $row, $tmp_row_offset );
				if( $newCol and $newRow ) {
					$tmp_field = $newCol.$newRow;
					//echo $this->getField().'=>'.$tmp_field.'<br />';
					if( ChessBoard::getTakenField( $tmp_field ) == $this->_color ) {
						break;
					}
					$fields[] = $tmp_field;
					//self::$_attacked_fields[$this->_color][] = $tmp_field;
					if( ChessBoard::getTakenField( $tmp_field ) == $this->_opponent_color ) {
						break;
					}
				} else {
					break;
				}
				++$i;
			}
		}
		$this->_possible_moves = $fields;
	}
}

class ChessPieceRook extends ChessPiece {
	function __construct( $color, $field ) {
		parent::__construct( $color, $field );
		//$this->setAttackedFields();
	}
	function setAttackedFields() {

		$col = $this->getCol();
		$row = $this->getRow();

		$offsets = array(
			array( 'col' => 0, 'row' => 1 ),
			array( 'col' => 0, 'row' => -1 ),
			array( 'col' => -1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 0 )
		);
		
		foreach( $offsets as $offset ) {
			$i = 1;
			while( 1 ) {
				$tmp_col_offset = $offset['col'] * $i;
				$tmp_row_offset = $offset['row'] * $i;
				$newCol = $this->getNewCol( $col, $tmp_col_offset );
				$newRow = $this->getNewRow( $row, $tmp_row_offset );
				if( $newCol and $newRow ) {
					$tmp_field = $newCol.$newRow;
					//echo $this->getField().'=>'.$tmp_field.'<br />';
					//self::$_attacked_fields[$tmp_field] = $this->_color;
					if( ChessBoard::getTakenField( $tmp_field ) ) {
						break;
					}
				} else {
					break;
				}
				++$i;
			}
		}
	}
	function setMovePossibilities() {
		$fields = array();

		$col = $this->getCol();
		$row = $this->getRow();

		$offsets = array(
			array( 'col' => 0, 'row' => 1 ),
			array( 'col' => 0, 'row' => -1 ),
			array( 'col' => -1, 'row' => 0 ),
			array( 'col' => 1, 'row' => 0 )
		);
		
		foreach( $offsets as $offset ) {
			$i = 1;
			while( 1 ) {
				$tmp_col_offset = $offset['col'] * $i;
				$tmp_row_offset = $offset['row'] * $i;
				$newCol = $this->getNewCol( $col, $tmp_col_offset );
				$newRow = $this->getNewRow( $row, $tmp_row_offset );
				if( $newCol and $newRow ) {
					$tmp_field = $newCol.$newRow;
					//echo $this->getField().'=>'.$tmp_field.'<br />';
					if( ChessBoard::getTakenField( $tmp_field ) == $this->_color ) {
						break;
					}
					$fields[] = $tmp_field;
					//self::$_attacked_fields[$this->_color][] = $tmp_field;
					if( ChessBoard::getTakenField( $tmp_field ) == $this->_opponent_color ) {
						break;
					}
				} else {
					break;
				}
				++$i;
			}
		}
		$this->_possible_moves = $fields;
	}
}

class ChessPieceBishop extends ChessPiece {
	function __construct( $color, $field ) {
		parent::__construct( $color, $field );
		//$this->setAttackedFields();
	}
	function setAttackedFields() {

		$col = $this->getCol();
		$row = $this->getRow();

		$offsets = array(
			array( 'col' => 1, 'row' => 1 ),
			array( 'col' => 1, 'row' => -1 ),
			array( 'col' => -1, 'row' => 1 ),
			array( 'col' => -1, 'row' => -1 )
		);
		
		foreach( $offsets as $offset ) {
			$i = 1;
			while( 1 ) {
				$tmp_col_offset = $offset['col'] * $i;
				$tmp_row_offset = $offset['row'] * $i;
				$newCol = $this->getNewCol( $col, $tmp_col_offset );
				$newRow = $this->getNewRow( $row, $tmp_row_offset );
				if( $newCol and $newRow ) {
					$tmp_field = $newCol.$newRow;
					//echo $this->getField().'=>'.$tmp_field.'<br />';
					//self::$_attacked_fields[$this->_color][] = $tmp_field;
					if( ChessBoard::getTakenField( $tmp_field ) ) {
						break;
					}
				} else {
					break;
				}
				++$i;
			}
		}
	}
	function setMovePossibilities() {
		$fields = array();

		$col = $this->getCol();
		$row = $this->getRow();

		$offsets = array(
			array( 'col' => 1, 'row' => 1 ),
			array( 'col' => 1, 'row' => -1 ),
			array( 'col' => -1, 'row' => 1 ),
			array( 'col' => -1, 'row' => -1 )
		);
		
		foreach( $offsets as $offset ) {
			$i = 1;
			while( 1 ) {
				$tmp_col_offset = $offset['col'] * $i;
				$tmp_row_offset = $offset['row'] * $i;
				$newCol = $this->getNewCol( $col, $tmp_col_offset );
				$newRow = $this->getNewRow( $row, $tmp_row_offset );
				if( $newCol and $newRow ) {
					$tmp_field = $newCol.$newRow;
					//echo $this->getField().'=>'.$tmp_field.'<br />';
					if( ChessBoard::getTakenField( $tmp_field ) == $this->_color ) {
						break;
					}
					$fields[] = $tmp_field;
					//self::$_attacked_fields[$this->_color][] = $tmp_field;
					if( ChessBoard::getTakenField( $tmp_field ) == $this->_opponent_color ) {
						break;
					}
				} else {
					break;
				}
				++$i;
			}
		}
		$this->_possible_moves = $fields;
	}
}

class ChessPieceKnight extends ChessPiece {
	function __construct( $color, $field ) {
		parent::__construct( $color, $field );
		//$this->setAttackedFields();
	}
	function setAttackedFields() {

		$col = $this->getCol();
		$row = $this->getRow();
		
		$offsets = array(
			array( 'col' => 2, 'row' => 1 ),
			array( 'col' => 2, 'row' => -1 ),
			array( 'col' => -2, 'row' => 1 ),
			array( 'col' => -2, 'row' => -1 ),
			array( 'col' => 1, 'row' => 2 ),
			array( 'col' => 1, 'row' => -2 ),
			array( 'col' => -1, 'row' => 2 ),
			array( 'col' => -1, 'row' => -2 )
		);

		foreach( $offsets as $offset ) {
			$newCol = $this->getNewCol( $col, $offset['col'] );
			$newRow = $this->getNewRow( $row, $offset['row'] );
			if( $newCol and $newRow ) {
				$tmp_field = $newCol.$newRow;
				//self::$_attacked_fields[$this->_color][] = $tmp_field;
			}
		}
	}
	function setMovePossibilities() {
		$fields = array();

		$col = $this->getCol();
		$row = $this->getRow();
		
		$offsets = array(
			array( 'col' => 2, 'row' => 1 ),
			array( 'col' => 2, 'row' => -1 ),
			array( 'col' => -2, 'row' => 1 ),
			array( 'col' => -2, 'row' => -1 ),
			array( 'col' => 1, 'row' => 2 ),
			array( 'col' => 1, 'row' => -2 ),
			array( 'col' => -1, 'row' => 2 ),
			array( 'col' => -1, 'row' => -2 )
		);

		foreach( $offsets as $offset ) {
			$newCol = $this->getNewCol( $col, $offset['col'] );
			$newRow = $this->getNewRow( $row, $offset['row'] );
			if( $newCol and $newRow ) {
				$tmp_field = $newCol.$newRow;
				if( ChessBoard::getTakenField( $tmp_field ) == $this->_color ) {
					continue;
				}
				$fields[] = $tmp_field;
				//self::$_attacked_fields[$this->_color][] = $tmp_field;
			}
		}
		$this->_possible_moves = $fields;
	}
}

class ChessPiecePawn extends ChessPiece {
	function __construct( $color, $field ) {
		parent::__construct( $color, $field );
		//$this->setAttackedFields();
	}
	function setAttackedFields() {

		$col = $this->getCol();
		$row = $this->getRow();

		if( $this->_color == 'w' ) {
			$newCol = $this->getNewCol( $col, -1 );
			$newRow = $this->getNewRow( $row, 1 );
			$tmp_field = $newCol.$newRow;
			//self::$_attacked_fields[$this->_color][] = $tmp_field;
			$newCol = $this->getNewCol( $col, 1 );
			$newRow = $this->getNewRow( $row, 1 );
			$tmp_field = $newCol.$newRow;
			//self::$_attacked_fields[$this->_color][] = $tmp_field;
		} else {
			$newCol = $this->getNewCol( $col, -1 );
			$newRow = $this->getNewRow( $row, -1 );
			$tmp_field = $newCol.$newRow;
			//self::$_attacked_fields[$this->_color][] = $tmp_field;
			$newCol = $this->getNewCol( $col, 1 );
			$newRow = $this->getNewRow( $row, -1 );
			$tmp_field = $newCol.$newRow;
			//self::$_attacked_fields[$this->_color][] = $tmp_field;
		}
	}
	function setMovePossibilities() {
		$fields = array();

		$col = $this->getCol();
		$row = $this->getRow();

		if( $this->_color == 'w' ) {
			$newCol = $this->getNewCol( $col, 0 );
			$newRow = $this->getNewRow( $row, 1 );
			$tmp_field = $newCol.$newRow;
			if( !( ChessBoard::getTakenField( $tmp_field ) ) ) {
				$fields[] = $tmp_field;
			}
			if( $row == 2 ){
				$newCol = $this->getNewCol( $col, 0 );
				$newRow = $this->getNewRow( $row, 2 );
				$tmp_field = $newCol.$newRow;
				if( ! ( ChessBoard::getTakenField( $tmp_field ) ) ) {
					$fields[] = $tmp_field;
				}
			}
			$newCol = $this->getNewCol( $col, 1 );
			$newRow = $this->getNewRow( $row, 1 );
			$tmp_field = $newCol.$newRow;
			if( ChessBoard::getTakenField( $tmp_field ) == $this->_opponent_color or $tmp_field == Chess::$_ep_field ) {
				$fields[] = $tmp_field;
			}
			$newCol = $this->getNewCol( $col, -1 );
			$newRow = $this->getNewRow( $row, 1 );
			$tmp_field = $newCol.$newRow;
			if( ChessBoard::getTakenField( $tmp_field ) == $this->_opponent_color or $tmp_field == Chess::$_ep_field ) {
				$fields[] = $tmp_field;
			}
		} else {
			$newCol = $this->getNewCol( $col, 0 );
			$newRow = $this->getNewRow( $row, -1 );
			$tmp_field = $newCol.$newRow;
			if( !( ChessBoard::getTakenField( $tmp_field ) ) ) {
				$fields[] = $tmp_field;
			}
			if( $row == 7 ){
				$newCol = $this->getNewCol( $col, 0 );
				$newRow = $this->getNewRow( $row, -2 );
				$tmp_field = $newCol.$newRow;
				if( !( ChessBoard::getTakenField( $tmp_field ) ) ) {
					$fields[] = $tmp_field;
				}
			}
			$newCol = $this->getNewCol( $col, 1 );
			$newRow = $this->getNewRow( $row, -1 );
			$tmp_field = $newCol.$newRow;
			if( ChessBoard::getTakenField( $tmp_field ) == $this->_opponent_color or $tmp_field == Chess::$_ep_field ) {
				$fields[] = $tmp_field;
			}
			$newCol = $this->getNewCol( $col, -1 );
			$newRow = $this->getNewRow( $row, -1 );
			$tmp_field = $newCol.$newRow;
			if( ChessBoard::getTakenField( $tmp_field ) == $this->_opponent_color or $tmp_field == Chess::$_ep_field ) {
				$fields[] = $tmp_field;
			}
		}
		$this->_possible_moves = $fields;
	}
}
