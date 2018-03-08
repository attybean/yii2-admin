<?php

namespace mdm\admin\models;

use mdm\admin\components\Configs;
use mdm\admin\components\Helper;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\rbac\Item;

/**
 * This is the model class for table "tbl_auth_item".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $ruleName
 * @property string $data
 *
 * @property Item $item
 *
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class AuthItem extends Model
{
	//  MOD START
    public $id;
    public $name;
    public $type;
    public $description;
    public $rule_id;
    public $data;
    public $parent_id;
    public $is_active;
    public $added_by;
    public $updated_by;
    public $sys;
	//  MOD END

    /**
     * @var Item
     */
    private $_item;

    /**
     * Initialize object
     * @param Item  $item
     * @param array $config
     */
    public function __construct($item = null, $config = [])
    {
        $this->_item = $item;
        if ($item !== null) {
			//  MOD START
            $this->id = $item->id;
            $this->name = $item->name;
            $this->type = $item->type;
            $this->description = $item->description;
            $this->rule_id = $item->rule_id;
            $this->data = $item->data === null ? null : Json::encode($item->data);
            $this->parent_id = $item->parent_id;
            $this->is_active = $item->is_active;
            $this->added_by = $item->added_by;
            $this->updated_by = $item->updated_by;
            $this->sys = $item->sys;
            //  MOD END
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['name'], 'checkUnique', 'when' => function () {
                return $this->isNewRecord || ($this->_item->name != $this->name);
            }],
			//  MOD START
            [['type', 'id','rule_id','is_active', 'added_by', 'updated_by', 'sys' ], 'integer'],
            [['description', 'data'], 'default'],
            [['parent_id'], 'string', 'max' => 36],
            [['name'], 'string', 'max' => 64]
			//  MOD END
        ];
    }

    /**
     * Check role is unique
     */
    public function checkUnique()
    {
        $authManager = Configs::authManager();
        $value = $this->name;
        if ($authManager->getRole($value) !== null || $authManager->getPermission($value) !== null) {
            $message = Yii::t('yii', '{attribute} "{value}" has already been taken.');
            $params = [
                'attribute' => $this->getAttributeLabel('name'),
                'value' => $value,
            ];
            $this->addError('name', Yii::$app->getI18n()->format($message, $params, Yii::$app->language));
        }
    }

    /**
     * Check for rule
     */
    public function checkRule()
    {
        $name = $this->ruleName;
        if (!Configs::authManager()->getRule($name)) {
            try {
                $rule = Yii::createObject($name);
                if ($rule instanceof \yii\rbac\Rule) {
                    $rule->name = $name;
                    Configs::authManager()->add($rule);
                } else {
                    $this->addError('ruleName', Yii::t('rbac-admin', 'Invalid rule "{value}"', ['value' => $name]));
                }
            } catch (\Exception $exc) {
                $this->addError('ruleName', Yii::t('rbac-admin', 'Rule "{value}" does not exists', ['value' => $name]));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('rbac-admin', 'Name'),
            'type' => Yii::t('rbac-admin', 'Type'),
            'description' => Yii::t('rbac-admin', 'Description'),
			//  MOD START
            // 'ruleName' => Yii::t('rbac-admin', 'Rule Name'),
			//  MOD END
            'data' => Yii::t('rbac-admin', 'Data'),
        ];
    }

	//  MOD START
	public function getNameFromId($item_id)
	{
	    return $this->find($item);
	}
	//  MOD END

    /**
     * Check if is new record.
     * @return boolean
     */
    public function getIsNewRecord()
    {
        return $this->_item === null;
    }

    /**
     * Find role
     * @param string $id
     * @return null|\self
     */
    public static function find($id)
    {
        $item = Configs::authManager()->getRole($id);
        if ($item !== null) {
            return new self($item);
        }

        return null;
    }

    /**
     * Save role to [[\yii\rbac\authManager]]
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            $manager = Configs::authManager();
            if ($this->_item === null) {
                if ($this->type == Item::TYPE_ROLE) {
                    $this->_item = $manager->createRole($this->name);
                } else {
                    $this->_item = $manager->createPermission($this->name);
                }
                $isNew = true;
            } else {
                $isNew = false;
				//  MOD START
                $oldId = $this->_item->id;
				// $oldName = $this->_item->name;
				//  MOD END
            }
            $this->_item->name = $this->name;
            $this->_item->description = $this->description;
			//  MOD START
			// $this->_item->ruleName = $this->ruleName;
			$this->_item->rule_id = $this->rule_id;
			//  MOD END
            $this->_item->data = $this->data === null || $this->data === '' ? null : Json::decode($this->data);
            if ($isNew) {
                $manager->add($this->_item);
            } else {
				//  MOD START
                $manager->update($oldId, $this->_item);
				// $manager->update($oldName, $this->_item);
				//  MOD END
            }
            Helper::invalidate();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds an item as a child of another item.
     * @param array $items
     * @return int
     */
    public function addChildren($items)
    {
        $manager = Configs::authManager();
        $success = 0;
        if ($this->_item) {
            foreach ($items as $name) {
                $child = $manager->getPermission($name);
                if ($this->type == Item::TYPE_ROLE && $child === null) {
                    $child = $manager->getRole($name);
                }
                try {
                    $manager->addChild($this->_item, $child);
                    $success++;
                } catch (\Exception $exc) {
                    Yii::error($exc->getMessage(), __METHOD__);
                }
            }
        }
        if ($success > 0) {
            Helper::invalidate();
        }
        return $success;
    }

    /**
     * Remove an item as a child of another item.
     * @param array $items
     * @return int
     */
    public function removeChildren($items)
    {
        $manager = Configs::authManager();
        $success = 0;
        if ($this->_item !== null) {
            foreach ($items as $name) {
                $child = $manager->getPermission($name);
                if ($this->type == Item::TYPE_ROLE && $child === null) {
                    $child = $manager->getRole($name);
                }
                try {
                    $manager->removeChild($this->_item, $child);
                    $success++;
                } catch (\Exception $exc) {
                    Yii::error($exc->getMessage(), __METHOD__);
                }
            }
        }
        if ($success > 0) {
            Helper::invalidate();
        }
        return $success;
    }

    /**
     * Get items
     * @return array
     */
    public function getItems()
    {
        $manager = Configs::authManager();
        $available = [];
		//  MOD START
        if ($this->type == Item::TYPE_ROLE) {
            $roles = $manager->getRoles();
            foreach ($roles as $k => $role) {
                $available[$role->id] = ['role', $role->name];
            }
            // foreach (array_keys($manager->getRoles()) as $name) {
            //     $available[$name] = 'role';
            // }
        }
            $permissions = $manager->getPermissions();
            foreach ($permissions as $k => $permission) {
                if ($permission->id[0] != '/') {
                    $available[$permission->id] = ['permission', $permission->name];
                }
            }
        // foreach (array_keys($manager->getPermissions()) as $name) {
        //     $available[$name] = $name[0] == '/' ? 'route' : 'permission';
        // }
		//  MOD END

        $assigned = [];
		//  MOD STAT
        foreach ($manager->getChildren($this->_item->id) as $item) {
            $assigned[$item->id] = $item->type == 1 ? ['role', $item->name] : ($item->id[0] == '/' ? ['route', $item->name] : ['permission', $item->name]);
            unset($available[$item->id]);
        }
        unset($available[$this->id]);
		//  MOD END
        return [
            'available' => $available,
            'assigned' => $assigned,
        ];
    }

    /**
     * Get item
     * @return Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get type name
     * @param  mixed $type
     * @return string|array
     */
    public static function getTypeName($type = null)
    {
        $result = [
            Item::TYPE_PERMISSION => 'Permission',
            Item::TYPE_ROLE => 'Role',
        ];
        if ($type === null) {
            return $result;
        }

        return $result[$type];
    }
}
