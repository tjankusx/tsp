<?php

// set up array of points for polygon
$nodes = [];
$nodesCount = 0;
$imageWidth = 808;
$imageHeight = 808;

$originalOrderNodes = [];

class TspNode {
    public $x;
    public $y;
    public $distanceTo;

}


$toImport = [
    [0, 0],
    [100, 0],
    [100, 100],
    [0, 100],
//    [25, 100],
    [50, 100],
    [50, 0],
    [45, 50],
    [55, 50],
];

$toImport = [
    [30, 30],
    [65, 20],
    [70, 70],
    [30, 70  l,
//    [55, 35],
//    [25, 100],
//    [50, 100],
//    [50, 0],
//    [45, 50],
//    [55, 50],
];

//shuffle($toImport);

$x  = array_column($toImport, 0);
$y = array_column($toImport, 1);

// Sort the data with volume descending, edition ascending
// Add $toImport as the last parameter, to sort by the common key
//array_multisort($y, SORT_DESC, $x, SORT_DESC, $toImport);


function addNode($w, $h) {
    global $nodes, $nodesCount, $originalOrderNodes;

    $tspNode = new TspNode();
    $tspNode->x = $w;
    $tspNode->y = $h;
    $tspNode->distanceTo = 0;


    $debugNode = new TspNode();
    $debugNode->x = $tspNode->x;
    $debugNode->y = $tspNode->y;
    $originalOrderNodes[] = $debugNode;

    if ($nodesCount == 0) {
        $nodes[] = $tspNode;
    } elseif ($nodesCount == 1) {

        $tspNode->distanceTo = sqrt(pow($tspNode->x - $nodes[0]->x, 2) + (pow($tspNode->y - $nodes[0]->y, 2)));

        $nodes[0]->distanceTo = $tspNode->distanceTo;
        $nodes[] = $tspNode;
    } else {
        $minLoss = PHP_INT_MAX;
        $newNodeIndex = -1;
        $newNodeDistanceFrom = 0;

        foreach ($nodes as $position => $node) {
            $nextNodeIndex = ($position+1 < count($nodes)) ? $position+1 : 0;

            $distanceTo = sqrt(pow($tspNode->x - $nodes[$position]->x, 2) + (pow($tspNode->y - $nodes[$position]->y, 2)));;
            $distanceFrom = sqrt(pow($tspNode->x - $nodes[$nextNodeIndex]->x, 2) + (pow($tspNode->y - $nodes[$nextNodeIndex]->y, 2)));;

            $loss = $distanceTo + $distanceFrom - $nodes[$nextNodeIndex]->distanceTo;
            if ($loss < $minLoss) {
                $minLoss = $loss;
                $newNodeIndex = $position+1;

                $tspNode->distanceTo = $distanceTo;
                $newNodeDistanceFrom = $distanceFrom;
            }

        }

        array_splice($nodes, $newNodeIndex, 0, array($tspNode));

        $nextNodeIndex = ($newNodeIndex+1 < count($nodes)) ? $newNodeIndex+1 : 0;
        $nodes[$nextNodeIndex]->distanceTo = $newNodeDistanceFrom;
    }

    $nodesCount++;

}


function simulateNode($w, $h) {
    global $nodes, $nodesCount, $originalOrderNodes;

    $tspNode = new TspNode();
    $tspNode->x = $w;
    $tspNode->y = $h;
    $tspNode->distanceTo = 0;


    if ($nodesCount == 0) {
    } elseif ($nodesCount == 1) {

    } else {
        $minLoss = PHP_INT_MAX;
        $newNodeIndex = -1;

        foreach ($nodes as $position => $node) {
            $nextNodeIndex = ($position+1 < count($nodes)) ? $position+1 : 0;

            $distanceTo = sqrt(pow($tspNode->x - $nodes[$position]->x, 2) + (pow($tspNode->y - $nodes[$position]->y, 2)));;
            $distanceFrom = sqrt(pow($tspNode->x - $nodes[$nextNodeIndex]->x, 2) + (pow($tspNode->y - $nodes[$nextNodeIndex]->y, 2)));;

            $loss = $distanceTo + $distanceFrom - $nodes[$nextNodeIndex]->distanceTo;
            if ($loss < $minLoss) {
                $minLoss = $loss;
                $newNodeIndex = $position+1;
            }

        }
    }

    return $newNodeIndex;

}


for ($i=0; $i<count($toImport); $i++) {
    addNode($toImport[$i][0], $toImport[$i][1]);
}

$nodePoints = [];
foreach ($nodes as $node) {
    $nodePoints[] = $node->x*8+4;
    $nodePoints[] = $node->y*8+4;
}

//print_r($nodePoints);
//die();

// create image
$image = imagecreatetruecolor($imageWidth, $imageHeight);

// allocate colors
$bg   = imagecolorallocate($image, 0, 0, 0);
$blue = imagecolorallocate($image, 0, 0, 255);

$greys = [
    imagecolorallocate($image, 64, 64, 64),
    imagecolorallocate($image, 96, 96, 96),
    imagecolorallocate($image, 128, 128, 128),
    imagecolorallocate($image, 160, 160, 160),
    imagecolorallocate($image, 192, 192, 192),
    imagecolorallocate($image, 128, 128, 192),
    imagecolorallocate($image, 192, 128, 128),
    imagecolorallocate($image, 128, 192, 128),
];

// fill the background
imagefilledrectangle($image, 0, 0, $imageWidth-1, $imageHeight-1, $bg);

for ($x=1; $x<1000; $x++) {
    for ($y=1; $y<1000; $y++) {
        $sim = simulateNode($x/10, $y/10)-1;

        imagefilledellipse($image, $x/10*8+4, $y/10*8+4, 5, 5, $greys[$sim]);

    }
}
//print_r($nodes);
//print_r($sim);
//die();



// draw a polygon
imagepolygon($image, $nodePoints, count($nodePoints)/2, $blue);

$col_ellipse = imagecolorallocate($image, 255, 255, 255);

$totalDistance = 0;
// draw the white ellipse
foreach ($nodes as $node) {
    $totalDistance += $node->distanceTo;
    $nodePoints[] = $node->x;
    $nodePoints[] = $node->y;
    imagefilledellipse($image, $node->x*8+4, $node->y*8+4, 3, 2, $col_ellipse);
}

$output = '';
foreach ($nodes as $node) {
    $output .= $node->x . ',' . $node->y . "\n";
}


file_put_contents('1500nodes.txt', $output);

$Originaloutput = print_r($originalOrderNodes, true);
file_put_contents('1500nodes_original.txt', $Originaloutput);

file_put_contents('1500nodes_distance.txt', 'Total: ' . $totalDistance);



//return;

// flush image
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
