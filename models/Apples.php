<?php

namespace app\models;

use Yii;
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
        $data = [];
        for($i = 0; $i < $count; $i++){
            $treeId = 1;

            $colorToRgb = [
                rand(0, 255),
                rand(0, 255),
                rand(0, 255),
            ];

            $color = '#' . implode(array_map(function($decColor) {
                return sprintf("%02s", dechex($decColor));
            }, $colorToRgb));

            $status = $this->statuses[rand(0, 2)];

            if($status == 'handing'){
                $positionCrown = $this->generatePositionToCrown();
                $posX = $positionCrown['x'];
                $posY = $positionCrown['y'];
                $fellIn = null;
            }else{
                $positionGround = $this->generatePositionToGround();
                $posX = $positionGround['x'];
                $posY = $positionGround['y'];

                $fellIn = date('Y-m-d H:i:s');
                if($status == 'decayed'){
                    $color = '#654321';
                }
            }

            $data[] = [
                $treeId,
                $color,
                $status,
                $posX,
                $posY,
                $fellIn,
            ];
        }

        Yii::$app->db->createCommand()->batchInsert('apples', [
            'tree_id',
            'color',
            'status',
            'pos_x',
            'pos_y',
            'fell_in',
        ], $data)->execute();
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
