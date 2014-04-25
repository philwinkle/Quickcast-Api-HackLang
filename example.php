<?hh // strict

require_once('Quickcast.php');

function test($quickcast) {
	//Example of using map on a collection
	$views = $quickcast->getCasts()->map($x ==> $x['views'])->toArray();

	//native reduce works with lambdas
	$totalViews = array_reduce($views, ($sum, $x) ==> $sum + $x);

	echo "Total Views: $totalViews" . PHP_EOL;

	foreach($quickcast->getCasts() as $cast){
	    echo $cast['intro'] . ' | Views: ' . $cast['views'] . PHP_EOL;
	    echo '------------------------' . PHP_EOL;
	}
}

$quickcast = new \Quickcast\Quickcast(new \Quickcast\Config());
test($quickcast);