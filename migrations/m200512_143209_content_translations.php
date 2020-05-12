<?php

use yii\db\Migration;

/**
 * Class m200512_143209_content_translations
 */
class m200512_143209_content_translations extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $defaultLocale = null;
        if (isset(Yii::$app->sourceLanguage))
            $defaultLocale = Yii::$app->sourceLanguage;

        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%content_blocks}}')->getColumn('source_id'))) {
            $this->addColumn('{{%content_blocks}}', 'source_id', $this->integer()->null()->after('id'));

            // Setup foreign key to source id
            $this->createIndex('{{%idx-content-blocks-source}}', '{{%content_blocks}}', ['source_id']);
            $this->addForeignKey(
                'fk_content_blocks_to_source',
                '{{%content_blocks}}',
                'source_id',
                '{{%content_blocks}}',
                'id',
                'NO ACTION',
                'CASCADE'
            );

        }

        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%content_blocks}}')->getColumn('locale'))) {
            $this->addColumn('{{%content_blocks}}', 'locale', $this->string(10)->defaultValue($defaultLocale)->after('status'));
            $this->createIndex('{{%idx-content-blocks-locale}}', '{{%content_blocks}}', ['locale']);

            // If module `Translations` exist setup foreign key `locale` to `trans_langs.locale`
            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                $this->addForeignKey(
                    'fk_content_blocks_to_langs',
                    '{{%content_blocks}}',
                    'locale',
                    $langsTable,
                    'locale',
                    'NO ACTION',
                    'CASCADE'
                );
            }
        }

        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%content_fields}}')->getColumn('source_id'))) {
            $this->addColumn('{{%content_fields}}', 'source_id', $this->integer()->null()->after('id'));

            // Setup foreign key to source id
            $this->createIndex('{{%idx-content-fields-source}}', '{{%content_fields}}', ['source_id']);
            $this->addForeignKey(
                'fk_content_fields_to_source',
                '{{%content_fields}}',
                'source_id',
                '{{%content_fields}}',
                'id',
                'NO ACTION',
                'CASCADE'
            );

        }

        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%content_fields}}')->getColumn('locale'))) {
            $this->addColumn('{{%content_fields}}', 'locale', $this->string(10)->defaultValue($defaultLocale)->after('params'));
            $this->createIndex('{{%idx-content-fields-locale}}', '{{%content_fields}}', ['locale']);

            // If module `Translations` exist setup foreign key `locale` to `trans_langs.locale`
            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                $this->addForeignKey(
                    'fk_content_fields_to_langs',
                    '{{%content_fields}}',
                    'locale',
                    $langsTable,
                    'locale',
                    'NO ACTION',
                    'CASCADE'
                );
            }
        }

        if (is_null($this->getDb()->getSchema()->getTableSchema('{{%content}}')->getColumn('locale'))) {
            $this->addColumn('{{%content}}', 'locale', $this->string(10)->defaultValue($defaultLocale)->after('content'));
            $this->createIndex('{{%idx-content-locale}}', '{{%content}}', ['locale']);

            // If module `Translations` exist setup foreign key `locale` to `trans_langs.locale`
            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                $this->addForeignKey(
                    'fk_content_to_langs',
                    '{{%content}}',
                    'locale',
                    $langsTable,
                    'locale',
                    'NO ACTION',
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
        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%content_blocks}}')->getColumn('source_id'))) {
            $this->dropIndex('{{%idx-content-blocks-source}}', '{{%content_blocks}}');
            $this->dropColumn('{{%content_blocks}}', 'source_id');
            $this->dropForeignKey(
                'fk_content_blocks_to_source',
                '{{%content_blocks}}'
            );
        }

        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%content_blocks}}')->getColumn('locale'))) {
            $this->dropIndex('{{%idx-content-blocks-locale}}', '{{%content_blocks}}');
            $this->dropColumn('{{%content_blocks}}', 'locale');

            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                if (!(Yii::$app->db->getTableSchema($langsTable, true) === null)) {
                    $this->dropForeignKey(
                        'fk_content_blocks_to_langs',
                        '{{%content_blocks}}'
                    );
                }
            }
        }

        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%content_fields}}')->getColumn('source_id'))) {
            $this->dropIndex('{{%idx-content-fields-source}}', '{{%content_fields}}');
            $this->dropColumn('{{%content_fields}}', 'source_id');
            $this->dropForeignKey(
                'fk_content_fields_to_source',
                '{{%content_fields}}'
            );
        }

        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%content_fields}}')->getColumn('locale'))) {
            $this->dropIndex('{{%idx-content-fields-locale}}', '{{%content_fields}}');
            $this->dropColumn('{{%content_fields}}', 'locale');

            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                if (!(Yii::$app->db->getTableSchema($langsTable, true) === null)) {
                    $this->dropForeignKey(
                        'fk_content_fields_to_langs',
                        '{{%content_fields}}'
                    );
                }
            }
        }

        if (!is_null($this->getDb()->getSchema()->getTableSchema('{{%content}}')->getColumn('locale'))) {
            $this->dropIndex('{{%idx-content-locale}}', '{{%content}}');
            $this->dropColumn('{{%content}}', 'locale');

            if (class_exists('\wdmg\translations\models\Languages')) {
                $langsTable = \wdmg\translations\models\Languages::tableName();
                if (!(Yii::$app->db->getTableSchema($langsTable, true) === null)) {
                    $this->dropForeignKey(
                        'fk_content_to_langs',
                        '{{%content}}'
                    );
                }
            }
        }
    }
}
