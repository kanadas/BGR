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
	foreach($headers as $head) echo "<th>$head</th>";
	echo "</tr>";
	while($row = oci_fetch_array($stmt, OCI_BOTH))
	{
		$link = "$file?$paramname=".$row[$paramcol];
		echo "<tr>";
		foreach($cols as $col)
			echo "<td>".linkGame($link, nvl($row[strtoupper($col)], "-"))."</td>";
	}
	echo "</table>";
}
?>
