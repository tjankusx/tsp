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
[1, 1],
[1, 100],
[100, 1],
//    [100, 100],
//    [25, 49],
];

shuffle($toImport);


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
        return null;
    } elseif ($nodesCount == 1) {

        $tspNode->distanceTo = sqrt(pow($tspNode->x - $nodes[0]->x, 2) + (pow($tspNode->y - $nodes[0]->y, 2)));

        $nodes[0]->distanceTo = $tspNode->distanceTo;
        $nodes[] = $tspNode;

        return null;
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
//                $newNodeDistanceFrom = $distanceFrom;
            }

        }
//
//        array_splice($nodes, $newNodeIndex, 0, array($tspNode));
//
//        $nextNodeIndex = ($newNodeIndex+1 < count($nodes)) ? $newNodeIndex+1 : 0;
//        $nodes[$nextNodeIndex]->distanceTo = $newNodeDistanceFrom;
    }

//    $nodesCount++;
    return [
        'minLoss' => $minLoss,
        'newNodeIndex' => $newNodeIndex,
        'distanceFrom' => $distanceFrom,
        'tspNode' => array($tspNode),
    ];

}

while(count($toImport)) {

//    echo count($toImport).'<br>';

    $bestResult = null;
    foreach ($toImport as $index => $node) {

        $result = addNode($node[0], $node[1]);


        if (empty($result)) {
            array_splice($toImport, $result['newNodeIndex'], 1);
            $nextNodeIndex = ($result['newNodeIndex'] + 1 < count($nodes)) ? $result['newNodeIndex'] + 1 : 0;
            $nodes[$nextNodeIndex]->distanceTo = $result['distanceFrom'];
            break;
        }



        if (empty($bestResult) || ($result['minLoss'] < $bestResult['minLoss'])) {
            $bestResult = $result;
            $bestResult['index'] = $index;
        }
    }
    $nodesCount++;


    if (empty($bestResult)) {
        break;
    }


    array_splice($toImport, $bestResult['index'], 1);
    array_splice($nodes, $bestResult['newNodeIndex'], 0, $bestResult['tspNode']);


//    print_r($nodes);

    $nextNodeIndex = ($bestResult['newNodeIndex'] + 1 < count($nodes)) ? $bestResult['newNodeIndex'] + 1 : 0;
    $nodes[$nextNodeIndex]->distanceTo = $bestResult['distanceFrom'];

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

$output = print_r($nodes, true);
file_put_contents('1500nodes.txt', $output);

$Originaloutput = print_r($originalOrderNodes, true);
file_put_contents('1500nodes_original.txt', $Originaloutput);

file_put_contents('1500nodes_distance.txt', 'Total: ' . $totalDistance);



//return;

// flush image
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
