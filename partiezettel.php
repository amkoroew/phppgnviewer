<?php
$move = ceil( ( $chess->_half_move - 1 ) / 2 ); 
$move_color = ( $chess->_half_move % 2 );
echo '<table border="1">';
	echo '<tr>';
		echo '<td colspan="3">Runde Nr. 7</td>';
		echo '<td colspan="3">Brett Nr. 6</td>';
		echo '<td colspan="3">Datum '.$chess->_info['EventDate'].'</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="3">Veranstaltung</td>';
		echo '<td colspan="6">'.$chess->_info['Event'].'</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td colspan="9">Wei&szlig; '.$chess->_info['White'].' Schwarz '.$chess->_info['Black'].'</td>';
	echo '</tr>';
	for( $i = 1; $i <= 20; ++$i ) {
		echo '<tr>';
			echo '<td>'.$i.'.</td>';
			if( $move == $i and $move_color == 0 ) {
				echo '<td bgcolor="#0000FF">';
			} else {
				echo '<td>';
			}
			if( isset( $chess->_moves[$i][0] ) ) {
				echo '<a href="index2.php?move='.(($i*2)-1).'">'.$chess->_moves[$i][0].'</a>';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
			if( $move == $i and $move_color == 1 ) {
				echo '<td bgcolor="#0000FF">';
			} else {
				echo '<td bgcolor="#808080">';
			}
			if( isset( $chess->_moves[$i][1] ) ) {
				echo '<a href="index2.php?move='.($i*2).'">'.$chess->_moves[$i][1].'</a>';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
			echo '<td>'.($i+20).'.</td>';
			if( $move == $i+20 and $move_color == 0 ) {
				echo '<td bgcolor="#0000FF">';
			} else {
				echo '<td>';
			}
			if( isset( $chess->_moves[$i+20][0] ) ) {
				echo '<a href="index2.php?move='.((($i+20)*2)-1).'">'.$chess->_moves[$i+20][0].'</a>';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
			if( $move == $i+20 and $move_color == 1 ) {
				echo '<td bgcolor="#0000FF">';
			} else {
				echo '<td bgcolor="#808080">';
			}
			if( isset( $chess->_moves[$i+20][1] ) ) {
				echo '<a href="index2.php?move='.(($i+20)*2).'">'.$chess->_moves[$i+20][1].'</a>';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
			echo '<td>'.($i+40).'.</td>';
			if( $move == $i+40 and $move_color == 0 ) {
				echo '<td bgcolor="#0000FF">';
			} else {
				echo '<td>';
			}
			if( isset( $chess->_moves[$i+40][0] ) ) {
				echo '<a href="index2.php?move='.((($i+40)*2)-1).'">'.$chess->_moves[$i+40][0].'</a>';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
			if( $move == $i+40 and $move_color == 1 ) {
				echo '<td bgcolor="#0000FF">';
			} else {
				echo '<td bgcolor="#808080">';
			}
			if( isset( $chess->_moves[$i+40][1] ) ) {
				echo '<a href="index2.php?move='.(($i+40)*2).'">'.$chess->_moves[$i+40][1].'</a>';
			} else {
				echo '&nbsp;';
			}
			echo '</td>';
		echo '<tr>';
	}
echo '</table>';
