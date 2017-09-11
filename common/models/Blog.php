<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "blog".
 *
 * @property integer $id
 * @property string $title
 * @property string $content
 * @property integer $views
 * @property integer $is_delete
 * @property string $created_at
 * @property string $updated_at
 */
class Blog extends \yii\db\ActiveRecord
{
    public $category;
    public $file;
    public $province;
    public $city;
    public $area;
    public $district;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content', 'title', 'views', 'category'], 'required'],
            [['views', 'is_delete'], 'integer'],
            [['title'], 'string', 'max' => 100],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'default', 'value' => date('Y-m-d')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

            'title' => '标题',
            'content' => '内容',
            'views' => '点击量',
            'is_delete' => '是否删除',

        ];
    }

    /**
     *  下拉筛选
     * @column string 字段
     * @value mix 字段对应的值，不指定则返回字段数组
     * @return array 返回某个值或者数组
     */
    public static function dropDown($column, $value = null)
    {
        $dropDownList = [
            "is_delete" => [
                0 => "显示",
                1 => "删除",
            ],
            "is_hot" => [
                0 => "否",
                1 => "是",
            ],
            //有新的字段要实现下拉规则，可像上面这样进行添加
            // ......
        ];
        //根据具体值显示对应的值
        if ($value !== null)
            return $dropDownList[$column][$value];
        //返回关联数组，用户下拉的filter实现
        else
            return $dropDownList[$column];
    }

    /**
     * @param $pid
     * @return array
     */
    public function getCityList($pid)
    {
//        $model = City::findAll(array('pid' => $pid));
        $model = (new Query())
            ->select('*')
            ->from('region')
            ->where(['parent_id' => $pid])
            ->all();

        return ArrayHelper::map($model, 'id', 'name');
    }

    public static function getRegion($parentId = 0)
    {
        $model = (new Query())
            ->select('*')
            ->from('region')
            ->where(['parent_id' => $parentId])
            ->all();

        return ArrayHelper::map($model, 'id', 'name');
    }
}
