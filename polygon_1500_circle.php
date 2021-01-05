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
    [2,1],
    [99,100],
    [49,49],
    [51,51],
    [3,98],
    [97,4],
];


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

$totalX = 0;
$totalY = 0;

$numberNodes = count($toImport);

//shit gets copied
for ($i=0; $i<$numberNodes; $i++) {
    $toImport[$i][0] *= 8; // for visibility
    $toImport[$i][1] *= 8; // for visibility
    $totalX += $toImport[$i][0];
    $totalY += $toImport[$i][1];
}


$center = [
    $totalX/$numberNodes,
    $totalY/$numberNodes
];


/*  CAN DO ANYTHING HERE
*/
//shit gets copied












shuffle($toImport);







//    SORT HERE
//    DO NOT ADD NODE HERE :::::: addNode($toImport[$i][0], $toImport[$i][1]);
//    SORT HERE


//
//for ($i=0; $i<count($toImport); $i++) {
//
//
//    // distance to center
//    $toImport[$i][2] = sqrt(pow($toImport[$i][0] - $center[0], 2) + (pow($toImport[$i][1] -  - $center[1], 2)));;
//
//
//
//    $toProcess[] = $toImport[$i];
//}
//
//
//function cmp($a, $b)
//{
//    if ($a[1] == $b[1]) {
//        return 0;
//    }
//    return ($a[1] > $b[1]) ? 1 : -1;
//}
//
//usort($toProcess, "cmp");

$toProcess = $toImport;

//print_r($toProcess); die();

// DIE BEFORE HERE

//    END OF SORT HERE


/*    END OF CAN DO ANYTHING
*/

//REAL SHIT BELOW
//add nodes in the order provided
//shit gets copied
for ($i=0; $i<count($toProcess); $i++) {
    addNode($toProcess[$i][0], $toProcess[$i][1]);
}

$nodePoints = [];
foreach ($nodes as $node) {
    $nodePoints[] = $node->x;
    $nodePoints[] = $node->y;
}

//print_r($nodePoints);
//die();

// create image
$image = imagecreatetruecolor($imageWidth, $imageHeight);

// allocate colors
$bg   = imagecolorallocate($image, 0, 0, 0);
$blue = imagecolorallocate($image, 0, 0, 255);

// fill the background
imagefilledrectangle($image, 0, 0, $imageWidth-1, $imageHeight-1, $bg);

// draw a polygon
imagepolygon($image, $nodePoints, count($nodePoints)/2, $blue);

$col_ellipse = imagecolorallocate($image, 255, 255, 255);

$totalDistance = 0;
// draw the white ellipse
foreach ($nodes as $node) {
    $totalDistance += $node->distanceTo;
    $nodePoints[] = $node->x;
    $nodePoints[] = $node->y;
    imagefilledellipse($image, $node->x-1, $node->y-1, 3, 2, $col_ellipse);
}

$output = '';
foreach ($nodes as $node) {
    $output .= $node->x/8 . ',' . $node->y/8 . "\n";
}


file_put_contents('1500nodes.txt', $output);

$Originaloutput = print_r($originalOrderNodes, true);
file_put_contents('1500nodes_original.txt', $Originaloutput);

file_put_contents('1500nodes_distance.txt', 'Total: ' . $totalDistance/8);



//return;

// flush image
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
