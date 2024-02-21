<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "traspaso".
 *
 * @property int $id
 * @property int $idBodegaOrigen
 * @property int $idBodegaDestino
 * @property int $numeroCajas
 * @property int|null $idTipoDocumento
 * @property float|null $consecutivo
 *
 * 
 * @property Bodegas $bodegaDestino
 * @property Bodegas $bodegaOrigen
 * @property Traspasodetalle[] $traspasodetalles
 */
class Traspaso extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'traspaso';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idBodegaOrigen', 'idBodegaDestino'], 'required'],
            [['idBodegaOrigen', 'idBodegaDestino', 'numeroCajas', 'idTipoDocumento'], 'integer'],
            [['consecutivo'], 'number'],
            [['idBodegaDestino'], 'exist', 'skipOnError' => true, 'targetClass' => Bodegas::class, 'targetAttribute' => ['idBodegaDestino' => 'id']],
            [['idBodegaOrigen'], 'exist', 'skipOnError' => true, 'targetClass' => Bodegas::class, 'targetAttribute' => ['idBodegaOrigen' => 'id']],
            [['idTipoDocumento'], 'exist', 'skipOnError' => true, 'targetClass' => Tipodocumento::class, 'targetAttribute' => ['idTipoDocumento' => 'id']],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'idBodegaOrigen' => 'Bodega Origen',
            'idBodegaDestino' => 'Bodega Destino',
            'numeroCajas' => 'Número Cajas',
            'serie' => 'Serie',
            'consecutivo' => 'Consecutivo',
        ];
    }

    /**
     * Gets query for [[BodegaDestino]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBodegaDestino()
    {
        return $this->hasOne(Bodegas::class, ['id' => 'idBodegaDestino']);
    }

    /**
     * Gets query for [[BodegaOrigen]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBodegaOrigen()
    {
        return $this->hasOne(Bodegas::class, ['id' => 'idBodegaOrigen']);
    }

    /**
     * Gets query for [[Traspasodetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTraspasodetalles()
    {
        return $this->hasMany(Traspasodetalle::class, ['idTraspaso' => 'id']);
    }
}
