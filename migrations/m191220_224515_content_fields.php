<?php

use yii\db\Migration;

/**
 * Class m191220_224515_content_fields
 */
class m191220_224515_content_fields extends Migration
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

        $this->createTable('{{%content_fields}}', [
            'id' => $this->primaryKey(),

            'name' => $this->string(45)->notNull(),
            'type' => $this->tinyInteger(1)->notNull()->defaultValue(1), // 1 - string, 2 - text, 3 - html
            'sort_order' => $this->integer(2)->notNull()->defaultValue(10), // sort order range
            'params' => $this->text()->null(), // serialized data

            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        if (class_exists('\wdmg\users\models\Users')) {
            $this->createIndex('{{%idx-content_fields-author}}','{{%content_fields}}', ['created_by', 'updated_by'], false);
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->addForeignKey(
                    'fk_content_fields_created2users',
                    '{{%content_fields}}',
                    'created_by',
                    $userTable,
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
                $this->addForeignKey(
                    'fk_content_fields_updated2users',
                    '{{%content_fields}}',
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
                $this->dropIndex('{{%idx-content_fields-author}}', '{{%content_fields}}');
                $this->dropForeignKey(
                    'fk_content_fields_created2users',
                    '{{%content_fields}}'
                );
                $this->dropForeignKey(
                    'fk_content_fields_updated2users',
                    '{{%content_fields}}'
                );
            }
        }

        $this->truncateTable('{{%content_fields}}');
        $this->dropTable('{{%content_fields}}');
    }

}
