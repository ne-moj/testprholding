<?php

namespace app\models;

use yii\db\ActiveRecord;

class Apples extends ActiveRecord
{
    public function generateApples($count = 20)
    {
        $userId = 1;
        $treeId = 1;

        $treeWidth = 200;
        $treeHeight = 150;
        $treeHalfWidth = $treeWidth / 2;
        $treeHalfHeigh = $treeHeight / 2;
        $heightTrunk = $treeHalfHeigh;

        $colorToRgb = [
            random_int(0, 255),
            random_int(0, 255),
            random_int(0, 255),
        ]

        $colorHex = '#' . implode(array_map(function($decColor) {
            return dechex($decColor);
        }, $colorToRgb));

        $positionCrown = $this->generatePositionToCrown();
        $positionGround = $this->generatePositionToGround();
    }

    private function generatePositionToCrown ()
    {
        $dataTree = $this->getDataByTree();

        $treeCroneCenterPosX = $dataTree['crownCenterPosX'];
        $treeCroneCenterPosY = $dataTree['crownCenterPosY'];

        $treeHalfWidth = $dataTree['halfCrownWidth'];
        $treeHalfHeigh = $dataTree['halfCrownHeigh'];


        $posX = random_int(-$treeHalfWidth, $treeHalfWidth);
        $posY = random_int(-$treeHalfHeigh, $treeHalfHeigh);

        $ovalVer = (($posX * $posX) / ($treeHalfWidth * $treeHalfWidth)) + (($posY * $posY) / ($treeHalfHeigh * $treeHalfHeigh));

        if($ovalVer > 1){
            $reductionRatio = sqrt($ovalVer);
            $posX /= $reductionRatio;
            $posY /= $reductionRatio;
        }

        return [
            'x' => $treeCroneCenterPosX + $posX,
            'y' => $treeCroneCenterPosY + $posY,
        ];
    }

    private function generatePositionToGround ()
    {
        $dataTree = $this->getDataByTree();

        $treeCroneCenterPosX = $dataTree['crownCenterPosX'];

        $treeHalfWidth = $dataTree['halfCrownWidth'];

        $posX = random_int(-$treeHalfWidth, $treeHalfWidth);

        return [
            'y' => 0,
            'x' => $treeCroneCenterPosX + $posX,
        ];
    }

    private function getDataByTree ()
    {
        return [
            'crownCenterPosX' = 120,
            'crownCenterPosY' = 150,
            'crownWidth' => 200,
            'crownHeight' => 150,
            'halfCrownWidth' => 100,
            'halfCrownHeigh' => 75,
        ];
    }
}
