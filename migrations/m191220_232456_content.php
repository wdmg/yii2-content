<?php

use yii\db\Migration;

/**
 * Class m191220_232456_content
 */
class m191220_232456_content extends Migration
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

        $this->createTable('{{%content}}', [
            'id' => $this->primaryKey(),

            'field_id' => $this->integer(11)->notNull(),
            'block_id' => $this->integer(11)->notNull(),
            'content' => $this->text()->notNull(),

            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'created_by' => $this->integer(11),
            'updated_at' => $this->datetime()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_by' => $this->integer(11),
        ], $tableOptions);

        $this->createIndex('{{%idx-content-fields}}', '{{%content}}', ['field_id']);
        $this->createIndex('{{%idx-content-blocks}}', '{{%content}}', ['block_id']);
        $this->addForeignKey(
            'fk_content_to_fields',
            '{{%content}}',
            'field_id',
            '{{%content_fields}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_content_to_blocks',
            '{{%content}}',
            'block_id',
            '{{%content_blocks}}',
            'id',
            'NO ACTION',
            'CASCADE'
        );

        if (class_exists('\wdmg\users\models\Users')) {
            $this->createIndex('{{%idx-content-author}}','{{%content}}', ['created_by', 'updated_by'], false);
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->addForeignKey(
                    'fk_content_created2users',
                    '{{%content}}',
                    'created_by',
                    $userTable,
                    'id',
                    'CASCADE',
                    'CASCADE'
                );
                $this->addForeignKey(
                    'fk_content_updated2users',
                    '{{%content}}',
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
        $this->dropIndex('{{%idx-content-fields}}', '{{%content}}');
        $this->dropIndex('{{%idx-content-blocks}}', '{{%content}}');
        $this->dropForeignKey(
            'fk_content_to_fields',
            '{{%content}}'
        );
        $this->dropForeignKey(
            'fk_content_to_blocks',
            '{{%content}}'
        );

        if (class_exists('\wdmg\users\models\Users')) {
            $userTable = \wdmg\users\models\Users::tableName();
            if (!(Yii::$app->db->getTableSchema($userTable, true) === null)) {
                $this->dropIndex('{{%idx-content-author}}', '{{%content}}');
                $this->dropForeignKey(
                    'fk_content_created2users',
                    '{{%content}}'
                );
                $this->dropForeignKey(
                    'fk_content_updated2users',
                    '{{%content}}'
                );
            }
        }

        $this->truncateTable('{{%content}}');
        $this->dropTable('{{%content}}');
    }

}
