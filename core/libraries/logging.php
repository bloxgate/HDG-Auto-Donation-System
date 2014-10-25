<?php
// Yes, I made a whole file for this.
function WriteLog($line)
{
	if($config["logs"]["Is Used"])
	{
		if(!file_exists("../logs/log_".date($config['logs']['date']).".txt"))
		{
			fopen("../logs/log_".date($config['logs']['date']).".txt", 'w');
			fclose("../logs/log_".date($config['logs']['date']).".txt");
			file_put_contents("../logs/log_".date($config['logs']['date']).".txt", date($config['logs']['time']).": Log file created!");
		}
		$old = file_get_contents("../logs/log_".date($config['logs']['date']).".txt");
		$new = $old."\n".$line;
		file_put_contents("../logs/log_".date($config['logs']['date']).".txt", $new);
	}
}
?>