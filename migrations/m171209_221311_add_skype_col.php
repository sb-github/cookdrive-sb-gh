<?php

use yii\db\Migration;

class m171209_221311_add_skype_col extends Migration
{
    public function up()
    {
        $this->addColumn('profile', 'skype_id', $this->string()->after('photo_url'));
    }

    public function down()
    {
        $this->dropColumn('profile', 'skype_id');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
