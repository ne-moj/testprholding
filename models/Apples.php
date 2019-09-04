<?php

namespace app\models;

use yii\db\ActiveRecord;

class Apples extends ActiveRecord
{
    private $statuses = [
        'handing',
        'lay',
        'decayed',
    ];

    public function generateApples($count = 20)
    {
        for($i = 0; $i < $count; $i++){
            $this->id = null;
            $this->tree_id = 1;

            $colorToRgb = [
                rand(0, 255),
                rand(0, 255),
                rand(0, 255),
            ];

            $this->color = '#' . implode(array_map(function($decColor) {
                return sprintf("%02s", dechex($decColor));
            }, $colorToRgb));

            $this->status = $this->statuses[rand(0, 2)];

            if($this->status == 'handing'){
                $positionCrown = $this->generatePositionToCrown();
                $this->pos_x = $positionCrown['x'];
                $this->pos_y = $positionCrown['y'];
                $this->fell_in = null;
            }else{
                $positionGround = $this->generatePositionToGround();
                $this->pos_x = $positionGround['x'];
                $this->pos_y = $positionGround['y'];

                $this->fell_in = date('Y-m-d H:i:s');
                if($this->status == 'decayed'){
                    $this->color = '#654321';
                }
            }

            $this->insertInternal();
        }
    }

    private function generatePositionToCrown ()
    {
        $dataTree = $this->getDataByTree();

        $treeCroneCenterPosX = $dataTree['crownCenterPosX'];
        $treeCroneCenterPosY = $dataTree['crownCenterPosY'];

        $treeHalfWidth = $dataTree['halfCrownWidth'];
        $treeHalfHeigh = $dataTree['halfCrownHeigh'];


        $posX = rand(-$treeHalfWidth, $treeHalfWidth);
        $posY = rand(-$treeHalfHeigh, $treeHalfHeigh);

        $ovalVerification = (($posX * $posX) / ($treeHalfWidth * $treeHalfWidth)) + (($posY * $posY) / ($treeHalfHeigh * $treeHalfHeigh));

        if($ovalVerification > 1){
            $reductionRatio = sqrt($ovalVerification);
            $posX /= $reductionRatio;
            $posY /= $reductionRatio;
        }

        return [
            'x' => (int) ($treeCroneCenterPosX + $posX),
            'y' => (int) ($treeCroneCenterPosY + $posY),
        ];
    }

    private function generatePositionToGround ()
    {
        $dataTree = $this->getDataByTree();

        $treeCroneCenterPosX = $dataTree['crownCenterPosX'];

        $treeHalfWidth = $dataTree['halfCrownWidth'];

        $posX = rand(-$treeHalfWidth, $treeHalfWidth);

        return [
            'y' => 0,
            'x' => (int) $treeCroneCenterPosX + $posX,
        ];
    }

    private function getDataByTree ()
    {
        $width = 200;
        $height = 500;
        return [
            'crownCenterPosX' => $width / 2,
            'crownCenterPosY' => $height,
            'crownWidth' => $width,
            'crownHeight' => $height,
            'halfCrownWidth' => $width / 2,
            'halfCrownHeigh' => $height / 2,
        ];
    }
}
