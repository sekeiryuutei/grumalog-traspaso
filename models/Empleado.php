<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "empleado".
 *
 * @property int $id
 * @property float $identificacion
 * @property string $nombreEmpleado
 * @property int|null $ndc
 * @property int|null $idEstado
 * @property int|null $idCargo
 * @property int|null $idCO
 * @property int|null $idCC
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property Empleadologistica[] $empleadologisticas
 * @property Horasextras[] $horasextras
 * @property Centrocostos $idCC0
 * @property Centrooperacion $idCO0
 * @property Cargo $idCargo0
 */
class Empleado extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'empleado';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['identificacion', 'nombreEmpleado'], 'required'],
            [['identificacion'], 'number'],
            [['ndc', 'idEstado', 'idCargo', 'idCO', 'idCC', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['nombreEmpleado'], 'string', 'max' => 150],
            [['idCargo'], 'exist', 'skipOnError' => true, 'targetClass' => Cargo::class, 'targetAttribute' => ['idCargo' => 'id']],
            [['idCO'], 'exist', 'skipOnError' => true, 'targetClass' => Centrooperacion::class, 'targetAttribute' => ['idCO' => 'id']],
            [['idCC'], 'exist', 'skipOnError' => true, 'targetClass' => Centrocostos::class, 'targetAttribute' => ['idCC' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identificacion' => 'Identificacion',
            'nombreEmpleado' => 'Nombre Empleado',
            'ndc' => 'Ndc',
            'idEstado' => 'Id Estado',
            'idCargo' => 'Id Cargo',
            'idCO' => 'Id Co',
            'idCC' => 'Id Cc',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Empleadologisticas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmpleadologisticas()
    {
        return $this->hasMany(Empleadologistica::class, ['idEmpleado' => 'id']);
    }

    /**
     * Gets query for [[Horasextras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHorasextras()
    {
        return $this->hasMany(Horasextras::class, ['idEmpleado' => 'id']);
    }

    /**
     * Gets query for [[IdCC0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdCC0()
    {
        return $this->hasOne(Centrocostos::class, ['id' => 'idCC']);
    }

    /**
     * Gets query for [[IdCO0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdCO0()
    {
        return $this->hasOne(Centrooperacion::class, ['id' => 'idCO']);
    }

    /**
     * Gets query for [[IdCargo0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdCargo0()
    {
        return $this->hasOne(Cargo::class, ['id' => 'idCargo']);
    }
}
