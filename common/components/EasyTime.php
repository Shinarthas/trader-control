<?
namespace common\components;

class EasyTime
{
	public static function timeAgo($time)
	{
		$dif = (time() - $time);
		if($dif >= 60)
		{
			if($dif<3600)
			{
				$data = floor($dif/60);
				if($data == 1)
					return $data.' minute ago';
				else
					return $data.' minutes ago';
			}
			if($dif>=3600 AND $dif<(3600*24))
			{
				$data = floor($dif/3600);
				if($data == 1)
					return $data.' hour ago';
				else
					return $data.' hours ago';
			}
			else
			{
				$data = floor($dif/(3600*24));
				if($data == 1)
					return $data.' day ago';
				else
					return $data.' days ago';
			}
		}
		else
		{
			$data =	$dif;
			if($data == 1)
				return $data.' second ago';
			else
				return $data.' seconds ago';
		}
	}	
	
	public static function timeAgoShort($time, $future = false)
	{
		$dif = (time() - $time);
		if($future)
			$dif = ($time - time());
		if($dif >= 60)
		{
			if($dif<3600)
			{
				$data = floor($dif/60);
				return $data.' m';

			}
			if($dif>=3600 AND $dif<(3600*24))
			{
				$data = floor($dif/3600);
				return $data.' h';

			}
			else
			{
				$data = floor($dif/(3600*24));
				return $data.' d';

			}
		}
		else
			return $dif.' s';

	}
	
	public static function secondsToHours($time)
	{
		$hours = floor($time/3600);
		$minutes = floor(($time - ($hours*3600))/60);
		$seconds = $time- (floor($time/60)*60);
		
		if($seconds<10)
			$seconds = "0".$seconds;
			
		if($minutes<10)
			$minutes = "0".$minutes;
	
		return $hours.':'.$minutes.':'.$seconds;
	}
	
	public static function secondsToMinutes($time)
	{
		$minutes = floor($time/60);
		$seconds = $time - (floor($time/60)*60);
		
		if($seconds<10)
			$seconds = "0".$seconds;
	
		return $minutes.':'.$seconds;
	}
}


?>