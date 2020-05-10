<?php

use yii\db\Migration;

/**
 * Class m191220_231744_content_blocks
 */
class m191220_223044_content_blocks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%content_blocks}}', [
            'id' => $this->primaryKey(),

            'title' => $this->string(64)->notNull(),
            'description' => $this->string(255)->null(),
            'alias' => $this->string(64)->null(),
            'fields' => $this->text()->null(), // serialized data of content fields
            'type' => $this->tinyInteger(1)->notNull()->defaultValue(1), // 1 - block, 2 - list
            'status' => $this->tinyInteger(1)->null()->defaultValue(0), // 0 - draft, 1 - published

            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11),
        ], $tableOptions);


        if (class_exists('\wdmg\users\models\Users')) {
            $this->createIndex('{{%idx-content_blocks-author}}','{{%content_blocks}}', ['created_by', 'updated_by'], false);
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->addForeignKey(
                    'fk_content_blocks_created2users',
                    '{{%content_blocks}}',
                    'created_by',
                    $userTable,
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
                $this->addForeignKey(
                    'fk_content_blocks_updated2users',
                    '{{%content_blocks}}',
                    'updated_by',
                    $userTable,
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
            }
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        if (class_exists('\wdmg\users\models\Users')) {
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropIndex('{{%idx-content_blocks-author}}', '{{%content_blocks}}');
                $this->dropForeignKey(
                    'fk_content_blocks_created2users',
                    '{{%content_blocks}}'
                );
                $this->dropForeignKey(
                    'fk_content_blocks_updated2users',
                    '{{%content_blocks}}'
                );
            }
        }

        $this->truncateTable('{{%content_blocks}}');
        $this->dropTable('{{%content_blocks}}');
    }

}
