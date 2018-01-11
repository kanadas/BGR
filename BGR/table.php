<?php
function linkGame($addr, $text)
{
	return "<a href=$addr>$text</a>";
}

function drowGameTable($rows) 
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
	foreach($rows as $row)
	{
		$link = "game.php?id=".$row['id'];
		echo "
		<tr>
			<td>".linkGame($link, $i)."</td>
			<td>".linkGame($link, $row['name'])."</td>
			<td>".linkGame($link, $row['BGGScore'])."</td>
			<td>".linkGame($link, $row['rating']."</td>
		</tr>";
	}
	echo "</table>";
}
?>
