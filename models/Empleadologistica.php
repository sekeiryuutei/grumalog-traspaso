<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "empleadologistica".
 *
 * @property int $id
 * @property int $idEmpleado
 * @property int $idEstado
 *
 * @property Empleado $empleado
 * @property Programacionentregamercancia[] $programacionentregamercancias
 * @property Usertraspaso[] $usertraspasos
 */
class Empleadologistica extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empleadologistica';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idEmpleado', 'idEstado'], 'required'],
            [['idEmpleado', 'idEstado'], 'integer'],
            [['idEmpleado'], 'exist', 'skipOnError' => true, 'targetClass' => Empleado::class, 'targetAttribute' => ['idEmpleado' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idEmpleado' => 'Id Empleado',
            'idEstado' => 'Id Estado',
        ];
    }

    /**
     * Gets query for [[Empleado]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleado()
    {
        return $this->hasOne(Empleado::class, ['id' => 'idEmpleado']);
    }

    /**
     * Gets query for [[Programacionentregamercancias]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramacionentregamercancias()
    {
        return $this->hasMany(Programacionentregamercancia::class, ['idEmpleadoLogistica' => 'id']);
    }

    /**
     * Gets query for [[Userconteos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsertraspasos()
    {
        return $this->hasMany(Usertraspaso::class, ['idEmpleadoLogistica' => 'id']);
    }
}
