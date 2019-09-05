<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\db\Connection;

defined ('APPLE_HANGING') or define('APPLE_HANGING', 'hanging');
defined ('APPLE_LAY')     or define('APPLE_LAY', 'lay');
defined ('APPLE_DECAYED') or define('APPLE_DECAYED', 'decayed');
defined ('APPLE_DECAYED_COLOR') or define('APPLE_DECAYED_COLOR', '#654321');
defined ('APPLE_ROTTING_TIME') or define('APPLE_ROTTING_TIME', 5 * 60 * 60); // 5 hours

class Apple extends ActiveRecord
{
    /*
     * @static
     * @var Tree|null
     */
    private static $tree = null;

    /*
     * @static
     * @var array
     */
    public static $statuses = [
        APPLE_HANGING,
        APPLE_LAY,
        APPLE_DECAYED,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        $prefix = isset(Connection::$tablePrefix) ? Connection::$tablePrefix : '';

        return $prefix . 'apples';
    }

    /*
     * @param string $color (hex value)
     */
    public function __construct($color = null, $status = null)
    {
        $tree = self::getTree();

        $this->tree_id    = $tree->id;
        $this->status     = !empty($status) && in_array($status, self::$statuses) ? $status : self::generateStatus();
        $this->color      = $color ? $color : self::generateColor($this->status);
        $position         = self::generatePositions($this->status);
        $this->pos_x      = $position['x'];
        $this->pos_y      = $position['y'];
        $this->created_at = self::generateCreatedAt($this->status);
        $this->fell_in    = self::generateFellIn($this->status);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $statuses = self::$statuses;
        return [
            [['tree_id', 'status', 'color'], 'required'],
            [['tree_id', 'pos_x', 'pos_y'], 'integer'],
            ['status', 'in', 'range' => $statuses, 'message' => 'The status is unknown. Must be (' . implode(', ', $statuses) . ').'],
            ['color', 'match', 'pattern' => '/#([a-f0-9]{3}){1,2}\b/i', 'message' => 'Color must be in hex value'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        if($this->status == APPLE_LAY){
            $rottingTime = strtotime($this->fell_in) + APPLE_ROTTING_TIME;
            if(time() > $rottingTime){
                $this->rot();
            }
        }

        parent::afterFind();
    }

    /*
     * Fall the apple to the ground
     *
     * @return null
     * @throws yii\base\Exception
     */
    public function fallToGround()
    {
        if($this->status == APPLE_HANGING){
            $this->status = APPLE_LAY;
            $this->pos_y = 0;
            $this->fell_in = date('Y-m-d H:i:s');

            return $this->save();
        }elseif(in_array($this->status, array(APPLE_DECAYED, APPLE_LAY))){
            throw new Exception('An apple cannot fall, it is already on the ground.');
        }else{
            throw new Exception("The {$this->status} status is unknown");
        }
    }

    /*
     * Eat the apple
     *
     * @return boolean
     * @throws yii\base\Exception
     */
    public function eat($percent)
    {
        if(in_array($this->status, array(APPLE_DECAYED, APPLE_HANGING))){
            $message = $this->status == APPLE_HANGING ? 'Apple hanging on a tree' : 'A rotten apple cannot be eaten';

            throw new Exception($message);
        }elseif($this->status != APPLE_LAY){
            throw new Exception("The {$this->status} status is unknown");
        }

        $this->eaten += $percent;
        if($this->eaten >= 100){
            $this->eaten = 100;
            return $this->delete();
        }else{
            return $this->save();
        }
    }

    /*
     * Method irreversibly spoils the apple
     *
     * @return boolean
     */
    public function rot()
    {
        $this->status = APPLE_DECAYED;
        $this->color = APPLE_DECAYED_COLOR;

        return $this->save();
    }

    /*
     * Check the apple is eaten up
     *
     * @return integer
     */
    public function isEatenUp()
    {
        return $this->eaten >= 100;
    }

    /*
     * Return size the apple
     *
     * @return integer
     */
    public function size()
    {
        return 100 - $this->eaten;
    }

    /*
     * Generate $count apples for a user tree
     *
     * @static
     * @param integer $count
     * @return null
     */
    public static function generateApples($count = 20)
    {
        $tree = self::getTree();

        $data = [];
        for($i = 0; $i < $count; $i++){

            $treeId    = $tree->id;
            $status    = self::generateStatus();
            $color     = self::generateColor($status);
            $position  = self::generatePositions($status);
            $createdAt = self::generateCreatedAt($status);
            $fellIn    = self::generateFellIn($status);

            $data[] = [
                $treeId,
                $color,
                $status,
                $position['x'],
                $position['y'],
                $createdAt,
                $fellIn,
            ];
        }

        Yii::$app->db->createCommand()->batchInsert('apples', [
            'tree_id',
            'color',
            'status',
            'pos_x',
            'pos_y',
            'created_at',
            'fell_in',
        ], $data)->execute();
    }

    /*
     * Get the tree associated with the current user
     *
     * @static
     * @return Tree
     */
    public static function getTree()
    {
        if(self::$tree === null){
            self::$tree = Tree::getByUser();
        }

        return self::$tree;
    }

    /*
     * Generate random color (if hanging on a tree)
     *
     * @static
     * @param string $status
     * @return string (hex color)
     */
    protected static function generateColor($status = null)
    {
            if($status == APPLE_DECAYED){
                return APPLE_DECAYED_COLOR;
            }

            $colorToRgb = [
                rand(0, 255), // red
                rand(0, 255), // green
                rand(0, 10), // blue
            ];

            return '#' . implode(
                array_map(
                    function($decColor) {
                        return sprintf("%02s", dechex($decColor));
                    },
                    $colorToRgb
                )
            );
    }

    /*
     * Generate random status (hanging, lay, decayed)
     *
     * @static
     * @return string
     */
    protected static function generateStatus()
    {
        return self::$statuses[rand(0, 2)];
    }

    /*
     * Generate random position (on the crown or on the ground, depending on status)
     *
     * @static
     * @param string $status
     * @return array ([x, y])
     */
    protected static function generatePositions($status = APPLE_HANGING)
    {
        if($status == APPLE_HANGING){
            $position = self::generatePositionToCrown();
        }else{
            $position = self::generatePositionToGround();
        }

        return $position;
    }

    /*
     * Generate random the created date
     *
     * @static
     * @param string $status
     * @return string (datetime)
     */
    protected static function generateCreatedAt($status = APPLE_HANGING)
    {
        return date('Y-m-d H:i:s');
    }

    /*
     * Generate random the fell date
     *
     * @static
     * @param string $status
     * @return string (datetime)
     */
    protected static function generateFellIn($status = APPLE_HANGING)
    {
        if($status != APPLE_HANGING){
            return date('Y-m-d H:i:s');
        }

        return null;
    }

    /*
     * Generate random position apple to crown
     *
     * @static
     * @return array ([x,y])
     */
    protected static function generatePositionToCrown ()
    {
        $dataTree = self::getTree()->getDataByTree();

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

    /*
     * Generate random position apple to ground
     *
     * @static
     * @return array ([x,y])
     */
    protected static function generatePositionToGround ()
    {
        $dataTree = self::getTree()->getDataByTree();

        $treeCroneCenterPosX = $dataTree['crownCenterPosX'];

        $treeHalfWidth = $dataTree['halfCrownWidth'];

        $posX = rand(-$treeHalfWidth, $treeHalfWidth);

        return [
            'y' => 0,
            'x' => (int) ($treeCroneCenterPosX + $posX),
        ];
    }
}
