<?php

use yii\db\Migration;

class m170124_102527_add_profile_photo_url extends Migration
{
    public function up()
    {
        $this->addColumn('profile', 'photo_url', $this->string()->after('timezone'));
    }

    public function down()
    {
        $this->dropColumn('profile', 'photo_url');
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
