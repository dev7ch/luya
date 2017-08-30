<?php

namespace luya\crawler\frontend\controllers;

use luya\crawler\models\Index;
use luya\crawler\models\Searchdata;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/**
 * Crawler Index Controller.
 *
 * Returns an {{\yii\data\ActiveDataProvider}} within $provider.
 *
 * @author Basil Suter <basil@nadar.io>
 */
class DefaultController extends \luya\web\Controller
{
    /**
     * Get search overview.
     *
     * The index action will return an active data provider object inside the $provider variable:
     *
     * ```php
     * foreach ($provider->models as $item) {
     *     var_dump($item);
     * }
     * ```
     *
     * @return string
     */
    public function actionIndex($query = null, $page = null)
    {
        $language = Yii::$app->composition->getKey('langShortCode');

        if (empty($query)) {
            $provider = new ArrayDataProvider();
        } else {
            $activeQuery = Index::activeQuerySearch($query, $language);

            $provider = new ActiveDataProvider([
                'query'      => $activeQuery,
                'pagination' => [
                    'defaultPageSize' => $this->module->searchResultPageSize,
                    'route'           => '/crawler/default',
                    'params'          => ['query' => $query, 'page' => $page],
                ],
            ]);

            $searchData = new Searchdata();
            $searchData->detachBehavior('LogBehavior');
            $searchData->attributes = [
                'query'     => $query,
                'results'   => $provider->totalCount,
                'timestamp' => time(),
                'language'  => $language,
            ];
            $searchData->save();
        }

        return $this->render('index', [
            'query'    => Html::encode($query),
            'provider' => $provider,
        ]);
    }
}
