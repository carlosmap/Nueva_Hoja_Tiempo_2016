<?
	$chr = '50';
	echo 'ASCII : '.chr($chr).'.<br />CHR : '.$chr.'<br />';
	$chr = 2;
	echo 'ORD : '.ord($chr).'<br />';
	switch( $chr ){
		case (ord($chr) == 50):
			echo '20';
		break;
	}
?>