<?php

namespace luya\gallery\frontend\controllers;

use luya\gallery\models\Album;
use luya\web\Controller;

/**
 * Get all collections or for a specificy categorie.
 *
 * @author Basil Suter <basil@nadar.io>
 */
class CollectionsController extends Controller
{
    /**
     * Get all collections.
     */
    public function actionData()
    {
        return $this->render('data', [
            'data' => Album::find()->orderBy(['is_highlight' => SORT_DESC, 'sort_index' => SORT_ASC])->all(),
        ]);
    }

    /**
     * Get all collections for a specfici categorie.
     *
     * @param int $catId
     *
     * @return string
     */
    public function actionDataByCategorie($catId)
    {
        return $this->render('data_by_categorie', [
            'data' => Album::find()->where(['cat_id' => $catId])->orderBy(['is_highlight' => SORT_DESC, 'sort_index' => SORT_ASC])->all(),
        ]);
    }
}
