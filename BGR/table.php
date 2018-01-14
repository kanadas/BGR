<?php
function linkGame($addr, $text)
{
	return "<a href=$addr>$text</a>";
}

function nvl($var, $val)
{
	if(isset($var)) return $var;
	return $val;
}

function drowTable($headers, $cols, $stmt, $file, $paramname, $paramcol) 
{
	echo "<table><tr>";
	echo "<th>#</th>";
	foreach($headers as $head) echo "<th>$head</th>";
	echo "</tr>";
	$i = 1;
	while($row = oci_fetch_array($stmt, OCI_BOTH))
	{
		$link = "$file?$paramname=".$row[$paramcol];
		echo "<tr>";
		echo "<td>".linkgame($link, $i)."</td>";
		foreach($cols as $col)
			echo "<td>".linkGame($link, nvl($row[strtoupper($col)], "-"))."</td>";
		++$i;
	}
	echo "</table>";
}
?>
