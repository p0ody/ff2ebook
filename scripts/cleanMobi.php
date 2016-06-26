<?php 

try
{
	$source = __DIR__ ."/../archive";
	if(is_dir($source) === true)
	{
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

		$count = 0;
        /** @var SplFileInfo $file */
		foreach($files as $file)
		{
			if ($file->getExtension() != "mobi")
				continue;
				
			unlink($file);
			$count++;
		}
		echo date("o-m-d G:i:s") .": Done!  $count files deleted.";
	}

}
catch (Exception $e)
{
	die($e->getMessage());
}