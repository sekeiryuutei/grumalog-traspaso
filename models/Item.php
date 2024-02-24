<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "item".
 *
 * @property int $id
 * @property float $item
 * @property string $referencia
 * @property string $descripcion
 * @property int $idCategoria
 * @property int $idSubcategoria
 * @property int|null $idProducto
 * @property int|null $idMarca
 * @property int|null $idTalla
 * @property int|null $idColor
 * @property string|null $codigoProveedor
 * @property string|null $nombreProveedor
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int $updated_by
 *
 * @property Color $color
 * @property Marca $idMarca0
 * @property Producto $idProducto0
 * @property Talla $idTalla0
 */
class Item extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item';
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
        return [
            [['item', 'referencia', 'descripcion', 'idCategoria', 'idSubcategoria'], 'required'],
            [['item'], 'number'],
            [['idCategoria', 'idSubcategoria', 'idProducto', 'idMarca', 'idTalla', 'idColor', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['referencia'], 'string', 'max' => 50],
            [['descripcion', 'nombreProveedor'], 'string', 'max' => 150],
            [['codigoProveedor'], 'string', 'max' => 10],
            [['idTalla'], 'exist', 'skipOnError' => true, 'targetClass' => Talla::class, 'targetAttribute' => ['idTalla' => 'id']],
            [['idColor'], 'exist', 'skipOnError' => true, 'targetClass' => Color::class, 'targetAttribute' => ['idColor' => 'id']],
            [['idMarca'], 'exist', 'skipOnError' => true, 'targetClass' => Marca::class, 'targetAttribute' => ['idMarca' => 'id']],
            [['idProducto'], 'exist', 'skipOnError' => true, 'targetClass' => Producto::class, 'targetAttribute' => ['idProducto' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item' => 'Item',
            'referencia' => 'Referencia',
            'descripcion' => 'Descripcion',
            'idCategoria' => 'Id Categoria',
            'idSubcategoria' => 'Id Subcategoria',
            'idProducto' => 'Id Producto',
            'idMarca' => 'Id Marca',
            'idTalla' => 'Id Talla',
            'idColor' => 'Id Color',
            'codigoProveedor' => 'Codigo Proveedor',
            'nombreProveedor' => 'Nombre Proveedor',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[Color]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getColor()
    {
        return $this->hasOne(Color::class, ['id' => 'idColor']);
    }

    /**
     * Gets query for [[IdMarca0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdMarca0()
    {
        return $this->hasOne(Marca::class, ['id' => 'idMarca']);
    }

    /**
     * Gets query for [[IdProducto0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdProducto0()
    {
        return $this->hasOne(Producto::class, ['id' => 'idProducto']);
    }

    /**
     * Gets query for [[IdTalla0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdTalla0()
    {
        return $this->hasOne(Talla::class, ['id' => 'idTalla']);
    }
}
