<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "blog_category".
 *
 * @property integer $blog_id
 * @property integer $category_id
 */
class BlogCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'blog_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['blog_id', 'category_id'], 'required'],
            [['blog_id', 'category_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'blog_id' => '文章ID',
            'category_id' => '栏目ID',
        ];
    }

    public static function getRelationCategory($blogId)
    {
        $res = static::find()->select('category_id')->where(['blog_id' => $blogId])->column();
        return $res;
    }

    public static function insertBlogCategory($blogId, $categoryId)
    {
        $data = [];
        foreach ($categoryId as $k => $v) {
            // 注意这里的属组形式[blog_id, category_id]，一定要跟下面batchInsert方法的第二个参数保持一致
            $data[] = [$blogId, $v];
        }
        $blogCategory = new BlogCategory();
        $attributes = ['blog_id', 'category_id'];
        $tableName = $blogCategory::tableName();
        $db = BlogCategory::getDb();

        self::deleteAll(['blog_id' => $blogId]);

        $db->createCommand()->batchInsert($tableName, $attributes, $data)->execute();
    }
}
