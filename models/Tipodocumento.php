<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipodocumento".
 *
 * @property int $id
 * @property string|null $codigo
 * @property string|null $nombre
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property Ordendecompra[] $ordendecompras
 */
class Tipodocumento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipodocumento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['codigo'], 'string', 'max' => 5],
            [['nombre'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'nombre' => 'Nombre',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Ordendecompras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrdendecompras()
    {
        return $this->hasMany(Ordendecompra::class, ['idTipoDocumento' => 'id']);
    }
}
