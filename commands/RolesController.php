<?php
/**
 * Created by PhpStorm.
 * User: denis
 * Date: 18/01/2018
 * Time: 17:29
 */

namespace app\commands;

use app\models\User;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\ArrayHelper;

class RolesController extends Controller
{
    /**
     * This function create basic admin and user roles
     */
    public function actionCreateBasicRoles()
    {
        $auth = \Yii::$app->authManager;
        $auth->removeAll();

        $user = $auth->createRole('user');
        $user->description = 'User';
        $auth->add($user);

        $admin = $auth->createRole('admin');
        $admin->description = 'Admin';
        $auth->add($admin);

        $auth->addChild($admin, $user);
    }

    /**
     * Assign role to user by username
     */
    public function actionAssignRoleToUser()
    {
        $username = $this->prompt('Enter Username:', ['required' => true]);
        $user = $this->findUserModel($username);
        $roleNames = $this->select('Existing roles:', ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'description'));
        $authManager = \Yii::$app->getAuthManager();
        $role = $authManager->getRole($roleNames);
        if ($authManager->assign($role, $user->id)) {
            $this->stdout('Done!' . PHP_EOL);
        }else{
            $this->stdout('Something goes wrong!' . PHP_EOL);
        }

    }

    /**
     * Remove role from user by username
     */
    public function actionRevokeRoleFromUser()
    {
        $username = $this->prompt('Enter username:', ['required' => true]);
        $user = $this->findUserModel($username);
        $roleNames = $this->select('Role:',
            ArrayHelper::merge(
                ['all' => 'All Roles'],
                ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'description')
            )
        );
        $authManager = \Yii::$app->getAuthManager();
        if ($roleNames == 'all'){
            $authManager->revokeAll($user->id);
        }else{
            $role = $authManager->getRole($roleNames);
            $authManager->revoke($role, $user->id);
        }
        $this->stdout('Done!' . PHP_EOL);
    }

    /**
     * Find user by username
     * @param $username
     * @return null|static
     * @throws Exception
     */
    private function findUserModel($username)
    {
        if (!$model = User::findOne(['username' => $username])){
            throw new Exception('User is not found');
        }else{
            return $model;
        }
    }
}