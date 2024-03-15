<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "traspasodetalle".
 *
 * @property int $id
 * @property int $idTraspaso
 * @property int $idItem
 * @property int $cantidad
 *
 * @property Item $item
 * @property Traspaso $traspaso
 */
class Traspasodetalle extends \yii\db\ActiveRecord
{
    public $bodegaorigen;
    public $bodegadestino;
    public $numerocajas;
    public $codigoitem;
    public $count;
    public $ultimo_codigo;
    public $cantidad_paquetes;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'traspasodetalle';
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('GETDATE()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
                'value' => function ($event) {
                    return Yii::$app->user->id;
                },
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        // return [
        //     [['idTraspaso', 'codigoitem','idItem', ], 'required', 'message' => '{attribute} Es Un Valor Obligatorio'],
        //     [['cantidad'], 'integer'],
        //     [['idTraspaso', 'idItem'], 'string', 'max' => 50],
        //     [['idTraspaso'], 'exist', 'skipOnError' => true, 'targetClass' => Traspaso::class, 'targetAttribute' => ['idTraspaso' => 'id']],
        //     [['idItem'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['idItem' => 'id']],
        // ];
        return [
            [['idTraspaso', 'idItem', 'codigoitem'], 'required', 'message' => '{attribute} Es Un Valor Obligatorio'],
            [['idTraspaso', 'cantidad', 'created_by', 'updated_by', 'idItem'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['idTraspaso'], 'exist', 'skipOnError' => true, 'targetClass' => Traspaso::class, 'targetAttribute' => ['idTraspaso' => 'id']],
            [['idItem'], 'exist', 'skipOnError' => true, 'targetClass' => Item::class, 'targetAttribute' => ['idItem' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'idTraspaso' => 'Id traspaso',
            'idItem' => 'Item',
            'cantidad' => 'Cantidad',
            'codigoitem' => 'Codigo EAN',
            'total' => 'Cantidad total',
            'bodegaorigen' => 'Bodega origen',
            'bodegadestino' => 'Bodega destino',
            'total' => 'Cantidad total',
            'count' => 'Total EAN',
            'updated_at' => 'Fecha',
            'ultimo_codigo' => 'ultimo codigo',
            'cantidad_paquetes' => 'paquetes',
        ];
    }

    /**
     * Gets query for [[Item]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItem()
    {
        return $this->hasOne(Item::class, ['id' => 'idItem']);
    }
    public function getItems()
    {
        return $this->hasMany(Item::class, ['id' => 'idItem']);
    }

    public function getCodigoitem()
    {
        return $this->hasOne(Item::class, ['item' => 'codigoitem']);
    }

    public function getTraspasodetalle()
    {
        return $this->hasOne(Traspasodetalle::class, ['idTraspaso' => 'idTraspaso']);
    }
    public function getTraspasodetalles()
    {
        return $this->hasMany(Traspasodetalle::class, ['idTraspaso' => 'idTraspaso']);
    }
    public function getFindCount($idTraspaso)
    {
        return $this->find()->where(['idTraspaso' => $idTraspaso])->count();
    }
    public function getCantidadPaquetes()
    {
        return $this->getItems()->where(['unidadOrden' => null])->count();
    }
    /**
     * Gets query for [[Traspaso]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTraspaso()
    {
        return $this->hasOne(Traspaso::class, ['id' => 'idTraspaso']);
    }

}
