<?php
function linkGame($addr, $text)
{
	return "<a href=$addr>$text</a>";
}

function drowGameTable($stmt) 
{
	echo <<<EOT
<table>
	<tr>
		<th>BGG Rank</th>
		<th>Title</th>
		<th>BGG Rating</th>
		<th>Your Rating</th>
	</tr>
EOT;
	$i = 1;
	while($row = oci_fetch_array($stmt, OCI_BOTH))
	{
		$link = "game.php?id=".$row['ID'];
		echo "<tr>
			<td>".linkGame($link, $i)."</td>
			<td>".linkGame($link, $row['NAME'])."</td>
			<td>".linkGame($link, $row['BGGSCORE'])."</td>
			<td>".linkGame($link, isset($row['RATING']) ? $row['RATING'] : "-")."</td>
			</tr>";
		++$i;
	}
	echo "</table>";
}
?>
