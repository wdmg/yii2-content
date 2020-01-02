<?php

use yii\db\Migration;

/**
 * Class m191220_233620_content_items
 */
class m191220_233620_content_items extends Migration
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

        $this->createTable('{{%content_items}}', [
            'id' => $this->primaryKey(),
            'block_id' => $this->integer(11)->notNull(),
            'ext_id' => $this->integer(11)->notNull(),
            'row_order' => $this->integer(2)->notNull()->defaultValue(10), // sort order range
        ], $tableOptions);

        $this->createIndex('{{%idx-content_items-blocks}}', '{{%content_items}}', ['block_id']);
        $this->createIndex('{{%idx-content_items-content}}', '{{%content_items}}', ['ext_id']);
        $this->addForeignKey(
            'fk_content_items_to_blocks',
            '{{%content_items}}',
            'block_id',
            '{{%content_blocks}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_content_items_to_content',
            '{{%content_items}}',
            'ext_id',
            '{{%content}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('{{%idx-content_items-blocks}}', '{{%content_items}}');
        $this->dropIndex('{{%idx-content_items-content}}', '{{%content_items}}');
        $this->dropForeignKey(
            'fk_content_items_to_blocks',
            '{{%content_items}}'
        );
        $this->dropForeignKey(
            'fk_content_items_to_content',
            '{{%content_items}}'
        );

        $this->truncateTable('{{%content_items}}');
        $this->dropTable('{{%content_items}}');
    }

}
